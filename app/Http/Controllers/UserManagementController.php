<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MarkingData; // Tambahkan model MarkingData
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

    public function editkonstanta()
    {
        // Menampilkan form pembuatan user baru
        return view('admin.data.editkonstanta');
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

    public function data()
    {
        // Mengambil semua data dari tabel marking_data untuk ditampilkan
        $markingData = MarkingData::all();
        return view('admin.data.add', compact('markingData'));
    }

    public function store(Request $request)
    {
        // Validasi input form
        $request->validate([
            'column' => 'required|string',  // Kolom yang dipilih harus ada
            'value' => 'required|string',   // Nilai konstanta harus ada
            'marking_type' => 'required|string', // Marking Type harus ada
        ]);

        // Ambil data dari request
        $column = $request->column;  // Kolom yang dipilih dari dropdown
        $value = $request->value;    // Nilai konstanta yang dimasukkan
        $markingType = $request->marking_type;  // Marking type yang dipilih

        // Menentukan nilai max_value berdasarkan marking_type (jika diperlukan)
        $maxValue = null;
        if ($markingType == 'type1') {
            $maxValue = 36;  // Marking 36 Jam Non HVC
        } elseif ($markingType == 'type2' || $markingType == 'type3') {
            // Marking Platinum atau Diamond
            $maxValue = "";  // Tidak ada batasan
        }

        // Menyimpan data ke tabel marking_data
        DB::table('marking_data')->insert([
            $column => $value,  // Menyimpan nilai konstanta pada kolom yang dipilih
            'marking_type' => $markingType,  // Menyimpan marking_type yang dipilih
            'max_value' => $maxValue,  // Menyimpan max_value (jika ada)
        ]);

        // Log data setelah disimpan
        \Log::debug("Data berhasil disimpan ke tabel marking_data:", [
            'column' => $column,
            'value' => $value,
            'marking_type' => $markingType,
            'max_value' => $maxValue
        ]);

        // Redirect dengan pesan sukses
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
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

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