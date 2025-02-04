<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use Illuminate\Support\Facades\DB; // Tambahkan baris ini
use Carbon\Carbon; // Jika Anda menggunakan Carbon
use Illuminate\Support\Facades\Auth; // Untuk autentikasi
use App\Models\ExcelDownload;

class ExcelController extends Controller
{
    public function saveExcel(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        
        try {
            $request->validate([
                'data' => 'required|array|min:1'
            ]);

            $data = $request->input('data');
            
            if (empty($data)) {
                return response()->json(['error' => 'Data kosong!'], 400);
            }

            $now = Carbon::now('Asia/Jakarta');
            $hour = $now->format('H'); // Ini akan mengambil jam aktual di Jakarta

            $filename = sprintf(
                'Report TTR WSA - %s - %s.00 Wib.xlsx',
                $now->format('dmY'),
                $hour
            );

            $now = Carbon::now('Asia/Jakarta');

        $downloadRecord = DB::table('excel_downloads')->insertGetId([
            'filename' => $filename,
            'merged_data' => json_encode($data),
            'processed_data' => json_encode($data),
            'downloaded_by' => Auth::check() ? Auth::user()->name : 'Guest',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

            gc_enable();
            gc_collect_cycles();

            $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
            $filePath = storage_path('app/' . $templateFile);
            
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadEmptyCells(false);
            $spreadsheet = $reader->load($filePath);
            
            // Ambil Sheet 1 untuk diisi dengan data
            $sheet1 = $spreadsheet->getSheet(0);
            
            // Update tanggal dan jam di Sheet 3
            $sheet3 = $spreadsheet->getSheet(1);
            
            $timezone = new \DateTimeZone('Asia/Jakarta');
            $now = new \DateTime('now', $timezone);  
            $currentDate = $now->format('d/m/Y');
            $currentHour = $now->format('H') . '.00 WIB';

            $highestRow = $sheet3->getHighestRow();
            for ($row = 1; $row <= $highestRow; $row++) {
                $cellValue = $sheet3->getCell('A' . $row)->getValue();
                if (strpos($cellValue, 'posisi') !== false) {
                    $pattern = '#posisi \d{2}/\d{2}/\d{4} pukul \d{2}\.\d{2} WIB#';
                    $replacement = "posisi {$currentDate} pukul {$currentHour}";
                    $newValue = preg_replace($pattern, $replacement, $cellValue);
                    $sheet3->setCellValue('A' . $row, $newValue);
                }
            }

            // Mulai isi data di Sheet 1 dari baris ke-2 (asumsikan baris pertama adalah header)
            $startRow = 2;
            foreach ($data as $rowData) {
                $col = 'A';
                foreach ($rowData as $cellValue) {
                    $sheet1->setCellValue($col . $startRow, $cellValue);
                    $col++;
                }
                $startRow++;
            }

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

    public function index()
    {
        // Ambil daftar file Excel dengan pagination
        $excelFiles = ExcelDownload::orderBy('created_at', 'desc')
            ->paginate(10); // 10 item per halaman

        return view('list-excel', [
            'excelFiles' => $excelFiles
        ]);
    }

    public function download($id)
    {
        $excelDownload = ExcelDownload::findOrFail($id);

        // Buat ulang file Excel dari data tersimpan
        $now = now();
        $hour = $now->format('H');
        
        $filename = $excelDownload->filename;

        $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
        $filePath = storage_path('app/' . $templateFile);
        
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadEmptyCells(false);
        $spreadsheet = $reader->load($filePath);
        
        // Ambil Sheet 1 untuk diisi dengan data
        $sheet1 = $spreadsheet->getSheet(0);
        
        // Rekonstruksi data dari JSON
        $data = json_decode($excelDownload->merged_data, true);

        // Mulai isi data di Sheet 1 dari baris ke-2 
        $startRow = 2;
        foreach ($data as $rowData) {
            $col = 'A';
            foreach ($rowData as $cellValue) {
                $sheet1->setCellValue($col . $startRow, $cellValue);
                $col++;
            }
            $startRow++;
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');
        
        $writer->save('php://output');
        exit;
    }

    public function destroy($id)
    {
        try {
            $excelDownload = ExcelDownload::findOrFail($id);
            $excelDownload->delete();

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadFromDatabase($id)
    {
        try {
            // Cari record download berdasarkan ID
            $excelDownload = ExcelDownload::findOrFail($id);

            gc_enable();
            gc_collect_cycles();

            $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
            $filePath = storage_path('app/' . $templateFile);
            
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadEmptyCells(false);
            $spreadsheet = $reader->load($filePath);
            
            // Ambil Sheet 1 untuk diisi dengan data
            $sheet1 = $spreadsheet->getSheet(0);
            
            // Update tanggal dan jam di Sheet 3
            $sheet3 = $spreadsheet->getSheet(1);
            
            $timezone = new \DateTimeZone('Asia/Jakarta');
            $now = new \DateTime('now', $timezone);  
            $currentDate = $now->format('d/m/Y');
            $currentHour = $now->format('H') . '.00 WIB';

            $highestRow = $sheet3->getHighestRow();
            for ($row = 1; $row <= $highestRow; $row++) {
                $cellValue = $sheet3->getCell('A' . $row)->getValue();
                if (strpos($cellValue, 'posisi') !== false) {
                    $pattern = '#posisi \d{2}/\d{2}/\d{4} pukul \d{2}\.\d{2} WIB#';
                    $replacement = "posisi {$currentDate} pukul {$currentHour}";
                    $newValue = preg_replace($pattern, $replacement, $cellValue);
                    $sheet3->setCellValue('A' . $row, $newValue);
                }
            }

            // Rekonstruksi data dari JSON
            $data = json_decode($excelDownload->merged_data, true);

            // Mulai isi data di Sheet 1 dari baris ke-2 
            $startRow = 2;
            foreach ($data as $rowData) {
                $col = 'A';
                foreach ($rowData as $cellValue) {
                    $sheet1->setCellValue($col . $startRow, $cellValue);
                    $col++;
                }
                $startRow++;
            }

            Calculation::getInstance($spreadsheet)->disableCalculationCache();

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $excelDownload->filename . '"');
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
            Log::error('Gagal download Excel dari database:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat download file: ' . $e->getMessage()
            ], 500);
        }
    }
}