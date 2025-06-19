<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Datapenghuni;
use App\Notifications\PaymentNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\PaymentApprovalNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PembayaranPendingNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Datapemasukan;
use App\Models\Datakamar;
use App\Models\Tagihan;
use Carbon\Carbon;
use App\Services\TagihanService; // pastikan service ini ada

class PenghuniController
{
    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function cekPembayaran()
    {
        $penghuni = Datapenghuni::where('id_user', Auth::id())
                               ->with(['datakamar', 'user', 'tagihan' => function($query) {
                                   $query->latest();
                               }])
                               ->first();

        // Jika penghuni baru saja memesan (ada success message), jangan redirect
        if (!$penghuni && !session('success')) {
            return redirect()->route('kamar-tersedia')
                            ->with('error', 'Anda belum memiliki kamar yang dipesan');
        }

        // Tambahkan data tagihan ke view
        $tagihan = $penghuni ? $penghuni->tagihan->first() : null;
        return view('penghuni.pembayaran', compact('penghuni', 'tagihan'));
    }

    public function uploadPembayaran(Request $request, $id)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $penghuni = Datapenghuni::findOrFail($id);

            // Get active tagihan
            $tagihan = Tagihan::where('id_penghuni', $penghuni->id)
                ->where('status_tagihan', 'Belum Lunas')
                ->latest()
                ->first();

            if (!$tagihan) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Tidak ada tagihan aktif untuk pembayaran.');
            }

            // Upload file
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/payments'), $filename);

            // Simpan bukti ke tagihan
            $tagihan->status_tagihan = 'Menunggu Konfirmasi';
            $tagihan->bukti_pembayaran = $filename;
            $tagihan->save();

            // Notifikasi ke pemilik
            $pemilik = User::where('role', 'pemilik')->get();
            foreach ($pemilik as $owner) {
                $owner->notify(new PembayaranPendingNotification($penghuni));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi dari pemilik.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Upload Pembayaran Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal mengupload bukti pembayaran: ' . $e->getMessage()]);
        }
    }


    public function pesanKamar($id)
    {
        DB::beginTransaction();
        try {
            $kamar = Datakamar::lockForUpdate()->findOrFail($id);

            // Check if room is available
            if ($kamar->status !== 'Tersedia') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Kamar tidak tersedia untuk dipesan');
            }

            $user = Auth::user();

            // Buat data penghuni
            $penghuni = Datapenghuni::create([
                'id_user' => $user->id,
                'id_datakamar' => $kamar->id,
                'tanggal_masuk' => now(),
                'status_hunian' => 'Menghuni',
                'status_pembayaran' => 'Belum Bayar'
            ]);

            // Pastikan data penghuni berhasil dibuat
            if (!$penghuni) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal membuat data penghuni.');
            }

            // Buat tagihan untuk penghuni baru
            $tagihan = Tagihan::create([
                'id_penghuni' => $penghuni->id,
                'jumlah_tagihan' => $kamar->harga ?? 0,
                'status_tagihan' => 'Belum Lunas',
                'tanggal_tagihan' => now(),
                // tambahkan field lain jika diperlukan
            ]);

            if (!$tagihan) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Gagal membuat tagihan.');
            }

            // Update status kamar
            $kamar->status = 'Pending';
            $kamar->save();

            DB::commit();
            return redirect()->route('cek-pembayaran')
                ->with('success', 'Kamar berhasil dipesan. Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memesan kamar: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        \Log::info('PenghuniController@store - Mulai proses', [
            'request' => $request->all(),
            'files' => $request->allFiles()
        ]);

        DB::beginTransaction();
        try {
            // Validasi input (hapus tanggal_masuk)
            $validated = $request->validate([
                'id_user' => 'required|exists:users,id',
                'id_datakamar' => 'required|exists:datakamar,id',
                'nama_lengkap' => 'required|string|max:255',
                'nik' => 'required|string|max:16',
                'alamat' => 'required|string',
                'no_telepon' => 'required|string|max:15',
                'pekerjaan' => 'required|string|max:100',
                'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'status_hunian' => 'required|in:Menghuni,Tidak Menghuni'
            ]);

            // Cek apakah user sudah punya kamar aktif
            $sudahAda = Datapenghuni::where('id_user', $validated['id_user'])
                ->whereIn('status_hunian', ['Menghuni', 'Pending'])
                ->exists();
            if ($sudahAda) {
                throw new \Exception('Anda sudah memesan/menghuni kamar. Tidak dapat memesan lebih dari satu kamar.');
            }

            // Cek status kamar sebelum proses lebih lanjut
            $kamar = Datakamar::lockForUpdate()->find($validated['id_datakamar']);
            if (!$kamar) {
                throw new \Exception('Kamar tidak ditemukan');
            }
            if ($kamar->status !== 'Tersedia') {
                \Log::warning('PenghuniController@store - Kamar tidak tersedia', ['kamar_id' => $kamar->id, 'status' => $kamar->status]);
                throw new \Exception('Kamar tidak tersedia untuk dipesan');
            }

            // Upload foto KTP
            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/ktp'), $filename);
                $validated['foto_ktp'] = $filename;
            }

            // Set tanggal_masuk otomatis hari ini
            $validated['tanggal_masuk'] = now();

            // Buat data penghuni
            $penghuni = Datapenghuni::create($validated);
            if (!$penghuni) {
                throw new \Exception('Gagal menyimpan data penghuni');
            }
            \Log::info('PenghuniController@store - Penghuni berhasil dibuat', ['penghuni_id' => $penghuni->id]);

            // Hitung tagihan prorata via service
            $periodeDate = Carbon::now();
            $periodeLengkap = $periodeDate->format('F Y');
            $jumlahTagihan = $this->tagihanService->hitungTagihanBulanan(
                Carbon::parse($penghuni->tanggal_masuk),
                null,
                $kamar->harga_bulanan ?? $kamar->harga ?? 0,
                $periodeLengkap
            );

            // Buat tagihan
            $tagihan = Tagihan::create([
                'id_penghuni' => $penghuni->id,
                'tanggal_masuk' => $penghuni->tanggal_masuk,
                'periode' => $periodeLengkap,
                'tanggal_tagihan' => now(),
                'jatuh_tempo' => now()->addDays(37),
                'jumlah_tagihan' => $jumlahTagihan,
                'status_tagihan' => 'Belum Lunas'
            ]);
            if (!$tagihan) {
                throw new \Exception('Gagal membuat tagihan');
            }
            \Log::info('PenghuniController@store - Tagihan berhasil dibuat', ['tagihan_id' => $tagihan->id]);

            // Update status kamar setelah semua proses sukses
            $kamar->status = 'Pending';
            $kamar->save();

            DB::commit();
            \Log::info('PenghuniController@store - Transaksi sukses');

            return redirect()->route('cek-pembayaran')
                ->with('success', 'Kamar berhasil dipesan. Silakan lakukan pembayaran sekarang.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('PenghuniController@store - Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Gagal memesan kamar: ' . $e->getMessage());
        }
    }

    public function create($id)
    {
        $kamar = Datakamar::findOrFail($id);
        return view('penghuni.form-booking', compact('kamar'));
    }
}
