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
            'all_ticket' => 'required|file|max:10240',
            'close_ticket' => 'required|file|max:10240',
        ]);

        try {
            // Fungsi untuk membaca file Excel
            $readExcel = function($file) {
                // Baca file sebagai string
                $content = file_get_contents($file->getPathname());
                
                // Simpan ke temporary file dengan nama baru
                $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
                file_put_contents($tempFile, $content);
                
                // Baca dengan PhpSpreadsheet
                $reader = IOFactory::createReader('Xlsx');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($tempFile);
                
                // Ambil data
                $data = $spreadsheet->getActiveSheet()->toArray(null, true, false, true);
                
                // Bersihkan temporary file
                unlink($tempFile);
                
                return $data;
            };

            // Baca kedua file
            $allTicketData = $readExcel($request->file('all_ticket'));
            $closeTicketData = $readExcel($request->file('close_ticket'));

            // Proses data seperti biasa
            $header = array_values($allTicketData[1]);
            $mergedData = array_merge(array_slice($allTicketData, 2), array_slice($closeTicketData, 2));

            session(['merged_data' => $mergedData, 'header' => $header]);
            session()->flash('success_message', 'File berhasil digabungkan.');

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
            
            // Generate filename
            $now = Carbon::now('Asia/Jakarta');
            $filename = sprintf(
                'Report TTR WSA - %s - %s.00 Wib.xlsx',
                $now->format('dmY'),
                $now->format('H')
            );

            // Check if template exists
            $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
            $filePath = storage_path('app/' . $templateFile);
            
            try {
                // Try to load template if exists
                if (file_exists($filePath)) {
                    $reader = IOFactory::createReaderForFile($filePath);
                    $reader->setReadDataOnly(false);
                    $reader->setReadEmptyCells(false);
                    $spreadsheet = $reader->load($filePath);
                } else {
                    // Create new spreadsheet if template doesn't exist
                    $spreadsheet = new Spreadsheet();
                }
            } catch (\Exception $e) {
                // Create new spreadsheet if loading fails
                Log::warning('Failed to load template, creating new spreadsheet: ' . $e->getMessage());
                $spreadsheet = new Spreadsheet();
            }

            // Ensure at least one sheet exists
            if ($spreadsheet->getSheetCount() === 0) {
                $spreadsheet->createSheet();
            }
            
            // Set first sheet as active
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set sheet title if it's a new sheet
            if ($sheet->getTitle() === 'Worksheet') {
                $sheet->setTitle('Sheet1');
            }

            // Clear existing content while preserving formatting
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            if ($highestRow > 1) {
                for ($row = 2; $row <= $highestRow; $row++) {
                    for ($col = 'A'; $col <= $highestColumn; $col++) {
                        $cell = $sheet->getCell($col . $row);
                        if (!$cell->isFormula()) {
                            $cell->setValue(null);
                        }
                    }
                }
            }

            // Write data in chunks
            $chunkSize = 1000;
            foreach (array_chunk($data, $chunkSize) as $index => $chunk) {
                $startRow = ($index * $chunkSize) + 2;
                $sheet->fromArray($chunk, null, 'A' . $startRow, true);
            }

            // Update date/time texts
            $this->updateDateTimeTexts($spreadsheet, $now);

            // Use temp file
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            
            // Configure writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->setOffice2003Compatibility(false);
            $writer->save($tempFile);

            // Save download info asynchronously
            dispatch(function () use ($filename, $data) {
                ExcelDownload::create([
                    'filename' => $filename,
                    'merged_data' => json_encode($data),
                    'processed_data' => json_encode($data),
                    'downloaded_by' => Auth::check() ? Auth::user()->name : 'Guest',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            })->afterResponse();

            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
                'Pragma' => 'public'
            ])->deleteFileAfterSend(true);

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

    private function updateDateTimeTexts(Spreadsheet $spreadsheet, Carbon $now)
    {
        $currentDate = $now->format('d/m/Y');
        $currentHour = $now->format('H') . '.00';
        $updateDate = $now->format('d') . ' ' . 
            ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 
             'Agustus', 'September', 'Oktober', 'November', 'Desember'][$now->month - 1] . 
            ' ' . $now->year;
        $updateTime = $now->format('H') . ':00';

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            // Only process if sheet has content
            if ($sheet->getHighestRow() > 0) {
                $rows = $sheet->getRowIterator(1, $sheet->getHighestRow());
                foreach ($rows as $row) {
                    $cellValue = $sheet->getCell('A' . $row->getRowIndex())->getValue();
                    if (!$cellValue) continue;

                    if (stripos($cellValue, 'posisi') !== false) {
                        $sheet->setCellValue('A' . $row->getRowIndex(), 
                            "posisi {$currentDate} pukul {$currentHour} WIB");
                    }
                    elseif (stripos($cellValue, 'Update:') !== false) {
                        $sheet->setCellValue('A' . $row->getRowIndex(), 
                            "Update: {$updateDate} Pkl {$updateTime}");
                    }
                }
            }
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