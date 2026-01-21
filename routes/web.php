<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PemindahanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StafAsetController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ✅ ROUTES YANG BISA DIAKSES SEMUA USER (STAFF & ADMIN)
Route::middleware('auth:stafaset')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
    // View Lantai & Ruangan (semua bisa lihat)
    Route::get('/lantai/{id}', [LantaiController::class, 'show'])->name('lantai.show');
    Route::get('/ruangan/{id}', [RuanganController::class, 'show'])->name('ruangan.show');
    
    // Barang CRUD (semua bisa input data)
    Route::get('/ruangan/{ruangan_id}/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    
    // Import Barang (semua bisa)
    Route::get('/barang/import/{ruangan}', [BarangController::class, 'importForm'])->name('barang.import.form');
    Route::post('/barang/import/{ruangan}', [BarangController::class, 'import'])->name('barang.import');
    
    // Pemindahan Barang (semua bisa)
    Route::get('/pemindahan/pindah', [PemindahanController::class, 'pindah'])->name('pemindahan.pindah');
    Route::post('/pemindahan/pindah', [PemindahanController::class, 'pindahStore'])->name('pemindahan.pindah.store');
    Route::get('/pemindahan/history', [PemindahanController::class, 'history'])->name('pemindahan.history');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
});

// ✅ ROUTES KHUSUS ADMIN
Route::middleware(['auth:stafaset', 'role:admin'])->group(function () {
    // Lantai Management (hanya admin)
    Route::post('/lantai', [LantaiController::class, 'store'])->name('lantai.store');
    Route::put('/lantai/{id}', [LantaiController::class, 'update'])->name('lantai.update');
    Route::delete('/lantai/{id}', [LantaiController::class, 'destroy'])->name('lantai.destroy');
    
    // Ruangan Management (hanya admin)
    Route::post('/lantai/{lantai_id}/ruangan', [LantaiController::class, 'storeRuangan'])->name('ruangan.store');
    Route::delete('/ruangan/{id}', [LantaiController::class, 'deleteRuangan'])->name('ruangan.delete');
    
    // Export PDF (hanya admin)
    Route::get('/ruangan/{id}/export', [RuanganController::class, 'export'])->name('ruangan.export');
    
    // Staff Management (hanya admin)
    Route::get('/staff', [StafAsetController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StafAsetController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StafAsetController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}/edit', [StafAsetController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{id}', [StafAsetController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{id}', [StafAsetController::class, 'destroy'])->name('staff.destroy');
}); 