<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileProcessController;
use App\Http\Controllers\UserManagementController;

// Redirect default ke login
Route::get('/', function () {
    return redirect('/login');
});

// Rute untuk login dan logout
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute dengan proteksi middleware auth
Route::middleware(['auth'])->group(function () {

    // Dashboard untuk user
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Halaman dashboard untuk admin
    // Route::middleware(['auth', 'role:admin'])->group(function () {
    //     Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    // });    
    // // Rute untuk file processing
    Route::get('/upload', [FileProcessController::class, 'showForm'])->name('upload.form');
    Route::post('/process', [FileProcessController::class, 'process'])->name('upload.process');
    Route::post('/process-booking-date', [FileProcessController::class, 'processBookingDate'])->name('upload.processBookingDate');
    Route::post('/delete', [FileProcessController::class, 'deleteSelected'])->name('upload.delete');
    Route::get('/download', [FileProcessController::class, 'downloadProcessedData'])->name('upload.download');
    
    // Rute untuk profil pengguna
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/history', [UserController::class, 'history'])->name('history');

    // Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
    // });    

    // Rute untuk admin user management
    // Route::prefix('admin')->middleware('role:admin')->group(function () {
    //     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
    //     // Rute untuk admin user management
        Route::get('/users', [UserManagementController::class, 'list'])->name('admin.users.list');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
        Route::resource('admin/users', UserManagementController::class);
        Route::get('/data/add', [UserManagementController::class, 'data'])->name('admin.data.add');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');

    // });
});
