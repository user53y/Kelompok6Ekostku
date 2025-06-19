<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datapemasukan extends Model
{
    protected $table = 'datapemasukan';

    protected $fillable = [
        'id_tagihan',
        'tanggal_pembayaran',
        'jumlah_pembayaran',
        'jenis_pembayaran',
        'bukti_pembayaran',
        'denda'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'jumlah_pembayaran' => 'decimal:2',
        'denda' => 'decimal:2'
    ];

    // Relationship with Tagihan model
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan');
    }
}
