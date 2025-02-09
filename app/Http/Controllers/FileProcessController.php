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

        // Load template with formatting
        $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
        $filePath = storage_path('app/' . $templateFile);
        
        // Load template with all formatting and formulas
        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(false);
        $spreadsheet = $reader->load($filePath);
        
        // Select sheet 1
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        // Get the last row with data
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();

        // Clear existing content while preserving formatting
        // Start from row 2 to preserve headers
        for ($row = 2; $row <= $highestRow; $row++) {
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $cell = $sheet->getCell($col . $row);
                if (!$cell->isFormula()) {
                    // Clear content but preserve formatting
                    $cell->setValue(null);
                }
            }
        }

        // Process new data in chunks
        $chunkSize = 500;
        $startRow = 2; // Start from row 2 (after header)
        
        foreach (array_chunk($data, $chunkSize) as $chunk) {
            foreach ($chunk as $rowIndex => $rowData) {
                $currentRow = $startRow + $rowIndex;
                $rowArray = array_values((array)$rowData);
                
                foreach ($rowArray as $columnIndex => $value) {
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                    $cellCoordinate = $column . $currentRow;
                    
                    // Only update cell if it's not a formula cell
                    $cell = $sheet->getCell($cellCoordinate);
                    if (!$cell->isFormula()) {
                        $cell->setValue($value);
                    }
                }
            }
            
            $startRow += count($chunk);
            unset($chunk);
            gc_collect_cycles();
        }

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