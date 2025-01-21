<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMarkingDiamondTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel marking_36_jam_non_hvc
        Schema::create('marking_diamond', function (Blueprint $table) {
            $table->id(); // ID kolom utama
            $table->integer('Max');  // Kolom Max
            $table->string('description');  // Kolom Description
            $table->timestamps();  // Kolom created_at dan updated_at
        });

        // Memasukkan data dengan nilai Max yang sesuai
        DB::table('marking_diamond')->insert([
            ['Max' => 1, 'description' => '<= 1 JAM'],
            ['Max' => 2, 'description' => '1-2 JAM'],
            ['Max' => 3, 'description' => '2-3 JAM'],
            ['Max' => 4, 'description' => '>3 JAM'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus tabel marking_36_jam_non_hvc jika migrasi dibatalkan
        Schema::dropIfExists('marking_diamond');
    }
}
