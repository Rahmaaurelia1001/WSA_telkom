<?php

namespace App\Http\Controllers;

use App\Models\ExcelDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExcelDownloadController extends Controller 
{
    public function index(Request $request)
    {
        try {
            Log::info('Mengambil data dari tabel excel_downloads.');
            
            // Add proper ordering and pagination with query string preservation
            $excelFiles = ExcelDownload::orderBy('created_at', 'desc')
                                      ->paginate(10)
                                      ->withQueryString();
            
            Log::info('Ditemukan ' . $excelFiles->total() . ' total records.');
            
            return view('excel.index', compact('excelFiles'));
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }
}