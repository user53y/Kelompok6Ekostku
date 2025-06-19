<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuktiPembayaranToTagihanTable extends Migration
{
    public function up()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('status_tagihan');
        });
    }

    public function down()
    {
        Schema::table('tagihan', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });
    }
};
