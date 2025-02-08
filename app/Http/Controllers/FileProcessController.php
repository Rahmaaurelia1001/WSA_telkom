<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FileProcessController extends Controller
{
    public function showForm()
    {
        return view('upload-form');
    }

    public function process(Request $request)
{
    $request->validate([
        'all_ticket' => 'required|file|max:10240|mimes:xlsx,xls',
        'close_ticket' => 'required|file|max:10240|mimes:xlsx,xls',
    ]);

    try {
        // Memuat file Excel
        $allTicketData = IOFactory::load($request->file('all_ticket')->getPathname())->getActiveSheet()->toArray(null, true, false, true);
        $closeTicketData = IOFactory::load($request->file('close_ticket')->getPathname())->getActiveSheet()->toArray(null, true, false, true);

        // Mengambil header sebagai array
        $header = array_values($allTicketData[1]); // Mengambil header sebagai array
        $mergedData = array_merge(array_slice($allTicketData, 2), array_slice($closeTicketData, 2));

        // Simpan data ke session
        session(['merged_data' => $mergedData, 'header' => $header]); // Simpan header sebagai array
        session()->flash('success_message', 'File berhasil digabungkan.');

        // Debug log untuk memeriksa format
        Log::info('Header:', $header);
        Log::info('Merged Data:', $mergedData);

        return redirect()->route('upload.form');
    } catch (\Exception $e) {
        Log::error('Error processing files: ' . $e->getMessage());
        return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()]);
    }
}
public function saveExcel(Request $request)
{
    // Increase PHP limits for large files
    ini_set('max_execution_time', 600);
    ini_set('memory_limit', '1024M');

    try {
        $request->validate([
            'data' => 'required|array|min:1'
        ]);

        $data = $request->input('data');

        if (empty($data)) {
            return response()->json(['error' => 'Data kosong!'], 400);
        }

        // Generate filename
        $now = Carbon::now('Asia/Jakarta');
        $filename = sprintf(
            'Report TTR WSA - %s - %s.00 Wib.xlsx',
            $now->format('dmY'),
            $now->format('H')
        );

        // Load template with minimal memory usage
        $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
        $filePath = storage_path('app/' . $templateFile);
        
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);  // Don't read formatting
        $spreadsheet = $reader->load($filePath);
        
        // Get first sheet and clear it efficiently
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getPageSetup()->setFitToWidth(1);
        
        // Clear existing data more efficiently
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->removeRow(2, $highestRow - 1);
        }

        // Batch insert data using array
        $dataArray = [];
        foreach ($data as $rowIndex => $rowData) {
            $dataArray[] = array_values((array)$rowData);
        }
        
        $sheet->fromArray($dataArray, null, 'A2', true);

        // Use memory-efficient writer
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(false);

        // Stream response with proper headers
        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'public'
            ]
        );

    } catch (\Exception $e) {
        Log::error('Gagal menyimpan Excel:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json([
            'error' => 'Terjadi kesalahan saat menyimpan file: ' . $e->getMessage()
        ], 500);
    }
}
    public function deleteSelected(Request $request)
{
    $mergedData = session('merged_data', []);
    $header = session('header', []);

    $columnToDelete = $request->input('column');
    $valuesToDelete = $request->input('value', []);

    if (empty($mergedData)) {
        return back()->withErrors(['msg' => 'Tidak ada data yang dapat dihapus.']);
    }

    $columnIndex = array_search($columnToDelete, $header);

    if ($columnIndex === false) {
        return back()->withErrors(['msg' => 'Kolom tidak ditemukan.']);
    }

    if (empty($valuesToDelete)) {
        return back()->withErrors(['msg' => 'Tidak ada nilai yang dipilih untuk dihapus.']);
    }

    $filteredData = array_filter($mergedData, function ($row) use ($columnIndex, $valuesToDelete) {
        // Convert row to array if it's not already
        $rowData = is_array($row) ? array_values($row) : array_values((array)$row);
        // Check if the column exists in the row
        return isset($rowData[$columnIndex]) && !in_array($rowData[$columnIndex], $valuesToDelete);
    });

    $filteredData = array_values($filteredData);

    session(['merged_data' => $filteredData]);
    session()->flash('success_message', 'Data berhasil dihapus.');

    return redirect()->route('upload.form');
}
}