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
        Schema::table('datapenghuni', function (Blueprint $table) {
            if (!Schema::hasColumn('datapenghuni', 'foto_ktp')) {
                $table->string('foto_ktp')->nullable()->after('pekerjaan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('datapenghuni', function (Blueprint $table) {
            $table->dropColumn('foto_ktp');
        });
    }
};
