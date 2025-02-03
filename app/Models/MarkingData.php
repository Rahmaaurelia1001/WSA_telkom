<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkingData extends Model
{
    use HasFactory;

    protected $table = 'marking_data'; // Nama tabel di database

    protected $fillable = [
        'marking_type',
        'max_value',
        'service_type',
        'customer_segment',
        'segmen',
        'status',
        'classification',
        'status_closed',
        'closed_reopen_by',
        'ttr',
        'z',
    ];
}
