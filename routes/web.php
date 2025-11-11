<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth:stafaset')->group(function () {
    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/home/lantai', [HomeController::class, 'storeLantai'])->name('lantai.store');

    // Lantai
    Route::get('/lantai/{lantai}', [LantaiController::class, 'show'])->name('lantai.show');
    Route::post('/lantai/{lantai}/ruangan', [LantaiController::class, 'storeRuangan'])->name('ruangan.store');
    Route::delete('/ruangan/{id}/delete', [LantaiController::class, 'deleteRuangan'])->name('ruangan.delete');

    // Ruangan
    Route::get('/ruangan/{id}', [RuanganController::class, 'show'])->name('ruangan.show');
    Route::get('/ruangan/{id}/export', [RuanganController::class, 'export'])->name('ruangan.export');

    // Barang
    Route::get('/ruangan/{ruangan_id}/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
});