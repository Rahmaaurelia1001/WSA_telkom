<?php
// FileProcessController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class FileProcessController extends Controller
{
    public function showForm()
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);
        $successMessage = session('success_message', null);
        $rowCount = count($mergedData);
        
     
        $serviceTypes = DB::table('marking_data')
            ->select('service_type')
            ->distinct()
            ->pluck('service_type')
            ->toArray();

        $segmens = DB::table('marking_data')
            ->select('segmen')
            ->distinct()
            ->pluck('segmen')
            ->toArray();

        $customerTypes = DB::table('marking_data')
            ->select('customer_type')
            ->whereNotNull('customer_type') 
            ->distinct()
            ->pluck('customer_type')
            ->toArray();

        $classificationTypes = DB::table('marking_data')
            ->select('classification')
            ->distinct()
            ->pluck('classification')
            ->toArray();

        $customerSegments = DB::table('marking_data')
            ->select('customer_segment')
            ->whereNotNull('customer_segment')
            ->where('customer_segment', '!=', '')
            ->distinct()
            ->pluck('customer_segment')
            ->toArray();
        
        $zTypes = DB::table('marking_data')
            ->select('z')
            ->distinct()
            ->pluck('z')
            ->toArray();
        
        $closedTypes = DB::table('marking_data')
            ->select('closed_reopen_by')
            ->distinct()
            ->pluck('closed_reopen_by')
            ->whereNotNull('closed_reopen_by')
            ->where('closed_reopen_by', '!=', '')
            ->toArray();
            
        return view('upload-form', compact('mergedData', 'header', 'successMessage', 'rowCount', 'serviceTypes', 'segmens', 'customerTypes', 'classificationTypes', 'customerSegments', 'zTypes', 'closedTypes'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'all_ticket' => 'required|file|max:10240',
            'close_ticket' => 'required|file|max:10240',
        ], [
            'all_ticket.required' => 'File All Ticket wajib diunggah.',
            'all_ticket.max' => 'File All Ticket tidak boleh lebih dari 10MB.',
            'close_ticket.required' => 'File Close Ticket wajib diunggah.',
            'close_ticket.max' => 'File Close Ticket tidak boleh lebih dari 10MB.',
        ]);

        try {
            $allTicketFile = $request->file('all_ticket');
            $closeTicketFile = $request->file('close_ticket');

            Log::info('All Ticket File Mime Type: ' . $allTicketFile->getMimeType());
            Log::info('Close Ticket File Mime Type: ' . $closeTicketFile->getMimeType());

            if (!in_array($allTicketFile->getClientOriginalExtension(), ['xlsx', 'xls'])) {
                throw new \Exception('File All Ticket harus berformat Excel (.xlsx atau .xls).');
            }

            if (!in_array($closeTicketFile->getClientOriginalExtension(), ['xlsx', 'xls'])) {
                throw new \Exception('File Close Ticket harus berformat Excel (.xlsx atau .xls).');
            }

            $spreadsheetAllTicket = IOFactory::load($allTicketFile->getPathname());
            $spreadsheetCloseTicket = IOFactory::load($closeTicketFile->getPathname());

            $sheetAllTicket = $spreadsheetAllTicket->getActiveSheet();
            $sheetCloseTicket = $spreadsheetCloseTicket->getActiveSheet();

            $allTicketData = $sheetAllTicket->toArray(null, true, false, true);
            $closeTicketData = $sheetCloseTicket->toArray(null, true, false, true);

            $header = $allTicketData[1];
            $mergedData = array_merge(
                array_slice($allTicketData, 2),
                array_slice($closeTicketData, 2)
            );

            // Find BOOKING DATE column
            $bookingDateColumn = null;
            foreach ($header as $col => $value) {
                if ($value === 'BOOKING DATE') {
                    $bookingDateColumn = $col;
                    break;
                }
            }

            if ($bookingDateColumn) {
                foreach ($mergedData as $row => $data) {
                    $cellValue = $data[$bookingDateColumn];
                    
                    if ($cellValue === null || $cellValue === '' || $cellValue === "'" || $cellValue === '"' || trim($cellValue) === '') {
                        continue;
                    }
                    
                    if (is_numeric($cellValue)) {
                        try {
                            $dateValue = Date::excelToDateTimeObject($cellValue);
                            $mergedData[$row][$bookingDateColumn] = $dateValue->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            Log::error("Error converting date at row {$row}: " . $e->getMessage());
                        }
                    } else {
                        try {
                            $dateValue = new \DateTime($cellValue);
                            $mergedData[$row][$bookingDateColumn] = $dateValue->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            Log::error("Error parsing date string at row {$row}: " . $e->getMessage());
                        }
                    }
                }
            }

            
            $serviceTypes = DB::table('marking_data')
                ->select('service_type')
                ->distinct()
                ->pluck('service_type')
                ->toArray();

            // Get segmen  from marking_data table
            $segmens = DB::table('marking_data')
                ->select('segmen')
                ->whereNotNull('segmen') 
                ->distinct()
                ->pluck('segmen')
                ->toArray();
            
                
            $customerTypes = DB::table('marking_data')
                ->select('customer_type')
                ->whereNotNull('customer_type') 
                ->distinct()
                ->pluck('customer_type')
                ->toArray();

            $classificationTypes = DB::table('marking_data')
                ->select('classification')
                ->distinct()
                ->pluck('classification')
                ->toArray();
            
            $customerSegments = DB::table('marking_data')
                ->select('customer_segment')
                ->whereNotNull('customer_segment')
                ->where('customer_segment', '!=', '')
                ->distinct()
                ->pluck('customer_segment')
                ->toArray();

            $zTypes = DB::table('marking_data')
                ->select('z')
                ->distinct()
                ->pluck('z')
                ->toArray();

            $closedTypes = DB::table('marking_data')
                ->select('closed_reopen_by')
                ->distinct()
                ->pluck('closed_reopen_by')
                ->whereNotNull('closed_reopen_by')
                ->where('closed_reopen_by', '!=', '')
                ->toArray();

            // Convert header and data to numeric arrays
            $header = array_values($header);
            $mergedData = array_map(function($row) {
                return array_values($row);
            }, $mergedData);

            session([
                'merged_data' => $mergedData, 
                'header' => $header,
                'service_types' => $serviceTypes,
                'segmens' => $segmens,
                'customer_types' => $customerTypes,
                'classification_types' => $classificationTypes,
                'customer_segments' => $customerSegments,
                'zs' => $zTypes,
                'closed_types' => $closedTypes
            ]);
            session()->flash('success_message', 'File berhasil digabungkan.');

            return redirect()->route('upload.form');

        } catch (\Exception $e) {
            Log::error('Error processing files: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()]);
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

    public function showFilterOptions(Request $request)
    {
        $header = session('header', []);
        $mergedData = session('merged_data', []);

        $column = $request->input('column');
        $columnIndex = array_search($column, $header);

        if ($columnIndex === false) {
            return response()->json(['error' => 'Kolom tidak ditemukan'], 400);
        }

        $uniqueValues = array_unique(array_column($mergedData, $columnIndex));
        sort($uniqueValues);
        
        return response()->json($uniqueValues);
    }

    public function downloadProcessedData()
    {
        $mergedData = session('merged_data', []);
        $header = session('header', []);

        if (empty($mergedData) || empty($header)) {
            return back()->withErrors(['msg' => 'Tidak ada data untuk diunduh.']);
        }

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Write header
            $sheet->fromArray([$header], null, 'A1');
            
            // Write data
            $sheet->fromArray($mergedData, null, 'A2');

            $fileName = 'Processed_Data_' . date('Ymd_His') . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
            ]);
        } catch (\Exception $e) {
            Log::error('Error downloading file: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat mengunduh file.']);
        }
    }

    public function getServiceTypes()
    {
        try {
            $serviceTypes = DB::table('marking_data')
                ->select('service_type')
                ->distinct()
                ->pluck('service_type');
            
            return response()->json($serviceTypes);
        } catch (\Exception $e) {
            Log::error('Error fetching service types: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data service type'], 500);
        }
    }

    public function checkServiceType($service)
    {
        try {
            $exists = DB::table('marking_data')
                ->where('service_type', $service)
                ->exists();
            
            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('Error checking service type: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa service type'], 500);
        }
    }

    public function getSegmens()
    {
        try {
            $segmens = DB::table('marking_data')
            ->select('segmen')
            ->whereNotNull('segmen') 
            ->distinct()
            ->pluck('segmen')
            ->toArray();

            Log::info('Retrieved segmens from database:', $segmens);  // Untuk memastikan data terambil

            if (empty($segmens)) {
                Log::warning('No segmen data found in marking_data table');
            }
            
            return response()->json($segmens);
        } catch (\Exception $e) {
            Log::error('Error fetching segmen types: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data segmen type'], 500);
        }
    }

    public function checkSegmen($segmen)
    {
        try {
            $exists = DB::table('marking_data')
                ->where('segmen', $segmen)
                ->exists();
            
            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('Error checking segmen type: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa segmen type'], 500);
        }
    }

    public function getCustomerTypes()
    {
        try {
            $customerTypes = DB::table('marking_data')
            ->select('customer_type')
            ->whereNotNull('customer_type') 
            ->distinct()
            ->pluck('customer_type')
            ->toArray();

            Log::info('Retrieved segmens from database:', $customerTypes);  // Untuk memastikan data terambil

            if (empty($customerTypes)) {
                Log::warning('No customer type data found in marking_data table');
            }
            
            return response()->json($customerTypes);
        } catch (\Exception $e) {
            Log::error('Error fetching segmen types: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data customer type'], 500);
        }
    }

    public function checkCustomerType($customer)
    {
        try {
            $exists = DB::table('marking_data')
                ->where('customer_type', trim($customer))  // Menggunakan trim() untuk menghapus spasi
                ->exists();
                        
            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('Error checking customer type: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa customer type'], 500);
        }
    }

    public function getClassificationTypes()
    {
        try {
            $classificationTypes = DB::table('marking_data')
                ->select('classification')
                ->distinct()
                ->pluck('classification')
                ->toArray();

            Log::info('Retrieved segmens from database:', $classificationTypes);  // Untuk memastikan data terambil

            if (empty($classificationTypes)) {
                Log::warning('No customer type data found in marking_data table');
            }
            
            return response()->json($classificationTypes);
        } catch (\Exception $e) {
            Log::error('Error fetching segmen types: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data customer type'], 500);
        }
    }

    public function checkClassificationType($classification)
    {
        try {
            $exists = DB::table('marking_data')
                ->where('classification', trim($classification))  // Menggunakan trim() untuk menghapus spasi
                ->exists();
                        
            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('Error checking customer type: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa customer type'], 500);
        }
    }

    public function getCustomerSegments()
    {
        try {
            $customerSegments = DB::table('marking_data')
                ->select('customer_segment')
                ->whereNotNull('customer_segment')
                ->where('customer_segment', '!=', '')
                ->distinct()
                ->pluck('customer_segment')
                ->toArray();

            Log::info('Retrieved segmens from database:', $customerSegments);  // Untuk memastikan data terambil

            if (empty($customerSegments)) {
                Log::warning('No customer type data found in marking_data table');
            }
            
            return response()->json($customerSegments);
        } catch (\Exception $e) {
            Log::error('Error fetching segmen types: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data customer type'], 500);
        }
    }

    public function checkCustomerSegment($customersegment)
    {
        try {
            $exists = DB::table('marking_data')
                ->where('customer_segment', trim($customersegment))  // Menggunakan trim() untuk menghapus spasi
                ->exists();
                        
            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('Error checking customer type: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa customer segment'], 500);
        }
    }

    public function getZTypes()
    {
        try {
            $zTypes = DB::table('marking_data')
                ->select('z')
                ->distinct()
                ->pluck('z')
                ->toArray();

            Log::info('Retrieved segmens from database:', $zTypes);  // Untuk memastikan data terambil

            if (empty($zTypes)) {
                Log::warning('No segmen data found in marking_data table');
            }
            
            return response()->json($zTypes);
        } catch (\Exception $e) {
            Log::error('Error fetching segmen types: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data segmen type'], 500);
        }
    }

    public function checkZType($z)
    {
        try {
            $exists = DB::table('marking_data')
                ->where('z', $z)
                ->exists();
            
            return response()->json(['exists' => $exists]);
        } catch (\Exception $e) {
            Log::error('Error checking segmen type: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa segmen type'], 500);
        }
    }
    
    public function getclosedTypes()
    {
        try {
            $closedTypes = DB::table('marking_data')
            ->select('closed_reopen_by')
            ->distinct()
            ->pluck('closed_reopen_by')
            ->whereNotNull('closed_reopen_by')
            ->where('closed_reopen_by', '!=', '')
            ->toArray();
    
            Log::info('Retrieved closed types from database:', $closedTypes);
    
            if (empty($closedTypes)) {
                Log::warning('No closed type data found in marking_data table');
            }
            
            return response()->json($closedTypes);
        } catch (\Exception $e) {
            Log::error('Error fetching closed types: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data closed type'], 500);
        }
    }
    
    public function checkClosedType($closed)
    {
        try {
            $exists = DB::table('marking_data')
                ->where('closed_reopen_by', $closed)
                ->exists();
            
            // Return inverse of exists - true if not found, false if found
            return response()->json(['exists' => !$exists]);
        } catch (\Exception $e) {
            Log::error('Error checking closed type: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat memeriksa closed type'], 500);
        }
    }
}