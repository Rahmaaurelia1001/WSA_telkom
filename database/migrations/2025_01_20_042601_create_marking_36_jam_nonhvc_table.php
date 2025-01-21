<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMarking36JamNonHvcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel marking_36_jam_non_hvc
        Schema::create('marking_36_jam_nonhvc', function (Blueprint $table) {
            $table->id(); // ID kolom utama
            $table->integer('Max');  // Kolom Max
            $table->integer('value');  // Kolom Value
            $table->string('description');  // Kolom Description
            $table->integer('max2')->nullable();  // Kolom Max2 baru
            $table->string('description2')->nullable();  // Kolom Description2 baru
            $table->timestamps();  // Kolom created_at dan updated_at
        });

        // Memasukkan data dengan nilai Max yang sesuai
        DB::table('marking_36_jam_nonhvc')->insert([
            ['Max' => 3, 'value' => 36, 'description' => '<= 3 JAM', 'max2' => 3, 'description2' => '<= 3 JAM'],
            ['Max' => 6, 'value' => 36, 'description' => '3-6 JAM', 'max2' => 6, 'description2' => '3-6 JAM'],
            ['Max' => 12, 'value' => 36, 'description' => '6-12 JAM', 'max2' => 12, 'description2' => '6-12 JAM'],
            ['Max' => 24, 'value' => 36, 'description' => '12-24 JAM', 'max2' => 24, 'description2' => '12-24 JAM'],
            ['Max' => 36, 'value' => 36, 'description' => '24-36 JAM', 'max2' => 36, 'description2' => '24-36 JAM'],
            ['Max' => 48, 'value' => 36, 'description' => '36-48 JAM', 'max2' => 48, 'description2' => '36-48 JAM'],
            ['Max' => 72, 'value' => 36, 'description' => '48-72 JAM', 'max2' => 72, 'description2' => '48-72 JAM'],
            ['Max' => 999, 'value' => 36, 'description' => '>= 72 JAM', 'max2' => 999, 'description2' => '>= 72 JAM'],
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
        Schema::dropIfExists('marking_36_jam_nonhvc');
    }
}
