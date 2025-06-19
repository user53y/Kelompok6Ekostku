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
        Schema::create('datapenghuni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_datakamar')->constrained('datakamar')->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('nik')->unique();
            $table->string('alamat');
            $table->string('no_telepon');
            $table->string('pekerjaan');
            $table->string('foto_ktp')->nullable();
            $table->date('tanggal_masuk');
            $table->string('status_hunian')->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datapenghuni');
    }
};
