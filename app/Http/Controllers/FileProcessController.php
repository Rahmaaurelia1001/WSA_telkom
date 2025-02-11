<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Untuk autentikasi
use App\Models\ExcelDownload;  // Pastikan model yang benar di-import

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

            // Generate filename and current date/time
            $now = Carbon::now('Asia/Jakarta');
            $filename = sprintf(
                'Report TTR WSA - %s - %s.00 Wib.xlsx',
                $now->format('dmY'),
                $now->format('H')
            );

            // Format current date and time for position text
            $currentDate = $now->format('d/m/Y');
            $currentHour = $now->format('H') . '.00';

            // Format for Update text (with Indonesian month names)
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            
            $updateDate = sprintf(
                "%02d %s %d",
                $now->day,
                $months[$now->month],
                $now->year
            );
            $updateTime = $now->format('H') . ':00';

            // Load template with formatting
            $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
            $filePath = storage_path('app/' . $templateFile);
            
            // Load template with all formatting and formulas
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(false);
            $spreadsheet = $reader->load($filePath);
            
            // Select sheet 1 for data
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Get the last row with data
            $highestRow = $sheet->getHighestDataRow();
            $highestColumn = $sheet->getHighestDataColumn();

            // Clear existing content while preserving formatting
            for ($row = 2; $row <= $highestRow; $row++) {
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cell = $sheet->getCell($col . $row);
                    if (!$cell->isFormula()) {
                        $cell->setValue(null);
                    }
                }
            }

            // Process new data in chunks
            $chunkSize = 500;
            $startRow = 2;
            
            foreach (array_chunk($data, $chunkSize) as $chunk) {
                foreach ($chunk as $rowIndex => $rowData) {
                    $currentRow = $startRow + $rowIndex;
                    $rowArray = array_values((array)$rowData);
                    
                    foreach ($rowArray as $columnIndex => $value) {
                        $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
                        $cellCoordinate = $column . $currentRow;
                        
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

            // Try to update texts in all sheets
            $sheetCount = $spreadsheet->getSheetCount();

            for ($sheetIndex = 0; $sheetIndex < $sheetCount; $sheetIndex++) {
                $currentSheet = $spreadsheet->setActiveSheetIndex($sheetIndex);
                $sheetName = $currentSheet->getTitle();
                Log::info("Processing sheet: " . $sheetName);
                
                $highestRow = $currentSheet->getHighestRow();

                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $currentSheet->getCell('A' . $row)->getValue();
                    
                    if ($cellValue) {
                        // Update "posisi" text
                        if (strpos(strtolower($cellValue), 'posisi') !== false) {
                            $pattern = '/posisi\s+\d{2}\/\d{2}\/\d{4}\s+pukul\s+\d{2}\.\d{2}\s*WIB/i';
                            $replacement = "posisi {$currentDate} pukul {$currentHour} WIB";
                            
                            $newValue = preg_replace($pattern, $replacement, $cellValue);
                            if ($newValue !== $cellValue) {
                                $currentSheet->setCellValue('A' . $row, $newValue);
                            } else {
                                $currentSheet->setCellValue('A' . $row, $replacement);
                            }
                        }
                        
                        // Update "Update:" text
                        if (stripos($cellValue, 'Update:') !== false) {
                            Log::info("Found Update text in sheet {$sheetName}, row {$row}: {$cellValue}");
                            
                            // More specific pattern for Update text
                            $updatePattern = '/Update:\s*\d{2}\s+[A-Za-z]+\s+\d{4}\s+Pkl\s+\d{2}:\d{2}/i';
                            $updateReplacement = "Update: {$updateDate} Pkl {$updateTime}";
                            
                            $newValue = preg_replace($updatePattern, $updateReplacement, $cellValue);
                            
                            if ($newValue !== $cellValue) {
                                Log::info("Updating cell value from: {$cellValue}");
                                Log::info("Updating cell value to: {$newValue}");
                                $currentSheet->setCellValue('A' . $row, $newValue);
                            } else {
                                Log::info("Pattern match failed, using direct replacement");
                                Log::info("Original value: {$cellValue}");
                                Log::info("New value: {$updateReplacement}");
                                $currentSheet->setCellValue('A' . $row, $updateReplacement);
                            }
                        }
                    }
                }
            }

            // Return to first sheet
            $spreadsheet->setActiveSheetIndex(0);

            // Use memory-efficient writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->setOffice2003Compatibility(false);

            // Stream response with proper headers
            $response = response()->stream(
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

            // Simpan informasi pengunduhan ke database
            ExcelDownload::create([
                'filename' => $filename,
                'merged_data' => json_encode($data), // Simpan data yang digabungkan
                'processed_data' => json_encode($data), // Simpan data yang diproses
                'downloaded_by' => Auth::check() ? Auth::user()->name : 'Guest', // Siapa yang mengunduh
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return $response;

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
            $rowData = is_array($row) ? array_values($row) : array_values((array)$row);
            return isset($rowData[$columnIndex]) && !in_array($rowData[$columnIndex], $valuesToDelete);
        });

        $filteredData = array_values($filteredData);

        session(['merged_data' => $filteredData]);
        session()->flash('success_message', 'Data berhasil dihapus.');

        return redirect()->route('upload.form');
    }
}