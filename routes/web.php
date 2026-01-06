<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KMeansController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\AuthController; 

// RUTE KHUSUS TAMU (BELUM LOGIN)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// RUTE KHUSUS ADMIN (SUDAH LOGIN)
Route::middleware('auth')->group(function () {
    
    // 1. Tombol Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2. Beranda (Dashboard)
    Route::get('/', [BerandaController::class, 'index'])->name('beranda.index');

    // 3. Import Data
    Route::get('/upload', [TransaksiController::class, 'index'])->name('upload.index');
    Route::post('/upload', [TransaksiController::class, 'import'])->name('upload.store');
    Route::delete('/upload/reset', [TransaksiController::class, 'destroy'])->name('upload.reset');

    // 4. Analisis K-Means
    Route::get('/kmeans', [KMeansController::class, 'index'])->name('kmeans.index');
    Route::post('/kmeans/process', [KMeansController::class, 'process'])->name('kmeans.process');
    Route::get('/kmeans/reset', [KMeansController::class, 'reset'])->name('kmeans.reset');

    // 5. Hasil & Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

});