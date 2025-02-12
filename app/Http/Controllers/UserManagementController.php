<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MarkingData; // Tambahkan model MarkingData
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\ExcelDownload;
use PhpOffice\PhpSpreadsheet\IOFactory;


class UserManagementController extends Controller
{
    public function list()
    {
        // Mengambil semua user dengan role 'user'
        $users = User::where('role', 'user')->get();
        return view('admin.users.list', compact('users'));
    }

    public function create()
    {
        // Menampilkan form pembuatan user baru
        return view('admin.users.create');
    }

    public function editkonstanta($id)
    {
        // Mengambil data konstanta yang ingin diedit
        $konstanta = MarkingData::findOrFail($id);
        return view('admin.data.editkonstanta', compact('konstanta'));
    }
    public function updateKonstanta(Request $request, $id)
{
    // Validasi input
    $request->validate([
        'column' => 'required|string',
        'value' => 'required|string',
    ]);

    // Ambil data yang akan diupdate
    $konstanta = DB::table('marking_data')->where('id', $id)->first();

    if (!$konstanta) {
        return redirect()->route('admin.data.add')->with('error', 'Data tidak ditemukan.');
    }

    // Hanya memperbarui kolom yang dipilih
    DB::table('marking_data')->where('id', $id)->update([
        $request->column => $request->value,
    ]);

    return redirect()->route('admin.data.add')->with('success', 'Konstanta berhasil diperbarui.');
}

    


public function deleteKonstanta($id)
{
    // Temukan data marking berdasarkan ID
    $konstanta = MarkingData::findOrFail($id);
    
    // Hapus data marking
    $konstanta->delete();

    // Log setelah data dihapus
    \Log::debug("Konstanta berhasil dihapus:", ['konstanta_id' => $konstanta->id]);

    // Redirect ke halaman index data marking setelah berhasil dihapus
    return redirect()->route('admin.data.add')->with('success', 'Konstanta berhasil dihapus.');
}
    public function createUser(Request $request)
{
    \Log::info('
    Incoming request data:', $request->all());

    try {
        // Validasi input termasuk role
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        \Log::info('Validation passed');

        // Create user dengan role default 'user'
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'user'  // Tambahkan default role
        ]);

        \Log::info('User created:', ['user_id' => $user->id]);

        return redirect()
            ->route('admin.users.list')
            ->with('success', 'User created successfully');
    } catch (\Exception $e) {
        \Log::error('Error creating user: ' . $e->getMessage());
        
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

    public function updateCell(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'rowIndex' => 'required|integer',
                'cellIndex' => 'required|integer',
                'value' => 'required|string',
            ]);

            // Path ke file Excel
            $filePath = storage_path('app/Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx');

            // Cek apakah file ada
            if (!file_exists($filePath)) {
                throw new \Exception('Excel file not found at path: ' . $filePath);
            }

            // Load spreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Konversi indeks kolom ke huruf
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($request->cellIndex + 1);
            $rowNumber = $request->rowIndex + 2; // +2 karena baris pertama adalah header

            // Update sel dengan nilai baru
            $worksheet->setCellValue($columnLetter . $rowNumber, $request->value);

            // Simpan perubahan
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($filePath);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating Excel cell: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating cell: ' . $e->getMessage()], 500);
        }
    }

