<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('excel_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->json('merged_data')->nullable();
            $table->json('processed_data')->nullable();
            $table->timestamp('downloaded_at');
            $table->string('downloaded_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('excel_downloads');
    }
};