<?php

namespace App\Services;

use Carbon\Carbon;

class TagihanService
{
    public function hitungTagihanBulanan($tanggalMasuk, $tanggalKeluar, $hargaBulanan, $periode)
    {
        $periodeDate = Carbon::createFromFormat('F Y', $periode);
        $jumlahHariDalamBulan = $periodeDate->daysInMonth;

        // Debugging: pastikan input benar (hapus/comment setelah yakin)
        // \Log::info('DEBUG PRORATA', [
        //     'tanggalMasuk' => $tanggalMasuk ? $tanggalMasuk->toDateString() : null,
        //     'tanggalKeluar' => $tanggalKeluar ? $tanggalKeluar->toDateString() : null,
        //     'periode' => $periode,
        //     'jumlahHariDalamBulan' => $jumlahHariDalamBulan,
        //     'hargaBulanan' => $hargaBulanan,
        // ]);

        // Jika tanggal masuk dan keluar di bulan yang sama dengan periode (prorata di tengah)
        if ($tanggalMasuk && $tanggalKeluar &&
            $tanggalMasuk->format('Y-m') === $periodeDate->format('Y-m') &&
            $tanggalKeluar->format('Y-m') === $periodeDate->format('Y-m')) {
            $durasiSewa = $tanggalKeluar->diffInDays($tanggalMasuk) + 1;
            $biayaProrata = ($durasiSewa / $jumlahHariDalamBulan) * $hargaBulanan;
            return round($biayaProrata);
        }

        // Jika tanggal masuk di bulan yang sama dengan periode (prorata awal)
        if ($tanggalMasuk && $tanggalMasuk->format('Y-m') === $periodeDate->format('Y-m')) {
            $durasiSewa = $jumlahHariDalamBulan - ($tanggalMasuk->day - 1);
            $biayaProrata = ($durasiSewa / $jumlahHariDalamBulan) * $hargaBulanan;
            return round($biayaProrata);
        }

        // Jika tanggal keluar di bulan yang sama dengan periode (prorata akhir)
        if ($tanggalKeluar && $tanggalKeluar->format('Y-m') === $periodeDate->format('Y-m')) {
            $durasiSewa = $tanggalKeluar->day;
            $biayaProrata = ($durasiSewa / $jumlahHariDalamBulan) * $hargaBulanan;
            return round($biayaProrata);
        }

        // Jika sewa penuh bulan (di antara tanggal masuk dan keluar)
        return $hargaBulanan;
    }

    public function calculateDenda($tagihan)
    {
        if ($tagihan->status_tagihan === 'Lunas') {
            return 0;
        }

        $tanggalMasuk = Carbon::parse($tagihan->tanggal_masuk);
        $tanggalBayar = Carbon::now();

        // Hitung batas akhir berdasarkan tanggal di bulan berikutnya + 7 hari
        $batasDenda = $tanggalMasuk->copy()->addMonthNoOverflow()->addDays(7);

        // Cek apakah kena denda (jika sudah lewat batas)
        if ($tanggalBayar->gt($batasDenda)) {
            return 5000;
        }

        return 0;
    }
}
