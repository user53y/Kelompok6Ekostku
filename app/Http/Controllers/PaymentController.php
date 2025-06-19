<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Datapemasukan;
use App\Models\Datapenghuni;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $tagihanService;

    public function __construct(TagihanService $tagihanService)
    {
        $this->tagihanService = $tagihanService;
    }

    public function check()
    {
        $penghuni = auth()->user()->datapenghuni;
        $tagihan = Tagihan::where('id_penghuni', $penghuni->id)
                         ->where('status_tagihan', 'Belum Lunas')
                         ->get();

        return view('penghuni.payment.check', compact('tagihan', 'penghuni'));
    }

    public function process(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $tagihan = Tagihan::findOrFail($id);
            $denda = $this->tagihanService->calculateDenda($tagihan);
            $totalPembayaran = $tagihan->jumlah_tagihan + $denda;

            // Handle bukti pembayaran upload
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/payments'), $filename);
            }

            // Create pembayaran record
            Datapemasukan::create([
                'id_tagihan' => $tagihan->id,
                'tanggal_pembayaran' => now(),
                'jumlah_pembayaran' => $totalPembayaran,
                'bukti_pembayaran' => $filename ?? null,
                'status' => 'pending',
                'denda' => $denda
            ]);

            // Update status tagihan
            $tagihan->penghuni->update(['status_pembayaran' => 'Menunggu Konfirmasi']);

            DB::commit();
            return redirect()->route('payment.check')
                           ->with('success', 'Pembayaran berhasil diproses dan menunggu konfirmasi');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $penghuni = auth()->user()->datapenghuni;
        $history = Tagihan::where('id_penghuni', $penghuni->id)
                         ->with('datapemasukan')
                         ->orderBy('created_at', 'desc')
                         ->get();

        return view('penghuni.payment.history', compact('history'));
    }
}
