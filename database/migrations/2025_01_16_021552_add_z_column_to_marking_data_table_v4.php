<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZColumnToMarkingDataTableV4 extends Migration
{
    public function up()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            $table->string('z')->default('guarantee'); // Menambahkan kolom z dengan default 'guarantee'
        });
    }

    public function down()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            $table->dropColumn('z'); // Menghapus kolom z jika migrasi dibatalkan
        });
    }
}