public function addRow(Request $request)
{
    try {
        // Increase limits for this operation
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        
        $filePath = storage_path('app/Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx');
        
        if (!file_exists($filePath)) {
            throw new \Exception('Excel file not found at: ' . $filePath);
        }

        // Use file locking to prevent concurrent access
        $lockFile = storage_path('app/excel_lock');
        $fp = fopen($lockFile, 'w+');
        
        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            throw new \Exception('File is being updated by another process');
        }

        try {
            // Load the entire workbook first
            $spreadsheet = IOFactory::load($filePath);
            
            // Check if the workbook has any sheets
            if ($spreadsheet->getSheetCount() === 0) {
                throw new \Exception('Excel file contains no sheets');
            }

            // Get the third sheet (index 2)
            try {
                $worksheet = $spreadsheet->getSheet(2); // Try to get sheet 3 (index 2)
            } catch (\Exception $e) {
                // If sheet 3 doesn't exist, use the first available sheet
                $worksheet = $spreadsheet->getActiveSheet();
                Log::warning('Sheet 3 not found, using active sheet instead');
            }
            
            // Get dimensions
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $columnCount = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
            
            // Add empty cells in the new row
            $rowData = array_fill(0, $columnCount, '');
            $worksheet->fromArray(
                [$rowData],
                null,
                'A' . ($highestRow + 1)
            );
            
            // Optimize memory before saving
            $spreadsheet->garbageCollect();

            // Use temporary file to prevent corruption
            $tempFile = $filePath . '.tmp';
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false);
            $writer->save($tempFile);

            if (file_exists($tempFile)) {
                rename($tempFile, $filePath);
            }

            // Clean up
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            gc_collect_cycles();
            
            return response()->json([
                'success' => true,
                'newRowIndex' => $highestRow,
                'columnCount' => $columnCount
            ]);
            
        } finally {
            // Make sure we always release the lock
            if (isset($fp)) {
                flock($fp, LOCK_UN);
                fclose($fp);
            }
        }
        
    } catch (\Exception $e) {
        Log::error('Error adding row: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false, 
            'message' => 'Error adding row: ' . $e->getMessage()
        ], 500);
    }
}

public function deleteCell(Request $request)
{
    try {
        // Meningkatkan batas waktu eksekusi
        set_time_limit(300); // 5 menit
        ini_set('memory_limit', '512M'); // Meningkatkan batas memori

        // Validasi input
        $request->validate([
            'rowIndex' => 'required|integer',
            'cellIndex' => 'required|integer',
        ]);

        // Path ke file Excel
        $filePath = storage_path('app/Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx');

        // Cek apakah file ada
        if (!file_exists($filePath)) {
            throw new \Exception('Excel file not found at path: ' . $filePath);
        }

        // Gunakan file locking untuk mencegah akses bersamaan
        $lockFile = storage_path('app/excel_lock');
        $fp = fopen($lockFile, 'w+');

        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            throw new \Exception('File is being updated by another process');
        }

        try {
            // Load spreadsheet
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Konversi indeks kolom ke huruf
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($request->cellIndex + 1);
            $rowNumber = $request->rowIndex + 2; // +2 karena baris pertama adalah header

            // Hapus isi sel
            $worksheet->setCellValue($columnLetter . $rowNumber, '');

            // Simpan perubahan
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setPreCalculateFormulas(false); // Nonaktifkan perhitungan formula
            $writer->save($filePath);

            // Membersihkan memori
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
            gc_collect_cycles();

            return response()->json(['success' => true]);
        } finally {
            // Pastikan untuk selalu melepaskan kunci
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    } catch (\Exception $e) {
        Log::error('Error deleting cell: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error deleting cell: ' . $e->getMessage()], 500);
    }
}



