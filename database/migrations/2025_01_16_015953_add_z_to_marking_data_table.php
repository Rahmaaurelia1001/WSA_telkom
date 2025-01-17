<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZColumnToMarkingDataTableV3 extends Migration
{
    public function up()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            $table->string('z')->default('non guarantee');
        });
    }

    public function down()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            $table->dropColumn('z');
        });
    }
}
