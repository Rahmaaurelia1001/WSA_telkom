<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMax2AndDescription2ToMarking36JamNonhvcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marking_36_jam_nonhvc', function (Blueprint $table) {
            $table->integer('max2')->nullable()->after('description'); // Ganti 'existing_column' dengan kolom sebelumnya
            $table->string('description2')->nullable()->after('max2');
        });

        // Insert contoh data ke dalam tabel
        DB::table('marking_36_jam_nonhvc')->insert([
            ['max2' => 12, 'description2' => '>= 12 JAM'],
            ['max2' => 9, 'description2' => '9-12 JAM'],
            ['max2' => 6, 'description2' => '6-9 JAM'],
            ['max2' => 3, 'description2' => '3-6 JAM'],
            ['max2' => 0, 'description2' => '<= 3 JAM'],
            ['max2' => -1, 'description2' => 'Overdue'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marking_36_jam_nonhvc', function (Blueprint $table) {
            $table->dropColumn(['max2', 'description2']);
        });
    }
}
