<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'id_penghuni',
        'tanggal_masuk',
        'tanggal_keluar',
        'periode',
        'tanggal_tagihan',
        'jatuh_tempo',
        'jumlah_tagihan',
        'status_tagihan'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'tanggal_tagihan' => 'date',
        'jatuh_tempo' => 'date'
    ];

    public function penghuni()
    {
        return $this->belongsTo(Datapenghuni::class, 'id_penghuni');
    }

    public function datapemasukan()
    {
        return $this->hasOne(Datapemasukan::class, 'id_tagihan');
    }

    public function calculateDenda()
    {
        if ($this->status_tagihan === 'Lunas') {
            return 0;
        }

        // If current date is after jatuh_tempo
        if (Carbon::now()->gt($this->jatuh_tempo)) {
            $daysLate = Carbon::now()->diffInDays($this->jatuh_tempo);
            // 1% denda per hari keterlambatan
            return ($this->jumlah_tagihan * 0.01) * $daysLate;
        }

        return 0;
    }
}
