<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkingDiamondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marking_diamonds', function (Blueprint $table) {
            $table->id();
            $table->integer('max');
            $table->string('jam', 50);
            $table->timestamps();
        });

        // Insert default data
        DB::table('marking_diamonds')->insert([
            ['max' => 1, 'jam' => '<= 1 JAM'],
            ['max' => 2, 'jam' => '1-2 JAM'],
            ['max' => 3, 'jam' => '2-3 JAM'],
            ['max' => 4, 'jam' => '> 3 JAM'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marking_diamonds');
    }
}
