<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\http\Middleware\RoleMiddleware;

class UserManagementController extends Controller
{
    // Konstruktor untuk memastikan hanya admin yang bisa mengakses controller ini
    public function __construct()
    {
        // Menambahkan middleware role untuk memeriksa apakah pengguna adalah admin
        $this->middleware('role:admin');
    }

    public function index()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role for new users
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully');
    }
}
