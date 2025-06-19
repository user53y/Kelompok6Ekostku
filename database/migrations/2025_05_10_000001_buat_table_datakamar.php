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
        Schema::create('datakamar', function (Blueprint $table) {
            $table->id();
            $table->string('no_kamar');
            $table->string('tipe');
            $table->string('luas');
            $table->integer('lantai');
            $table->integer('kapasitas');
            $table->decimal('harga_bulanan', 10, 2);
            $table->string('status')->default('Tersedia');
            $table->string('gambar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datakamar');
    }
};
