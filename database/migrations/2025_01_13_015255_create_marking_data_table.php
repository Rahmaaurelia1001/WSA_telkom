<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMarkingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel marking_data
        Schema::create('marking_data', function (Blueprint $table) {
            $table->id();
            $table->string('marking_type');
            $table->string('max_value');
            $table->string('service_type')->nullable();
            $table->string('customer_segment')->nullable();
            $table->string('segmen')->nullable();
            $table->string('status')->nullable();
            $table->string('classification')->nullable();
            $table->string('status_closed')->nullable();
            $table->string('closed_reopen_by')->nullable();
            $table->string('ttr')->nullable();
            $table->timestamps();
        });

        // Memasukkan data ke tabel marking_data
        DB::table('marking_data')->insert([
            [
                'marking_type' => 'Marking 36 Jam Non HVC',
                'max_value' => 'Max 36',
                'service_type' => 'IPTV',
                'customer_segment' => 'GOLD',
                'segmen' => 'PL-TSEL',
                'status' => 'ANALYSIS',
                'classification' => 'TECHNICAL',
                'status_closed' => 'CLOSED',
                'closed_reopen_by' => 'CLOSED by FCR Front Liner',
                'ttr' => 'TTR 3 Jam Manja',
            ],
            [
                'marking_type' => 'Marking Platinum',
                'max_value' => 'Max',
                'service_type' => 'VOICE',
                'customer_segment' => 'DIAMOND',
                'segmen' => null,
                'status' => 'BACKEND',
                'classification' => 'NOT GUARANTEE',
                'status_closed' => 'SALAMSIM',
                'closed_reopen_by' => null,
                'ttr' => 'TTR Diamond',
            ],
            // Tambahkan data lainnya sesuai kebutuhan
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus tabel marking_data
        Schema::dropIfExists('marking_data');
    }
}
