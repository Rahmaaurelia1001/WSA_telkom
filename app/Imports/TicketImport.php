<?php

namespace App\Imports;

use App\Models\Ticket; // Sesuaikan dengan model yang kamu gunakan
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TicketImport implements ToModel, WithHeadingRow
{
    /**
     * Transform each row of data into a Ticket model instance.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Ticket([
            'ticket_id' => $row['ticket_id'], // Ganti dengan nama kolom yang ada di Excel
            'category' => $row['category'],   // Ganti dengan nama kolom yang ada di Excel
            'description' => $row['description'], // Ganti dengan nama kolom yang ada di Excel
            // Sesuaikan dengan kolom yang ada di file excel dan model database kamu
        ]);
    }
}
