<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datapenghuni extends Model
{
    protected $table = 'datapenghuni';

    protected $fillable = [
        'id_user',
        'id_datakamar',
        'nama_lengkap',
        'nik',
        'alamat',
        'no_telepon',
        'pekerjaan',
        'foto_ktp',
        'tanggal_masuk',
        'status_hunian',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
    ];

    // Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relationship with Datakamar model
    public function datakamar()
    {
        return $this->belongsTo(Datakamar::class, 'id_datakamar');
    }

    // Add tagihan relationship
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_penghuni');
    }

    public function riwayat()
    {
        // Jika riwayat ada di tabel yang sama (datapenghuni), ambil semua record user ini, urutkan terbaru dulu
        return $this->hasMany(self::class, 'id_user', 'id_user')->orderByDesc('created_at');
    }
}
