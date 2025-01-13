<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertInitialDataIntoMarkingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
            [
                'marking_type' => 'Marking Diamond',
                'max_value' => 'Max',
                'service_type' => 'INTERNET',
                'customer_segment' => 'PLATINUM',
                'segmen' => null,
                'status' => 'DRAFT',
                'classification' => null,
                'status_closed' => 'MEDIACARE',
                'closed_reopen_by' => null,
                'ttr' => 'TTR Platinum',
            ],
            [
                'marking_type' => 'SERVICE TYPE',
                'max_value' => 'Max',
                'service_type' => null,
                'customer_segment' => 'SILVER',
                'segmen' => null,
                'status' => 'NEW',
                'classification' => null,
                'status_closed' => null,
                'closed_reopen_by' => null,
                'ttr' => 'TTR Non HVC',
            ],
            [
                'marking_type' => 'TYPE',
                'max_value' => 'Max',
                'service_type' => null,
                'customer_segment' => 'REGULER',
                'segmen' => null,
                'status' => null,
                'classification' => null,
                'status_closed' => null,
                'closed_reopen_by' => null,
                'ttr' => null,
            ],
            [
                'marking_type' => 'CUSTOMER SEGMENT',
                'max_value' => 'Max',
                'service_type' => null,
                'customer_segment' => 'HVC_GOLD',
                'segmen' => null,
                'status' => null,
                'classification' => null,
                'status_closed' => null,
                'closed_reopen_by' => null,
                'ttr' => null,
            ],
            [
                'marking_type' => 'SEGMEN',
                'max_value' => 'Max',
                'service_type' => null,
                'customer_segment' => 'HVC_PLATINUM',
                'segmen' => null,
                'status' => null,
                'classification' => null,
                'status_closed' => null,
                'closed_reopen_by' => null,
                'ttr' => null,
            ],
            [
                'marking_type' => 'STATUS',
                'max_value' => 'Max',
                'service_type' => null,
                'customer_segment' => 'HVC_DIAMOND',
                'segmen' => null,
                'status' => null,
                'classification' => null,
                'status_closed' => null,
                'closed_reopen_by' => null,
                'ttr' => null,
            ],
            [
                'marking_type' => 'CLASSIFICATION',
                'max_value' => 'Max',
                'service_type' => null,
                'customer_segment' => 'HVC_SILVER',
                'segmen' => null,
                'status' => null,
                'classification' => null,
                'status_closed' => null,
                'closed_reopen_by' => null,
                'ttr' => null,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Hapus data dari tabel marking_data
        DB::table('marking_data')->truncate();
    }
}
