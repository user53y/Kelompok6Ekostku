<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penghuni')->constrained('datapenghuni')->onDelete('cascade');
            $table->date('tanggal_masuk'); // tanggal awal penghuni masuk
            $table->date('tanggal_keluar')->nullable(); // kosong jika masih tinggal
            $table->string('periode'); // misalnya 'Mei 2025' atau '2025-05'

            $table->date('tanggal_tagihan'); // tanggal tagihan dibuat
            $table->date('jatuh_tempo'); // otomatis: tanggal_tagihan + 30 hari

            $table->decimal('jumlah_tagihan', 10, 2); // contoh: 750000.00
            $table->string('status_tagihan')->default('Belum Lunas'); // Lunas / Belum Lunas / Jatuh Tempo

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
