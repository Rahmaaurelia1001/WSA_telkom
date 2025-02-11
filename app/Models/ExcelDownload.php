<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcelDownload extends Model
{
    // Tentukan nama tabel jika tidak sesuai dengan nama model
    protected $table = 'excel_downloads';

    // Tentukan kolom yang bisa diisi jika diperlukan
    protected $fillable = [
        'filename', 
        'downloaded_by', 
        'downloaded_at',
        'created_at', 
        'merged_data', 
        'processed_data',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime', // Pastikan ini ada
        'created_at' => 'datetime',
    ];
}
