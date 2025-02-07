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

            $now = Carbon::now('Asia/Jakarta');
            $hour = $now->format('H');

            $filename = sprintf(
                'Report TTR WSA - %s - %s.00 Wib.xlsx',
                $now->format('dmY'),
                $hour
            );

            // Simpan data ke database
            DB::table('excel_downloads')->insertGetId([
                'filename' => $filename,
                'merged_data' => json_encode($data),
                'processed_data' => json_encode($data),
                'downloaded_by' => auth()->check() ? auth()->user()->name : 'Guest',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Buat file Excel dari template
            $templateFile = 'Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx';
            $filePath = storage_path('app/' . $templateFile);
            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($filePath);
            $sheet1 = $spreadsheet->getSheet(0);

            // Clear content di Sheet 1 tanpa menghapus baris
            $highestRow = $sheet1->getHighestRow();
            $highestColumn = $sheet1->getHighestColumn();

            for ($row = 2; $row <= $highestRow; $row++) { // Mulai dari baris kedua
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $sheet1->setCellValue($col . $row, ''); // Set nilai sel menjadi kosong
                }
            }

            // Isi data di Sheet 1 dari baris ke-2
            $startRow = 2;
            foreach ($data as $rowData) {
                $col = 'A';
                foreach ($rowData as $cellValue) {
                    $sheet1->setCellValue($col . $startRow, $cellValue);
                    $col++;
                }
                $startRow++;
            }

            // Simpan file ke lokasi sementara
            $tempFilePath = storage_path('app/' . $filename);
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($tempFilePath);

            // Return the file for download
            return response()->download($tempFilePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan Excel:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan file: ' . $e->getMessage()], 500);
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
}