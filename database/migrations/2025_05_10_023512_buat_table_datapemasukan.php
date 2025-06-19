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
        Schema::create('datapemasukan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tagihan')->constrained('tagihan')->onDelete('cascade');
            $table->date('tanggal_pembayaran');
            $table->decimal('jumlah_pembayaran', 12, 2);
            $table->string('jenis_pembayaran');
            $table->string('bukti_pembayaran')->nullable();
            $table->decimal('denda', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datapemasukan');
    }
};
