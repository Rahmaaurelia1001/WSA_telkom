<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerTypeV2ToMarkingDataTable extends Migration
{
    /**
     * Menjalankan migration untuk menambah kolom `customer_type`.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            if (!Schema::hasColumn('marking_data', 'customer_type')) {
                $table->string('customer_type')->nullable(); // menambahkan kolom customer_type jika belum ada
            }
        });
    }

    /**
     * Membatalkan perubahan yang dilakukan oleh migration ini.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            $table->dropColumn('customer_type'); // menghapus kolom customer_type
        });
    }
}
