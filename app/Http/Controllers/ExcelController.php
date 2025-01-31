<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;

class ExcelController extends Controller
{
    public function saveExcel(Request $request)
    {
        // Optimasi 1: Setting memory dan execution time
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 5 menit
        
        try {
            $request->validate([
                'data' => 'required|array|min:1'
            ]);
    
            $data = $request->input('data');
            
            if (empty($data)) {
                return response()->json(['error' => 'Data kosong!'], 400);
            }

            // Optimasi 2: Konfigurasi garbage collector
            gc_enable();
            gc_collect_cycles();

            // Load file Excel dengan format
            $filename = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
            $filePath = storage_path('app/' . $filename);
            
            // Optimasi 3: Konfigurasi reader
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadEmptyCells(false); // Abaikan cell kosong
            $spreadsheet = $reader->load($filePath);
            
            // Akses sheet pertama
            $sheet = $spreadsheet->getSheet(0);
            
            // Optimasi 4: Nonaktifkan perhitungan formula
            Calculation::getInstance($spreadsheet)->disableCalculationCache();
            
            Log::info('Mulai menghapus data lama');
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            // Optimasi 5: Batch processing untuk penghapusan
            $batchSize = 1000;
            for ($row = 1; $row <= $highestRow; $row += $batchSize) {
                $endRow = min($row + $batchSize - 1, $highestRow);
                $range = 'A' . $row . ':' . $highestColumn . $endRow;
                
                foreach ($sheet->getRowIterator($row, $endRow) as $rowIterator) {
                    foreach ($rowIterator->getCellIterator() as $cell) {
                        $cell->setValue(null);
                    }
                }
                
                if ($row % 5000 == 0) {
                    gc_collect_cycles();
                }
            }
            
            Log::info('Mulai menulis data baru');
            // Optimasi 6: Chunk processing untuk penulisan data
            $chunkSize = 1000;
            foreach (array_chunk($data, $chunkSize) as $chunkIndex => $chunk) {
                foreach ($chunk as $rowIndex => $rowData) {
                    $actualRowIndex = ($chunkIndex * $chunkSize) + $rowIndex + 1;
                    foreach ($rowData as $columnIndex => $value) {
                        $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);
                        $sheet->setCellValue($columnLetter . $actualRowIndex, $value);
                    }
                }
                
                if ($chunkIndex % 5 == 0) {
                    gc_collect_cycles();
                }
            }
            
            // Optimasi 7: Konfigurasi writer
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false);
            
            // Header untuk streaming
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: 0');
            header('Pragma: public');
            
            // Buffer handling
            if (ob_get_length()) ob_end_clean();
            
            // Tulis file
            $writer->save('php://output');
            
            // Cleanup
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            gc_collect_cycles();
            
            exit;

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
}