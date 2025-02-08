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
        // Load Excel files
        $allTicketData = IOFactory::load($request->file('all_ticket')->getPathname())->getActiveSheet()->toArray(null, true, false, true);
        $closeTicketData = IOFactory::load($request->file('close_ticket')->getPathname())->getActiveSheet()->toArray(null, true, false, true);

        // Log the raw data
        Log::info('All Ticket Data:', $allTicketData);
        Log::info('Close Ticket Data:', $closeTicketData);

        // Check if all_ticket data is loaded correctly
        if (empty($allTicketData) || !is_array($allTicketData)) {
            return back()->withErrors(['msg' => 'Data dari file all_ticket tidak ditemukan atau kosong.']);
        }

        // Check if the header exists for all_ticket
        if (!isset($allTicketData[1])) {
            return back()->withErrors(['msg' => 'Header tidak ditemukan di file all_ticket.']);
        }

        // Get header as an array
        $header = array_values($allTicketData[1]); // Get header as an array

        // Prepare close_ticket data
        $closeData = array_slice($closeTicketData, 2); // Skip header row
        if (empty($closeData)) {
            Log::info('Close Ticket Data is empty, proceeding with only all_ticket data.');
        }

        // Merge data, ensuring we handle the case where close_ticket has no data
        $mergedData = array_merge(array_slice($allTicketData, 2), $closeData);

        // Save data to session
        session(['merged_data' => $mergedData, 'header' => $header]); // Save header as an array
        session()->flash('success_message', 'File berhasil digabungkan.');

        // Debug log to check format
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
    // Get data from session
    $mergedData = session('merged_data', []);
    $header = session('header', []);

    // Debug logging
    Log::info('Delete request received', [
        'column' => $request->input('column'),
        'values' => $request->input('value'),
        'header' => $header,
        'data_count_before' => count($mergedData)
    ]);

    // Validation
    if (empty($mergedData) || !is_array($mergedData)) {
        Log::error('No merged data found in session');
        return back()->withErrors(['msg' => 'Tidak ada data yang dapat dihapus.']);
    }

    if (empty($header) || !is_array($header)) {
        Log::error('No header found in session');
        return back()->withErrors(['msg' => 'Header tidak ditemukan.']);
    }

    $columnToDelete = $request->input('column');
    $valuesToDelete = $request->input('value', []);

    if (empty($columnToDelete)) {
        Log::error('No column specified for deletion');
        return back()->withErrors(['msg' => 'Kolom tidak ditemukan.']);
    }

    if (empty($valuesToDelete)) {
        Log::error('No values specified for deletion');
        return back()->withErrors(['msg' => 'Tidak ada nilai yang dipilih untuk dihapus.']);
    }

    // Find column index
    $columnIndex = array_search($columnToDelete, array_values($header));
    
    if ($columnIndex === false) {
        Log::error('Column not found in header', ['column' => $columnToDelete, 'header' => $header]);
        return back()->withErrors(['msg' => 'Kolom tidak ditemukan dalam header.']);
    }

    // Filter data
    $filteredData = [];
    foreach ($mergedData as $row) {
        // Convert row to array if it's not already
        $rowArray = is_array($row) ? $row : (array)$row;
        
        // Skip if the row doesn't have the column or if the value should be deleted
        if (!isset($rowArray[$columnIndex]) || in_array($rowArray[$columnIndex], $valuesToDelete)) {
            continue;
        }
        
        $filteredData[] = $rowArray;
    }

    // Debug logging
    Log::info('Data filtered', [
        'data_count_after' => count($filteredData),
        'removed_count' => count($mergedData) - count($filteredData)
    ]);

    // Save filtered data back to session
    session(['merged_data' => $filteredData]);
    session()->flash('success_message', sprintf(
        'Berhasil menghapus %d data.',
        count($mergedData) - count($filteredData)
    ));

    return redirect()->route('upload.form');
}
    return redirect()->route('upload.form');
}
}