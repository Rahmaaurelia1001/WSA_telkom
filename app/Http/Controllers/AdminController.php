<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExcelDownload;  // Pastikan model yang benar di-import
use Illuminate\Support\Facades\Log;



class AdminController extends Controller
{
    public function dashboard()
{
    // Increase memory limit for large datasets
    ini_set('memory_limit', '1024M');

    try {
        Log::info('Mengambil data dari tabel excel_downloads.');

        // Ambil semua data dari tabel excel_downloads
        $excelFiles = ExcelDownload::all(); // Fetch all records

        // Log jumlah data yang ditemukan
        Log::info('Ditemukan ' . $excelFiles->count() . ' record.');

        if ($excelFiles->isEmpty()) {
            Log::info('Tidak ada data di tabel excel_downloads.');
        }

        // Return the view with the data
        return view('dashboard.admin', compact('excelFiles'));
    } catch (\Exception $e) {
        // Log the error message
        Log::error('Terjadi kesalahan saat mengambil data: ' . $e->getMessage());

        // Redirect back with an error message
        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data.');
    }
}

    public function navbarAdmin()
    {
        return view('admin.navbar-admin'); // Pastikan nama view sudah benar
    }

//     public function index()
//     {
//         try {
//             Log::info('Mengambil data dari tabel excel_downloads.');

//             // Ambil semua data dari tabel excel_downloads
//             $excelFiles = ExcelDownload::paginate(10);  // Anda bisa menyesuaikan pagination sesuai kebutuhan

//             // Log jumlah data yang ditemukan
//             Log::info('Ditemukan ' . $excelFiles->count() . ' record.');

//             if ($excelFiles->isEmpty()) {
//                 Log::info('Tidak ada data di tabel excel_downloads.');
//             }

//             return view('excel.index', compact('excelFiles'));
//         } catch (\Exception $e) {
//             Log::error('Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
//             return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data.');
//         }
//     }
}




