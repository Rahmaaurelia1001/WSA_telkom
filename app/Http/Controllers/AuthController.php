<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RoleMiddleware;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input login
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Proses autentikasi
        if (Auth::attempt($credentials)) {
            // Regenerasi session setelah login
            $request->session()->regenerate();

            // Mengecek role pengguna dan mengarahkan ke halaman yang sesuai
            if (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard'); // Admin diarahkan ke dashboard admin
            }

            return redirect()->intended('dashboard'); // User diarahkan ke dashboard umum
        }

        // Jika login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Proses logout
    public function logout(Request $request)
    {
        // Logout pengguna dan invalidate session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman utama setelah logout
        return redirect('/');
    }

    // // Menampilkan dashboard
    public function dashboard()
    {
        // Jika pengguna adalah admin, arahkan ke dashboard admin
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Menampilkan dashboard pengguna biasa
        return view('dashboard');
    }

    // Menampilkan halaman profil pengguna
    public function profile()
    {
        $user = auth()->user();
        return view('profile', compact('user'));
    }

    // Memperbarui profil pengguna
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        // Validasi input form profil
        $request->validate([
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Menyimpan foto profil jika ada
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $user->profile_picture = $path;
        }

        // Memperbarui nama pengguna
        $user->name = $request->name;
        $user->save();

        // Mengarahkan kembali dengan pesan sukses
        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public function adminDashboard()
{
    // Cek jika pengguna memiliki role admin
    if (Auth::user()->hasRole('admin')) {
        // Kembalikan tampilan dashboard admin
        return view('admin.dashboard');
    }

    // Jika pengguna tidak memiliki role admin, alihkan ke halaman lain
    return redirect('/dashboard');
}

}
