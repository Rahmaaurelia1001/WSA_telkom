<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileProcessController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\ExcelController;  // Mengimport controller

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
    Route::get('/dashboardUser', [DashboardUserController::class, 'dashboardUser'])->name('dashboardUser');
;
    

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
        // Route::post('/download-excel', [ExcelController::class, 'downloadExcel']);
        // routes/web.php
        Route::post('/api/save-excel', [ExcelController::class, 'saveExcel']);
        
    // });    

    // Rute untuk admin user management
    // Route::prefix('admin')->middleware('role:admin')->group(function () {
    //     Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
    //     // Rute untuk admin user management
        Route::get('/users', [UserManagementController::class, 'list'])->name('admin.users.list');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        // Route::get('/users/konstanta', [UserManagementController::class, 'editkonstanta'])->name('admin.data.editkonstanta');
        Route::post('/users/create', [UserManagementController::class, 'createUser'])->name('admin.users.createUser');
        // Route::post('/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroylist'])->name('admin.users.destroy');
        Route::resource('admin/users', UserManagementController::class);
        Route::get('/data/add', [UserManagementController::class, 'data'])->name('admin.data.add');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::post('/data/add', [UserManagementController::class, 'store'])->name('admin.data.store');
        Route::get('/data/list', [UserManagementController::class, 'list'])->name('admin.data.list'); // Menambahkan route untuk list
        
        // Route::post('/store', [AdminDataController::class, 'store'])->name('store');
        // Route::delete('/destroy/{id}', [AdminDataController::class, 'destroy'])->name('destroy');
        Route::get('/admin/data/edit/', [UserManagementController::class, 'edit'])->name('admin.data.edit');
        // Route::put('/admin/edit/{id}', [UserManagementController::class, 'updateKonstanta'])->name('admin.data.update');
        // Route::delete('/admin/data/delete{id}/', [UserManagementController::class, 'deleteKonstanta'])->name('admin.data.deleteKonstanta');
        
        Route::get('/users/konstanta/{id}/edit', [UserManagementController::class, 'editkonstanta'])->name('admin.data.editkonstanta');
        // Route::put('/users/konstanta/{id}', [UserManagementController::class, 'updateKonstanta'])->name('admin.data.updateKonstanta');
        Route::delete('/users/konstanta/{id}', [UserManagementController::class, 'deleteKonstanta'])->name('admin.data.deleteKonstanta');
        Route::put('/users/konstanta/{id}/edit2', [UserManagementController::class, 'updateKonstanta'])->name('admin.data.updateKonstanta');
        // Route::delete('/data/konstanta/{id}', [UserManagementController::class, 'deleteKonstanta'])->name('admin.data.deleteKonstanta');
        

       


    // });
});
