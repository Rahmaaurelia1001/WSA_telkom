<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;
use App\Imports\AllTicketImport;
use App\Imports\CloseTicketImport;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class FileProcessController extends Controller
{
    // Menampilkan form upload
    public function showForm()
    {
        return view('upload-form');
    }

    public function process(Request $request)
{
    // Validasi file upload
    $request->validate([
        'all_ticket' => 'required|mimes:xlsx,xls',
        'close_ticket' => 'required|mimes:xlsx,xls',
    ], [
        'all_ticket.mimes' => 'File All Ticket harus berformat .xlsx atau .xls',
        'close_ticket.mimes' => 'File Close Ticket harus berformat .xlsx atau .xls',
    ]);

    // Ambil file yang diupload
    $allTicketFile = $request->file('all_ticket');
    $closeTicketFile = $request->file('close_ticket');

    // Debugging: Pastikan file diterima
    Log::info('All Ticket File: ', [$allTicketFile->getClientOriginalName()]);
    Log::info('Close Ticket File: ', [$closeTicketFile->getClientOriginalName()]);

    // Proses file menggunakan Maatwebsite Excel
    try {
        $allTicketData = Excel::toCollection(new AllTicketImport, $allTicketFile);
        $closeTicketData = Excel::toCollection(new CloseTicketImport, $closeTicketFile);
    } catch (\Exception $e) {
        Log::error('Error processing files: ' . $e->getMessage());
        return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()]);
    }

    // Gabungkan data All Ticket dengan Close Ticket
    $mergedData = $allTicketData[0]->merge($closeTicketData[0]);

    // Filter data kategori "A"
    $filteredData = $mergedData->filter(function ($row) {
        return isset($row['category']) && $row['category'] == 'A'; // Pastikan sesuai dengan nama kolom
    });

    // Cek apakah ada data yang memenuhi kriteria
    if ($filteredData->isEmpty()) {
        return back()->withErrors(['msg' => 'Tidak ada data yang memenuhi kriteria.']);
    }

    // Simpan data yang sudah difilter ke dalam database
    $filteredData->each(function ($row) {
        Ticket::create([
            'ticket_id' => $row['ticket_id'] ?? null,
            'category' => $row['category'] ?? null,
            'description' => $row['description'] ?? null,
        ]);
    });

    // Simpan hasil filter ke dalam file Excel
    $filename = 'processed_tickets.xlsx';
    Excel::store(new UserExport($filteredData), $filename);

    // Simpan nama file yang diproses ke session
    session(['processed_filename' => $filename]);

    // Debugging: Pastikan session sudah diset dengan benar
    Log::info('File telah diproses dan disimpan dengan nama: ' . $filename);

    // Informasikan kepada user bahwa file berhasil diproses
    return redirect()->route('upload.form')->with('success', 'File berhasil diproses dan siap untuk diunduh.');
}
public function download($filename)
{
    return response()->download(storage_path("app/{$filename}"));
}


}
