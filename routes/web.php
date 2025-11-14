<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:stafaset')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // Lantai Routes
    Route::post('/lantai', [HomeController::class, 'storeLantai'])->name('lantai.store');
    Route::get('/lantai/{id}', [LantaiController::class, 'show'])->name('lantai.show');
    Route::put('/lantai/{id}', [LantaiController::class, 'update'])->name('lantai.update');
    Route::delete('/lantai/{id}', [LantaiController::class, 'destroy'])->name('lantai.destroy');
    
    // Ruangan Routes
    Route::post('/lantai/{lantai_id}/ruangan', [LantaiController::class, 'storeRuangan'])->name('ruangan.store');
    Route::delete('/ruangan/{id}', [LantaiController::class, 'deleteRuangan'])->name('ruangan.delete');
    Route::get('/ruangan/{id}', [RuanganController::class, 'show'])->name('ruangan.show');
    Route::get('/ruangan/{id}/export', [RuanganController::class, 'export'])->name('ruangan.export');

    // Barang Routes
    Route::get('/ruangan/{ruangan_id}/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');

    Route::get('/barang/import/{ruangan}', [BarangController::class, 'importForm'])->name('barang.import.form');
    Route::post('/barang/import/{ruangan}', [BarangController::class, 'import'])->name('barang.import');
});