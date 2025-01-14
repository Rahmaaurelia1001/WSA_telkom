<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileProcessController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua route yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
   
    // Route untuk file processing
    Route::get('/upload', [FileProcessController::class, 'showForm'])->name('upload.form');
    Route::post('/process', [FileProcessController::class, 'process'])->name('upload.process');
    Route::post('/process-booking-date', [FileProcessController::class, 'processBookingDate'])->name('upload.processBookingDate');
    Route::post('/delete', [FileProcessController::class, 'deleteSelected'])->name('upload.delete');
    Route::get('/download', [FileProcessController::class, 'downloadProcessedData'])->name('upload.download');
    
    // Route untuk profil pengguna
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::get('/history', [UserController::class, 'history'])->name('history');
});
