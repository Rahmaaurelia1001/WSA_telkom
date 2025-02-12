<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx as SpreadsheetWriter;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ProcessExcelExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $filename;

    public function __construct($data, $filename)
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function handle()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Isi data ke dalam sheet mulai dari baris ke-2
        foreach ($this->data as $index => $row) {
            $sheet->fromArray($row, null, 'A' . ($index + 2));
        }

        // Simpan file di storage/app/public/excel/
        $filePath = "public/excel/{$this->filename}";
        $writer = new SpreadsheetWriter($spreadsheet);
        $writer->save(storage_path("app/{$filePath}"));
    }
}
