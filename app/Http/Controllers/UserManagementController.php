<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MarkingData; // Tambahkan model MarkingData
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
    \Log::info('Incoming request data:', $request->all());

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
    $request->validate([
        'rowIndex' => 'required|integer',
        'cellIndex' => 'required|integer',
        'value' => 'required|string',
    ]);

    // Logic to update the specific cell in the database
    // You will need to determine how to map rowIndex and cellIndex to your database structure

    // Example: Assuming you have a way to identify the row in your database
    $markingData = MarkingData::find($request->rowIndex); // Adjust this logic as needed
    if ($markingData) {
        // Update the specific column based on cellIndex
        // You will need to map cellIndex to the actual column name
        $columns = ['service_type', 'customer_type', 'customer_segment', 'segmen', 'status', 'classification', 'status_closed', 'closed_reopen_by', 'ttr', 'marking_type', 'z'];
        $columnName = $columns[$request->cellIndex];

        $markingData->$columnName = $request->value;
        $markingData->save();

        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Data not found.']);
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
        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filePath);
        
        // Get sheet 3
        $worksheet = $spreadsheet->getSheet(2); // Index 2 for sheet 3
        $data = $worksheet->toArray();
        
        // Get headers from first row
        $headers = array_shift($data);
        
        return view('admin.data.add', [
            'headers' => $headers,
            'excelData' => $data
        ]);
    } catch (\Exception $e) {
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