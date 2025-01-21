<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTtrDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table
        Schema::create('ttr_data', function (Blueprint $table) {
            $table->id();
            $table->string('close_bound');  // Stores values like 'TTR 3 Jam Manja', 'TTR Diamond', etc.
            $table->integer('jam_value');   // Stores corresponding numeric values (3, 6, 36, etc.)
            $table->timestamps();
        });

        // Insert data directly into the table
        DB::table('ttr_data')->insert([
            ['close_bound' => 'TTR 3 Jam Manja', 'jam_value' => 3],
            ['close_bound' => 'TTR Diamond', 'jam_value' => 3],
            ['close_bound' => 'TTR Platinum', 'jam_value' => 6],
            ['close_bound' => 'TTR Non HVC', 'jam_value' => 36],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the table if it exists
        Schema::dropIfExists('ttr_data');
    }
}
