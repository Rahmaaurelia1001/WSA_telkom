<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;

class FileProcessController extends Controller
{
    // Menampilkan form upload
    public function showForm()
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);
        $successMessage = session('success_message', null);
        $rowCount = count($mergedData);

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
    // Proses penghapusan data berdasarkan kolom dan nilai yang dipilih
public function deleteSelected(Request $request)
{
    $mergedData = session('merged_data', []);
    $header = session('header', []);

    $columnToDelete = $request->input('column');
    $valuesToDelete = $request->input('value', []);

    // Cek apakah data yang akan dihapus ada
    if (empty($mergedData)) {
        return back()->withErrors(['msg' => 'Tidak ada data yang dapat dihapus.']);
    }

    // Cek apakah kolom yang dipilih ada di header
    $columnIndex = array_search($columnToDelete, $header);

    if ($columnIndex === false) {
        return back()->withErrors(['msg' => 'Kolom tidak ditemukan.']);
    }

    // Jika nilai yang dipilih tidak ada, tampilkan pesan kesalahan
    if (empty($valuesToDelete)) {
        return back()->withErrors(['msg' => 'Tidak ada nilai yang dipilih untuk dihapus.']);
    }

    // Filter data berdasarkan kolom dan nilai yang dipilih
    $filteredData = array_filter($mergedData, function ($row) use ($columnIndex, $valuesToDelete) {
        return !in_array($row[$columnIndex], $valuesToDelete); // Hapus data yang cocok dengan nilai yang dipilih
    });

    $filteredData = array_values($filteredData); // Reindex array

    // Simpan data yang sudah difilter kembali ke session
    session(['merged_data' => $filteredData]);

    // Kirim pesan sukses
    session()->flash('success_message', 'Data berhasil dihapus.');

    return redirect()->route('upload.form');
}


    // Menampilkan opsi filter dalam bentuk checkbox
    public function showFilterOptions(Request $request)
    {
        $header = session('header', []);
        $mergedData = session('merged_data', []);

        $column = $request->input('column');
        $columnIndex = array_search($column, $header);

        if ($columnIndex === false) {
            return response()->json(['error' => 'Kolom tidak ditemukan'], 400);
        }

        $uniqueValues = array_unique(array_column($mergedData, $columnIndex));
        return response()->json($uniqueValues);
    }

    // Proses pengunduhan data hasil proses
    public function downloadProcessedData()
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);

        if (empty($mergedData) || empty($header)) {
            return back()->withErrors(['msg' => 'Tidak ada data untuk diunduh.']);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tambahkan header ke spreadsheet
        $sheet->fromArray([$header], null, 'A1');

        // Tambahkan data ke spreadsheet
        $sheet->fromArray($mergedData, null, 'A2');

        // Atur nama file
        $fileName = 'Processed_Data_' . date('Ymd_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }
}
