<?php

namespace App\Http\Controllers;

use App\Models\Datapenghuni;
use App\Models\DataKamar;
use App\Models\Tagihan;
use App\Models\Datapemasukan;
use App\Models\User;
use App\Notifications\PembayaranPendingNotification;
use App\Notifications\PengajuanBerhentiNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KontrakController extends Controller
{
    public function perpanjangSewa(Request $request)
    {
        $request->validate(rules: [
            'penghuni_id' => 'required|exists:datapenghuni,id',
            'durasi' => 'required|integer|min:1|max:12',
        ]);

        DB::beginTransaction();

        $penghuni = Datapenghuni::with('datakamar')->findOrFail($request->penghuni_id);

        if ($penghuni->status_pembayaran !== 'Lunas') {
            abort(400, 'Silakan lunasi pembayaran sebelumnya terlebih dahulu.');
        }

        $durasi = (int)$request->durasi;
        $currentEnd = Carbon::parse($penghuni->tanggal_keluar);
        $periodeStart = Carbon::parse($penghuni->periode_mulai);
        $newEnd = $currentEnd->copy()->addMonths($durasi);
        $newPeriode = $periodeStart->copy()->addMonths($durasi)->format('F Y');

        $total_payment = $penghuni->datakamar->harga_bulanan * $durasi;

        Datapemasukan::create([
            'penghuni_id' => $penghuni->id,
            'jumlah' => $total_payment,
            'tanggal_pembayaran' => now(),
            'status' => 'pending',
            'keterangan' => "Perpanjangan sewa {$durasi} bulan"
        ]);

        $penghuni->update([
            'tanggal_keluar' => $newEnd,
            'periode_mulai' => $newPeriode,
            'status_pembayaran' => 'Menunggu Konfirmasi'
        ]);

        $pemilik = User::where('role', 'pemilik')->first();
        if ($pemilik) {
            $pemilik->notify(new PembayaranPendingNotification($penghuni));
        }

        DB::commit();

        return redirect()->route('cek-pembayaran')
            ->with('success', "Pengajuan perpanjangan sewa berhasil. Total tagihan: Rp " . number_format($total_payment, 0, ',', '.'));
    }

    // Upload bukti pembayaran oleh penghuni
    public function uploadBuktiPembayaran(Request $request, $penghuni_id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $penghuni = Datapenghuni::with('tagihan')->findOrFail($penghuni_id);
            $tagihan = $penghuni->tagihan()->where('status_tagihan', 'Belum Lunas')->latest()->first();

            if (!$tagihan) {
                return back()->with('error', 'Tidak ada tagihan aktif.');
            }

            // Simpan file
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/payments'), $filename);

            // Simpan ke datapemasukan
            $pemasukan = \App\Models\Datapemasukan::create([
                'id_tagihan' => $tagihan->id,
                'tanggal_pembayaran' => now(),
                'jumlah_pembayaran' => $tagihan->jumlah_tagihan,
                'bukti_pembayaran' => $filename,
                'status' => 'pending',
                'denda' => 0
            ]);

            // Update status tagihan & penghuni
            $tagihan->update(['status_tagihan' => 'Menunggu Konfirmasi']);
            $penghuni->update(['status_pembayaran' => 'Menunggu Konfirmasi']);

            // Notifikasi ke pemilik
            $pemilik = User::where('role', 'pemilik')->first();
            if ($pemilik) {
                $pemilik->notify(new PembayaranPendingNotification($penghuni));
            }

            DB::commit();
            return back()->with('success', 'Bukti pembayaran berhasil diupload, menunggu konfirmasi pemilik.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal upload bukti pembayaran: ' . $e->getMessage());
        }
    }

    // Pengajuan pemberhentian sewa oleh penghuni
    public function ajukanBerhenti($penghuni_id)
    {
        $penghuni = Datapenghuni::findOrFail($penghuni_id);

        // Validasi tanggal sebelum 25
        if (now()->day >= 25) {
            return back()->with('error', 'Pengajuan pemberhentian hanya bisa sebelum tanggal 25.');
        }

        // Simpan pengajuan berhenti, status pending
        $penghuni->update(['status_hunian' => 'Menunggu Persetujuan Berhenti']);

        // Notifikasi ke pemilik
        $pemilik = User::where('role', 'pemilik')->first();
        if ($pemilik) {
            $pemilik->notify(new PengajuanBerhentiNotification($penghuni));
        }

        return back()->with('success', 'Pengajuan pemberhentian berhasil, menunggu persetujuan pemilik.');
    }

    // Pengajuan pemberhentian sewa oleh penghuni (tanpa parameter URL, ambil dari request)
    public function ajukanBerhentiViaRequest(Request $request)
    {
        $request->validate([
            'penghuni_id' => 'required|exists:datapenghuni,id'
        ]);
        return $this->ajukanBerhenti($request->penghuni_id);
    }

    public function berhentikan($id)
    {
        DB::beginTransaction();
        try {
            $tagihan = Tagihan::with('penghuni.datakamar')->findOrFail($id);
            $penghuni = $tagihan->penghuni;

            if (!$penghuni) {
                throw new \Exception('Data penghuni tidak ditemukan');
            }

            if ($tagihan->status_tagihan !== 'Lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Harap lunasi tagihan terlebih dahulu.'
                ], 400);
            }

            // Update status kamar menjadi Tersedia
            if ($penghuni->datakamar) {
                $penghuni->datakamar->update(['status' => 'Tersedia']);
            }

            // Simpan data pemasukan terakhir sebagai histori pemberhentian
            $pemasukan = Datapemasukan::where('id_tagihan', $tagihan->id)->first();
            if (!$pemasukan) {
                Datapemasukan::create([
                    'id_tagihan' => $tagihan->id,
                    'tanggal_pembayaran' => now(),
                    'jumlah_pembayaran' => $tagihan->jumlah_tagihan,
                    'jenis_pembayaran' => 'manual',
                    'bukti_pembayaran' => null,
                    'denda' => 0
                ]);
            }

            // Update status tagihan menjadi Berhenti (tidak dihapus)
            $tagihan->update([
                'status_tagihan' => 'Berhenti'
            ]);

            // Archive/Update status penghuni (tidak dihapus)
            $penghuni->update([
                'status_hunian' => 'Tidak Menghuni',
                'tanggal_keluar' => now(),
                'status_pembayaran' => 'Lunas'
            ]);

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memberhentikan sewa: ' . $e->getMessage()
            ], 500);
        }
    }
}
