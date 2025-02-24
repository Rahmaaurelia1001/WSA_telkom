<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ExcelDownload;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        ini_set('memory_limit', '1024M');
        
        try {
            Log::info('Mengambil data dari tabel excel_downloads.');
            
            // Get search parameter
            $search = $request->get('search');
            
            // Query with optional search and pagination
            $excelFiles = ExcelDownload::query()
                ->when($search, function($query, $search) {
                    return $query->where('filename', 'like', "%{$search}%")
                        ->orWhere('uploaded_by', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString();
            
            Log::info('Ditemukan ' . $excelFiles->total() . ' record.');
            
            return view('dashboard.admin', compact('excelFiles', 'search'));
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    public function navbarAdmin()
    {
        return view('admin.navbar-admin');
    }
}