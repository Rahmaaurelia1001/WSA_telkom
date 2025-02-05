<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\MarkingData; // Tambahkan model MarkingData
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


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

    public function data()
    {
        // Mengambil semua data dari tabel marking_data untuk ditampilkan
        $markingData = MarkingData::all();
        return view('admin.data.add', compact('markingData'));
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