public function addColumn(Request $request)
{
    try {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        $filePath = storage_path('app/Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx');
        
        if (!file_exists($filePath)) {
            throw new \Exception('Excel file not found');
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        
        // Get the highest column letter and convert to index
        $highestColumn = $worksheet->getHighestColumn();
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);
        
        // Get the new column letter
        $newColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
        
        // Get column name from request
        $columnName = $request->input('columnName', 'New Column');
        
        // Set the header for the new column
        $worksheet->setCellValue($newColumnLetter . '1', $columnName);
        
        // Add empty cells in the new column
        $lastRow = $worksheet->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $worksheet->setCellValue($newColumnLetter . $row, '');
        }
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);
        
        return response()->json([
            'success' => true,
            'newColumnIndex' => $columnIndex,
            'columnName' => $columnName
        ]);
    } catch (\Exception $e) {
        Log::error('Error adding column: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function saveExcelData(Request $request)
{
    set_time_limit(300);
    try {
        $request->validate([
            'data' => 'required|array',
        ]);

        // Path to the Excel file
        $filePath = storage_path('app/Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx');

        // Check if the file exists
        if (!file_exists($filePath)) {
            \Log::error('Excel file not found at path: ' . $filePath);
            return response()->json(['success' => false, 'message' => 'Excel file not found.'], 404);
        }

        // Load the existing spreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Update the spreadsheet with the new data
        foreach ($request->data as $rowIndex => $row) {
            foreach ($row as $cellIndex => $value) {
                // Convert column index to letter (A, B, C, ...)
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($cellIndex + 1);
                $cellCoordinate = $columnLetter . ($rowIndex + 1); // +1 because PhpSpreadsheet is 1-indexed
                $worksheet->setCellValue($cellCoordinate, $value);
            }
        }

        // Save the updated spreadsheet
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        \Log::error('Error saving Excel data: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Error saving data.'], 500);
    }
}

public function data()
{
    try {
        $filePath = storage_path('app/Report TTR WSA - 06012025 - 08.00 Wib (3).xlsx');

        // Check if the file exists
        if (!file_exists($filePath)) {
            Log::error('Excel file not found at path: ' . $filePath);
            return back()->with('error', 'Excel file not found.');
        }

        // Load the existing spreadsheet
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filePath);

        // Check the number of sheets
        $sheetCount = $spreadsheet->getSheetCount();
        Log::info('Number of sheets in the Excel file: ' . $sheetCount);

        // Ensure there is at least one sheet
        if ($sheetCount < 1) {
            Log::error('No sheets found in the Excel file.');
            return back()->with('error', 'No sheets found in the Excel file.');
        }

        // Get sheet 3 if it exists
        if ($sheetCount < 3) {
            Log::error('Not enough sheets in the Excel file. Expected at least 3, found: ' . $sheetCount);
            return back()->with('error', 'Not enough sheets in the Excel file.');
        }

        $worksheet = $spreadsheet->getSheet(2); // Index 2 for sheet 3
        $data = $worksheet->toArray();

        // Get headers from the first row
        $headers = array_shift($data);

        return view('admin.data.add', [
            'headers' => $headers,
            'excelData' => $data
        ]);
    } catch (\Exception $e) {
        Log::error('Error reading Excel file: ' . $e->getMessage());
        return back()->with('error', 'Error reading Excel file: ' . $e->getMessage());
    }
}

    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'column' => 'required|string',
        'value' => 'required|string',
    ]);

    // Ambil data dari request
    $column = $request->column;
    $value = $request->value;

    // Buat array kosong untuk data yang akan disimpan
    $data = [
        'service_type' => null,
        'customer_type' => null,
        'customer_segment' => null,
        'segmen' => null,
        'status' => null,
        'classification' => null,
        'status_closed' => null,
        'closed_reopen_by' => null,
        'ttr' => null,
        'marking_type' => null,
        'z' => null,
    ];

    // Hanya isi kolom yang dipilih
    $data[$column] = $value;

    // Simpan ke database
    DB::table('marking_data')->insert($data);

    return redirect()->route('admin.data.add')->with('success', 'Konstanta berhasil ditambahkan.');
}



    public function destroy($id)
    {
        // Temukan data marking berdasarkan ID
        $markingData = MarkingData::findOrFail($id);
        
        // Hapus data marking
        $markingData->delete();
        
        // Redirect ke halaman index data marking setelah berhasil dihapus
        return redirect()->route('admin.data.index')->with('success', 'Konstanta berhasil dihapus.');
    }


    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }



   

public function update(Request $request, User $user)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'password' => 'nullable|min:8', // Password opsional
    ]);

    // Simpan data yang akan diperbarui
    $data = [
        'name' => $request->name,
        'email' => $request->email,
    ];

    // Jika password diisi, hash dan masukkan ke dalam array
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // Update user
    $user->update($data);

    return redirect()->route('admin.users.list')->with('success', 'User updated successfully');
}


    public function destroylist(User $user)
    {
        \Log::info('User to delete:', ['user_id' => $user->id, 'email' => $user->email]);
    
        try {
            // Log attempt to delete user
            \Log::info('Attempting to delete user:', ['user_id' => $user->id, 'email' => $user->email]);
    
            // Delete the user
            $user->delete();
    
            // Log successful deletion
            \Log::info('User deleted successfully:', ['user_id' => $user->id, 'email' => $user->email]);
    
            // Redirect with success message
            return redirect()
                ->route('admin.users.list')
                ->with('success', 'User has been deleted successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to delete user:', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
    
            // Redirect with error message
            return redirect()
                ->route('admin.users.list')
                ->with('error', 'Failed to delete user. ' . $e->getMessage());
        }
    }
}    