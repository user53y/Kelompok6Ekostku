<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datapengeluaran extends Model
{
    use HasFactory;

    protected $table = 'datapengeluaran';

    protected $fillable = [
        'id_user',
        'id_jenis',
        'jumlah_pengeluaran',
        'tanggal_pengeluaran',
    ];

    public function jenisPengeluaran()
    {
        return $this->belongsTo(JenisPengeluaran::class, 'id_jenis')->withDefault([
            'nama_pengeluaran' => 'Data Jenis Terhapus'
        ]);
    }
}
