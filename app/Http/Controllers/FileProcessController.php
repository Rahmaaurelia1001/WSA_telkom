<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class FileProcessController extends Controller
{
    public function showForm()
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);
        $successMessage = session('success_message', null);
        $rowCount = count($mergedData);

        return view('upload-form', compact('mergedData', 'header', 'successMessage', 'rowCount'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'all_ticket' => 'required|file|max:10240',  // Menghapus aturan mimes
            'close_ticket' => 'required|file|max:10240', // Menghapus aturan mimes
        ], [
            'all_ticket.required' => 'File All Ticket wajib diunggah.',
            'all_ticket.max' => 'File All Ticket tidak boleh lebih dari 10MB.',
            'close_ticket.required' => 'File Close Ticket wajib diunggah.',
            'close_ticket.max' => 'File Close Ticket tidak boleh lebih dari 10MB.',
        ]);

        try {
            $allTicketFile = $request->file('all_ticket');
            $closeTicketFile = $request->file('close_ticket');

            // Log MIME Type untuk pengecekan format file
            Log::info('All Ticket File Mime Type: ' . $allTicketFile->getMimeType());
            Log::info('Close Ticket File Mime Type: ' . $closeTicketFile->getMimeType());

            // Cek jika file berformat Excel
            if (!in_array($allTicketFile->getClientOriginalExtension(), ['xlsx', 'xls'])) {
                throw new \Exception('File All Ticket harus berformat Excel (.xlsx atau .xls).');
            }

            if (!in_array($closeTicketFile->getClientOriginalExtension(), ['xlsx', 'xls'])) {
                throw new \Exception('File Close Ticket harus berformat Excel (.xlsx atau .xls).');
            }

            // Load Excel files
            $spreadsheetAllTicket = IOFactory::load($allTicketFile->getPathname());
            $spreadsheetCloseTicket = IOFactory::load($closeTicketFile->getPathname());

            // Get active sheets
            $sheetAllTicket = $spreadsheetAllTicket->getActiveSheet();
            $sheetCloseTicket = $spreadsheetCloseTicket->getActiveSheet();

            // Get raw cell values including formulas and errors
            $allTicketData = $sheetAllTicket->toArray(null, true, false, true);
            $closeTicketData = $sheetCloseTicket->toArray(null, true, false, true);

            $header = $allTicketData[1];
            $mergedData = array_merge(
                array_slice($allTicketData, 2),
                array_slice($closeTicketData, 2)
            );

            // Find BOOKING DATE column
            $bookingDateColumn = null;
            foreach ($header as $col => $value) {
                if ($value === 'BOOKING DATE') {
                    $bookingDateColumn = $col;
                    break;
                }
            }

            if ($bookingDateColumn) {
                foreach ($mergedData as $row => $data) {
                    $cellValue = $data[$bookingDateColumn];
                    
                    // Keep cell value as is without modifying
                    if ($cellValue === null || $cellValue === '' || $cellValue === "'" || $cellValue === '"' || trim($cellValue) === '') {
                        continue;
                    }
                    
                    // Convert Excel date to datetime if numeric
                    if (is_numeric($cellValue)) {
                        try {
                            $dateValue = Date::excelToDateTimeObject($cellValue);
                            $mergedData[$row][$bookingDateColumn] = $dateValue->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            Log::error("Error converting date at row {$row}: " . $e->getMessage());
                        }
                    } else {
                        // Try to parse string date
                        try {
                            $dateValue = new \DateTime($cellValue);
                            $mergedData[$row][$bookingDateColumn] = $dateValue->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            Log::error("Error parsing date string at row {$row}: " . $e->getMessage());
                        }
                    }
                }
            }

            // Convert header and data to numeric arrays
            $header = array_values($header);
            $mergedData = array_map(function($row) {
                return array_values($row);
            }, $mergedData);

            session(['merged_data' => $mergedData, 'header' => $header]);
            session()->flash('success_message', 'File berhasil digabungkan.');

            return redirect()->route('upload.form');

        } catch (\Exception $e) {
            Log::error('Error processing files: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()]);
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
            return !in_array($row[$columnIndex], $valuesToDelete);
        });

        $filteredData = array_values($filteredData);

        session(['merged_data' => $filteredData]);
        session()->flash('success_message', 'Data berhasil dihapus.');

        return redirect()->route('upload.form');
    }

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
        sort($uniqueValues);
        
        return response()->json($uniqueValues);
    }

    public function downloadProcessedData()
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);

        if (empty($mergedData) || empty($header)) {
            return back()->withErrors(['msg' => 'Tidak ada data untuk diunduh.']);
        }

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Write header
            $sheet->fromArray([$header], null, 'A1');
            
            // Write data
            $sheet->fromArray($mergedData, null, 'A2');

            $fileName = 'Processed_Data_' . date('Ymd_His') . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ]);
        } catch (\Exception $e) {
            Log::error('Error downloading file: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat mengunduh file.']);
        }
    }
    
}