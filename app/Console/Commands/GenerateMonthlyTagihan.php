<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Datapenghuni;
use App\Models\Tagihan;
use Carbon\Carbon;

class GenerateMonthlyTagihan extends Command
{
    protected $signature = 'tagihan:generate-monthly';
    protected $description = 'Generate monthly tagihan for all active tenants';

    public function handle()
    {
        $now = Carbon::now();
        $periode = $now->format('F Y');

        $penghuniList = Datapenghuni::where('status_hunian', 'Menghuni')->get();
        $created = 0;

        foreach ($penghuniList as $penghuni) {
            // Cek apakah sudah ada tagihan bulan ini
            $exists = Tagihan::where('id_penghuni', $penghuni->id)
                ->where('periode', $periode)
                ->exists();

            if (!$exists) {
                Tagihan::create([
                    'id_penghuni' => $penghuni->id,
                    'periode' => $periode,
                    'tanggal_tagihan' => $now,
                    'jatuh_tempo' => $now->copy()->addDays(7),
                    'jumlah_tagihan' => $penghuni->datakamar->harga_bulanan ?? 0,
                    'status_tagihan' => 'Belum Lunas',
                    'denda' => 0,
                    'tanggal_masuk' => $penghuni->tanggal_masuk ?? $now, // tambahkan baris ini
                ]);
                $created++;
            }
        }

        $this->info("Tagihan bulanan berhasil dibuat untuk {$created} penghuni.");
    }
}
