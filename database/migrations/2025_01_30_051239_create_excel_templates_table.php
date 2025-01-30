<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExcelTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('excel_templates', function (Blueprint $table) {
            $table->id(); // Kolom ID sebagai primary key
            $table->string('name'); // Nama template Excel
            $table->binary('file'); // Kolom untuk menyimpan file Excel dalam bentuk binary
            $table->timestamps(); // Kolom timestamp untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('excel_templates');
    }
}
