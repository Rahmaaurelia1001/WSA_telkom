<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerTypeToMarkingDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->string('customer_type')->nullable()->after('ttr'); // Sesuaikan posisi dengan 'after'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marking_data', function (Blueprint $table) {
            // Hapus kolom jika rollback
            $table->dropColumn('customer_type');
        });
    }
}
