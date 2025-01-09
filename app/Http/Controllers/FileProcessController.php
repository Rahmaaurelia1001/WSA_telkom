<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileProcessController extends Controller
{
    // Menampilkan form upload
    public function showForm()
    {
        // Ambil data yang telah digabungkan dari session (jika ada)
        $mergedData = session('merged_data', []);
        $header = session('header', []);  // Ambil header dari session
        // Ambil pesan sukses dari session (jika ada)
        $successMessage = session('success_message', null);
        return view('upload-form', compact('mergedData', 'header', 'successMessage'));
    }

    // Proses pengunggahan dan penggabungan file
    public function process(Request $request)
    {
        // Validasi file upload dengan ukuran maksimum 10MB
        $request->validate([
            'all_ticket' => 'required|mimes:xlsx,xls|max:10240',  // Maksimal 10MB
            'close_ticket' => 'required|mimes:xlsx,xls|max:10240',
        ], [
            'all_ticket.mimes' => 'File All Ticket harus berformat .xlsx atau .xls',
            'close_ticket.mimes' => 'File Close Ticket harus berformat .xlsx atau .xls',
            'all_ticket.max' => 'File All Ticket tidak boleh lebih dari 10MB',
            'close_ticket.max' => 'File Close Ticket tidak boleh lebih dari 10MB',
        ]);

        // Ambil file yang diupload
        $allTicketFile = $request->file('all_ticket');
        $closeTicketFile = $request->file('close_ticket');

        // Debugging: Pastikan file diterima
        Log::info('All Ticket File: ', [$allTicketFile->getClientOriginalName()]);
        Log::info('Close Ticket File: ', [$closeTicketFile->getClientOriginalName()]);

        try {
            // Baca data dari kedua file menggunakan PhpSpreadsheet
            $spreadsheetAllTicket = IOFactory::load($allTicketFile);
            $spreadsheetCloseTicket = IOFactory::load($closeTicketFile);

            // Ambil sheet pertama dari masing-masing file
            $sheetAllTicket = $spreadsheetAllTicket->getActiveSheet();
            $sheetCloseTicket = $spreadsheetCloseTicket->getActiveSheet();

            // Ambil data dari kedua sheet
            $allTicketData = $sheetAllTicket->toArray();
            $closeTicketData = $sheetCloseTicket->toArray();
        } catch (\Exception $e) {
            Log::error('Error processing files: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat membaca file: ' . $e->getMessage()]);
        }

        // Ambil header dari file pertama (all_ticket)
        $header = $allTicketData[0];

        // Gabungkan data dari kedua file (abaikan header duplikat)
        $mergedData = array_merge(array_slice($allTicketData, 1), array_slice($closeTicketData, 1));

        // Cek jika data terlalu besar, simpan ke storage sementara
        if (count($mergedData) > 1000) {
            $filePath = 'merged_data/' . uniqid() . '.json';
            Storage::put($filePath, json_encode($mergedData));
            session(['merged_data_file' => $filePath]);
        } else {
            // Simpan data yang telah digabungkan dan header ke session
            session(['merged_data' => $mergedData, 'header' => $header]);
        }

        // Set pesan sukses ke session
        session()->flash('success_message', 'File berhasil digabungkan.');

        // Redirect kembali ke form dengan pesan sukses
        return redirect()->route('upload.form');
    }
}
