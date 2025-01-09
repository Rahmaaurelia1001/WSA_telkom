<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport; // Ekspor untuk mendownload file
use App\Imports\AllTicketImport;
use App\Imports\CloseTicketImport;
use App\Models\Ticket;

class FileProcessController extends Controller
{
    // Menampilkan form upload
    public function showForm()
    {
        return view('upload-form');
    }

    // Proses file yang diupload
    public function process(Request $request)
    {
        // Validasi bahwa hanya file Excel yang dapat diunggah
        $request->validate([
            'all_ticket' => 'required|mimes:xlsx,xls',
            'close_ticket' => 'required|mimes:xlsx,xls',
        ]);

        // Ambil file yang diupload
        $allTicketFile = $request->file('all_ticket');
        $closeTicketFile = $request->file('close_ticket');

        // Proses file All Ticket dan Close Ticket menggunakan Maatwebsite Excel
        $allTicketData = Excel::toCollection(new AllTicketImport, $allTicketFile);
        $closeTicketData = Excel::toCollection(new CloseTicketImport, $closeTicketFile);

        // Gabungkan data Close Ticket ke dalam All Ticket
        $mergedData = $allTicketData[0]->merge($closeTicketData[0]);

        // Lakukan pemfilteran kategori "A" pada data
        $filteredData = $mergedData->filter(function ($row) {
            return isset($row['category']) && $row['category'] == 'A'; // Pastikan ini sesuai dengan nama kolom di file Excel
        });

        // Cek jika data yang difilter kosong
        if ($filteredData->isEmpty()) {
            return back()->withErrors(['msg' => 'Tidak ada data yang memenuhi kriteria.']);
        }

        // Simpan data yang sudah difilter ke dalam database
        $filteredData->each(function ($row) {
            // Menyimpan data ke dalam database, sesuaikan dengan kolom yang ada di Excel
            Ticket::create([
                'ticket_id' => $row['ticket_id'] ?? null,  // Pastikan nama kolom sesuai dengan yang ada di Excel
                'category' => $row['category'] ?? null,    // Pastikan nama kolom sesuai dengan yang ada di Excel
                'description' => $row['description'] ?? null,  // Pastikan nama kolom sesuai dengan yang ada di Excel
                // Kolom lainnya bisa ditambahkan jika ada
            ]);
        });

        // Menyimpan hasil filter ke dalam file Excel
        $filename = 'processed_tickets.xlsx';
        Excel::store(new UserExport($filteredData), $filename);

        // Kembali ke halaman dengan pesan sukses
        return back()->with('success', 'File berhasil diproses dan siap untuk diunduh.');
    }

    // Fungsi untuk mendownload file yang sudah diproses
    public function download($filename)
    {
        // Mengunduh file hasil pemrosesan
        return response()->download(storage_path("app/{$filename}"));
    }
}
