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
        ini_set('max_execution_time', 300); // 300 seconds (5 minutes)
        ini_set('memory_limit', '512M'); // 512 MB of memory limit
        try {
            $request->validate([
                'data' => 'required|array|min:1'
            ]);
    
            $data = $request->input('data');
            
            if (empty($data)) {
                return response()->json(['error' => 'Data kosong!'], 400);
            }

            $now = now();
            $hour = $now->format('H');
            
            $filename = sprintf(
                'Report TTR WSA - %s - %s.00 Wib.xlsx',
                $now->format('dmY'),
                $hour
            );

            gc_enable();
            gc_collect_cycles();

            $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
            $filePath = storage_path('app/' . $templateFile);
            
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadEmptyCells(false);
            $spreadsheet = $reader->load($filePath);
            
            $sheet3 = $spreadsheet->getSheet(1);
            
            $timezone = new \DateTimeZone('Asia/Jakarta');
            $now = new \DateTime('now', $timezone);  
            $currentDate = $now->format('d/m/Y');
            $currentHour = $now->format('H') . '.00 WIB';  // Menggunakan objek DateTime untuk mendapatkan jam saat ini

            $highestRow = $sheet3->getHighestRow();
            for ($row = 1; $row <= $highestRow; $row++) {
                $cellValue = $sheet3->getCell('A' . $row)->getValue();
                if (strpos($cellValue, 'posisi') !== false) {
                    // Pola regex yang diperbaiki untuk format bahasa Indonesia
                    $pattern = '#posisi \d{2}/\d{2}/\d{4} pukul \d{2}\.\d{2} WIB#'; // Pola untuk format waktu
                    $replacement = "posisi {$currentDate} pukul {$currentHour}";
                    $newValue = preg_replace($pattern, $replacement, $cellValue);
                    $sheet3->setCellValue('A' . $row, $newValue);
                }
            }

            // $now = new \DateTime();  // Inisialisasi DateTime untuk waktu saat ini
            // $timezone = new \DateTimeZone('Asia/Jakarta');  // Tentukan zona waktu WIB
            // $now->setTimezone($timezone);  // Pastikan waktu diambil sesuai zona waktu WIB
            // $currentDate = $now->format('d/m/Y');
            // $currentHour = $now->format('H') . '.00 WIB';  // Menggunakan format jam 24 jam, misalnya 15.00 WIB

            // $highestRow = $sheet3->getHighestRow();  // Mengetahui baris terakhir yang terisi
            // $highestColumn = $sheet3->getHighestColumn();  // Dapatkan kolom terakhir
            // for ($row = 1; $row <= $highestRow; $row++) {
            //     for ($col = 'A'; $col <= $highestColumn; $col++) {
            //         $cellValue = $sheet3->getCell($col . $row)->getFormattedValue();
            //         Log::info("Cell Value (Row: $row, Column: $col): $cellValue");

            //         if (strpos($cellValue, 'Update:') !== false) {
            //             $pattern = '#Update: \d{2} [A-Za-z]+ \d{4} Pkl \d{2}[:\.]\d{2}#';
            //             $replacement = "Update: {$currentDate} Pkl {$currentHour}";
            //             $newValue = preg_replace($pattern, $replacement, $cellValue);
            //             Log::info("Updated Value (Row: $row, Column: $col): $newValue");
            //             $sheet3->setCellValue($col . $row, $newValue);
            //         }
            //     }
            // }
      
            $sheet = $spreadsheet->getSheet(0);
            Calculation::getInstance($spreadsheet)->disableCalculationCache();

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Expires: 0');
            header('Pragma: public');
            
            if (ob_get_length()) ob_end_clean();
            
            $writer->save('php://output');
            
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