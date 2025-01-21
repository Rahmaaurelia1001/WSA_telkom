<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTableHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Membuat tabel headers
        Schema::create('header_table', function (Blueprint $table) {
            $table->id(); // ID kolom utama
            $table->string('header_name');  // Nama Header
            $table->string('description')->nullable(); // Deskripsi Header
            $table->timestamps(); // Kolom created_at dan updated_at
        });

        // Memasukkan data ke tabel
        DB::table('header_table')->insert([
            ['header_name' => 'BOOKING DATE', 'description' => 'Tanggal Pemesanan'],
            ['header_name' => 'DURASI MANJA', 'description' => 'Durasi Manja'],
            ['header_name' => 'TODAY WO', 'description' => 'Work Order Hari Ini'],
            ['header_name' => 'JAM MANJA', 'description' => 'Jam Manja'],
            ['header_name' => 'DURASI TIKET', 'description' => 'Durasi Tiket'],
            ['header_name' => 'REG-1', 'description' => 'Regional 1'],
            ['header_name' => 'SERVICE TYPE?', 'description' => 'Tipe Layanan'],
            ['header_name' => 'CUSTOMER SEGMENT (PL-TSEL)', 'description' => 'Segment Pelanggan'],
            ['header_name' => 'CUSTOMER ONLY', 'description' => 'Hanya Pelanggan'],
            ['header_name' => 'CLASSIFICATION (TECH ONLY)', 'description' => 'Klasifikasi Teknis'],
            ['header_name' => 'VALID TICKET GRUP', 'description' => 'Grup Tiket Valid'],
            ['header_name' => 'PDA', 'description' => 'PDA'],
            ['header_name' => 'NON GUARANTEE', 'description' => 'Tanpa Garansi'],
            ['header_name' => 'TIKET AKTIF', 'description' => 'Tiket Aktif'],
            ['header_name' => 'GUARANTEE', 'description' => 'Garansi'],
            ['header_name' => 'CLOSED', 'description' => 'Tertutup'],
            ['header_name' => 'FILTER ASSURANCE', 'description' => 'Filter Assurance'],
            ['header_name' => 'ASSURANCE CLOSE', 'description' => 'Assurance Close'],
            ['header_name' => 'HVC DIAMOND', 'description' => 'HVC Diamond'],
            ['header_name' => 'GRUP DIAMOND', 'description' => 'Grup Diamond'],
            ['header_name' => 'HVC PLATINUM', 'description' => 'HVC Platinum'],
            ['header_name' => 'GRUP PLATINUM', 'description' => 'Grup Platinum'],
            ['header_name' => 'NON HVC', 'description' => 'Non HVC'],
            ['header_name' => 'GRUP NON HVC', 'description' => 'Grup Non HVC'],
            ['header_name' => 'FCR', 'description' => 'First Call Resolution'],
            ['header_name' => 'TTR RESOLVED dari OPEN', 'description' => 'TTR Resolved dari Open'],
            ['header_name' => 'MANJA', 'description' => 'Manja'],
            ['header_name' => 'TTR RESOLVED dari MANJA', 'description' => 'TTR Resolved dari Manja'],
            ['header_name' => 'VALID CLOSED', 'description' => 'Valid Closed'],
            ['header_name' => 'COMPLY TTR 3 Jam Manja', 'description' => 'Comply TTR 3 Jam Manja'],
            ['header_name' => 'COMPLY TTR Diamond', 'description' => 'Comply TTR Diamond'],
            ['header_name' => 'COMPLY Platinum', 'description' => 'Comply Platinum'],
            ['header_name' => 'COMPLY Non HVC', 'description' => 'Comply Non HVC'],
            ['header_name' => 'CLOSED HI', 'description' => 'Closed HI'],
            ['header_name' => 'IS MANJA', 'description' => 'Is Manja'],
            ['header_name' => 'SISA DURASI TTR OPEN', 'description' => 'Sisa Durasi TTR Open'],
            ['header_name' => 'GRUP DURASI SISA ORDER', 'description' => 'Grup Durasi Sisa Order TTR Open'],
            ['header_name' => 'IS NOT GAMAS', 'description' => 'Is Not Gamas'],
            ['header_name' => 'IS DUPLICATE', 'description' => 'Is Duplicate'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus tabel jika migrasi dibatalkan
        Schema::dropIfExists('header_table');
    }
}
