<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class FileProcessController extends Controller
{
    // Menampilkan form upload
    public function showForm()
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);
        $successMessage = session('success_message', null);
        $rowCount = count($mergedData); // Hitung jumlah baris data

        return view('upload-form', compact('mergedData', 'header', 'successMessage', 'rowCount'));
    }

    // Proses pengunggahan dan penggabungan file
    public function process(Request $request)
    {
        $request->validate([
            'all_ticket' => 'required|file|max:10240',
            'close_ticket' => 'required|file|max:10240',
        ], [
            'all_ticket.required' => 'File All Ticket wajib diunggah.',
            'close_ticket.required' => 'File Close Ticket wajib diunggah.',
            'all_ticket.max' => 'File All Ticket tidak boleh lebih dari 10MB.',
            'close_ticket.max' => 'File Close Ticket tidak boleh lebih dari 10MB.',
        ]);

        $allTicketFile = $request->file('all_ticket');
        $closeTicketFile = $request->file('close_ticket');

        $allowedExtensions = ['xls', 'xlsx'];
        if (!in_array($allTicketFile->getClientOriginalExtension(), $allowedExtensions) ||
            !in_array($closeTicketFile->getClientOriginalExtension(), $allowedExtensions)) {
            return back()->withErrors(['msg' => 'File harus berformat .xls atau .xlsx']);
        }

        try {
            $spreadsheetAllTicket = IOFactory::load($allTicketFile->getPathname());
            $spreadsheetCloseTicket = IOFactory::load($closeTicketFile->getPathname());

            $sheetAllTicket = $spreadsheetAllTicket->getActiveSheet();
            $sheetCloseTicket = $spreadsheetCloseTicket->getActiveSheet();

            $allTicketData = $sheetAllTicket->toArray();
            $closeTicketData = $sheetCloseTicket->toArray();
        } catch (\Exception $e) {
            Log::error('Error processing files: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat membaca file: ' . $e->getMessage()]);
        }

        $header = $allTicketData[0];
        $mergedData = array_merge(array_slice($allTicketData, 1), array_slice($closeTicketData, 1));

        session(['merged_data' => $mergedData, 'header' => $header]);

        session()->flash('success_message', 'File berhasil digabungkan.');

        return redirect()->route('upload.form');
    }

    // Proses penghapusan data berdasarkan kolom dan nilai yang dipilih
    public function deleteSelected(Request $request)
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);

        $columnToDelete = $request->input('column');
        $valueToDelete = $request->input('value');

        if (empty($mergedData)) {
            return back()->withErrors(['msg' => 'Tidak ada data yang dapat dihapus.']);
        }

        $columnIndex = array_search($columnToDelete, $header);

        if ($columnIndex === false) {
            return back()->withErrors(['msg' => 'Kolom tidak ditemukan.']);
        }

        $filteredData = array_filter($mergedData, function ($row) use ($columnIndex, $valueToDelete) {
            return $row[$columnIndex] != $valueToDelete;
        });

        $filteredData = array_values($filteredData);

        session(['merged_data' => $filteredData]);

        session()->flash('success_message', 'Data berhasil dihapus.');

        return redirect()->route('upload.form');
    }
}
