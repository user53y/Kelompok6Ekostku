<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Datapemasukan;
use App\Models\Tagihan;
use App\Models\Datapenghuni;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    public function index()
    {
        $datapemasukan = Datapemasukan::with(['tagihan.penghuni.datakamar'])->get();
        return view('pemilik.datapemasukan.index', compact('datapemasukan'));
    }

    public function destroy($id)
    {
        $pemasukan = Datapemasukan::findOrFail($id);
        $pemasukan->delete();
        return redirect()->back()->with('success', 'Pemasukan berhasil dihapus.');
    }

    public function verifyPayment($id)
    {
        DB::beginTransaction();
        try {
            $pemasukan = Datapemasukan::with('tagihan.penghuni')->findOrFail($id);

            // Update payment status
            $pemasukan->status = 'lunas';
            $pemasukan->save();

            // Update tagihan status
            $tagihan = $pemasukan->tagihan;
            $tagihan->status_tagihan = 'Lunas';
            $tagihan->save();

            // Update penghuni status
            $penghuni = $tagihan->penghuni;
            $penghuni->status_pembayaran = 'Lunas';
            $penghuni->save();

            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memverifikasi pembayaran');
        }
    }
}
