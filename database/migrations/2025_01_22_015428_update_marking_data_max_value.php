<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('marking_data', function (Blueprint $table) {
        $table->integer('max_value')->nullable()->change();
    });
}

public function down()
{
    Schema::table('marking_data', function (Blueprint $table) {
        $table->integer('max_value')->nullable(false)->change();
    });
}

};
