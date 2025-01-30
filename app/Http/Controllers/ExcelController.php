<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ExcelController extends Controller
{
    public function saveExcel(Request $request)
    {
        ini_set('max_execution_time', 300); // 300 seconds (5 minutes)
        ini_set('memory_limit', '512M'); // 512 MB of memory limit
        try {
            // ✅ Validasi request: pastikan 'data' ada dan berupa array
            $request->validate([
                'data' => 'required|array|min:1'
            ]);

            $data = $request->input('data');

            // 🔍 Debug log untuk melihat data yang masuk
            Log::info('Menerima data untuk Excel:', ['data' => $data]);

            // ❌ Cek jika data kosong (berisi array kosong)
            if (empty($data)) {
                return response()->json(['error' => 'Data kosong, tidak bisa diproses!'], 400);
            }

            // 📁 Tentukan path file Excel yang sudah ada di storage
            $filename = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx'; // Nama file yang sudah ada
            $filePath = storage_path('app/' . $filename);

            // ❓ Cek apakah file ada
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File yang dimaksud tidak ditemukan!'], 404);
            }

            // ✅ Muat file yang sudah ada menggunakan IOFactory
            $spreadsheet = IOFactory::load($filePath);

            // Akses sheet pertama (sheet1)
            $sheet1 = $spreadsheet->getSheet(0); // Sheet pertama

            // 🧹 Bersihkan isi sheet pertama dengan menuliskan nilai kosong pada seluruh cell
            $highestRow = $sheet1->getHighestRow();
            $highestColumn = $sheet1->getHighestColumn();

            // Loop untuk menghapus konten di setiap cell
            for ($row = 1; $row <= $highestRow; $row++) {
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $sheet1->setCellValue($col . $row, null);  // Mengosongkan cell
                }
            }

            // 📄 Masukkan data baru ke dalam sheet pertama
            foreach ($data as $rowIndex => $row) {
                foreach ($row as $colIndex => $value) {
                    $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + 1);
                    $sheet1->setCellValue($cellCoordinate, $value);
                }
            }

            // 📁 Tentukan path penyimpanan file yang diperbarui
            $newFilePath = storage_path('app/public/processed_data_' . now()->format('Ymd_His') . '.xlsx');
            
            // 💾 Simpan file yang sudah diperbarui
            $writer = new Xlsx($spreadsheet);
            $writer->save($newFilePath);

            // 🖥️ Buat URL untuk mengunduh file yang sudah diperbarui
            $downloadUrl = Storage::url('public/' . basename($newFilePath));

            return response()->json([
                'success' => true,
                'filename' => basename($newFilePath),
                'download_url' => $downloadUrl
            ]);

        } catch (\Exception $e) {
            // 🔥 Log error untuk debugging
            Log::error('Gagal menyimpan Excel:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat menyimpan file. Cek log untuk detailnya.'
            ], 500);
        }
    }
}
