<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Datapenghuni;
use App\Models\Tagihan;
use Carbon\Carbon;

class GenerateMonthlyTagihan extends Command
{
    protected $signature = 'tagihan:generate-monthly';
    protected $description = 'Generate monthly tagihan for all penghuni';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $penghuniList = Datapenghuni::with('datakamar')->where('status_hunian', 'Menghuni')->get();

        foreach ($penghuniList as $penghuni) {
            $tagihan = new Tagihan();
            $tagihan->id_penghuni = $penghuni->id;
            $tagihan->periode = Carbon::now()->format('F Y');
            $tagihan->tanggal_tagihan = Carbon::now();
            $tagihan->jatuh_tempo = Carbon::now()->addDays(7);
            $tagihan->jumlah_tagihan = $penghuni->datakamar->harga_bulanan;
            $tagihan->status_tagihan = 'Belum Lunas';
            $tagihan->save();
        }

        $this->info('Monthly tagihan generated successfully.');
    }
}
