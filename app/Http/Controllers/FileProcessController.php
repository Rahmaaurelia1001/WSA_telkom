<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class FileProcessController extends Controller
{
   public function process(Request $request)
   {
       $request->validate([
           'all_ticket' => 'required|file|mimes:xlsx,xls',
           'close_ticket' => 'required|file|mimes:xlsx,xls' 
       ]);

       try {
           // Baca file pertama (all ticket)
           $allTicketFile = IOFactory::load($request->file('all_ticket'));
           $allTicketSheet = $allTicketFile->getActiveSheet();
           $allTicketData = $allTicketSheet->toArray();

           // Baca file kedua (close ticket), skip header
           $closeTicketFile = IOFactory::load($request->file('close_ticket')); 
           $closeTicketSheet = $closeTicketFile->getActiveSheet();
           $closeTicketData = $closeTicketSheet->toArray();
           array_shift($closeTicketData); // Hapus baris header

           // Gabungkan data
           $mergedData = array_merge($allTicketData, $closeTicketData);

           // Buat file Excel baru untuk hasil gabungan
           $spreadsheet = new Spreadsheet();
           $sheet = $spreadsheet->getActiveSheet();

           // Masukkan data gabungan 
           foreach ($mergedData as $rowIndex => $row) {
               foreach ($row as $columnIndex => $value) {
                   $sheet->setCellValueByColumnAndRow($columnIndex + 1, $rowIndex + 1, $value);
               }
           }

           // Simpan file
           $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
           $outputPath = storage_path('app/public/merged_file.xlsx');
           $writer->save($outputPath);

           return back()->with('success', 'Files berhasil digabungkan.');

       } catch (\Exception $e) {
           return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
       }
   }

   public function showForm()
   {
       return view('upload-form');
   }
}