<?php

namespace App\Http\Controllers;

use App\Models\ExcelDownload;  // Pastikan model yang benar di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExcelDownloadController extends Controller
{
    public function index()
    {
        try {
            Log::info('Mengambil data dari tabel excel_downloads.');

            // Ambil semua data dari tabel excel_downloads
            $excelFiles = ExcelDownload::paginate(10);  // Anda bisa menyesuaikan pagination sesuai kebutuhan

            // Log jumlah data yang ditemukan
            Log::info('Ditemukan ' . $excelFiles->count() . ' record.');

            if ($excelFiles->isEmpty()) {
                Log::info('Tidak ada data di tabel excel_downloads.');
            }

            return view('excel.index', compact('excelFiles'));
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
}
