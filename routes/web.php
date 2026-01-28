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

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ROUTES: SEMUA USER (STAFF & ADMIN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:stafaset')->group(function () {

    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // View Lantai & Ruangan
    Route::get('/lantai/{id}', [LantaiController::class, 'show'])->name('lantai.show');
    Route::get('/ruangan/{id}', [RuanganController::class, 'show'])->name('ruangan.show');

    // Barang CRUD
    Route::get('/ruangan/{ruangan}/barang/create', [BarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy');

    // Import Barang
    Route::get('/barang/import/{ruangan}', [BarangController::class, 'importForm'])->name('barang.import.form');
    Route::post('/barang/import/{ruangan}', [BarangController::class, 'import'])->name('barang.import');

    // Pemindahan Barang
    Route::get('/pemindahan/pindah', [PemindahanController::class, 'pindah'])->name('pemindahan.pindah');
    Route::post('/pemindahan/pindah', [PemindahanController::class, 'pindahStore'])->name('pemindahan.pindah.store');
    Route::get('/pemindahan/history', [PemindahanController::class, 'history'])->name('pemindahan.history');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::get('/notifications/realtime', [NotificationController::class, 'realtime'])->name('notifications.realtime');
});

/*
|--------------------------------------------------------------------------
| ROUTES: KHUSUS ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:stafaset', 'role:admin'])->group(function () {

    // Lantai Management
    Route::post('/lantai', [LantaiController::class, 'store'])->name('lantai.store');
    Route::put('/lantai/{lantai}', [LantaiController::class, 'update'])->name('lantai.update');
    Route::delete('/lantai/{lantai}', [LantaiController::class, 'destroy'])->name('lantai.destroy');

    // Ruangan Management
    Route::post('/lantai/{lantai}/ruangan', [LantaiController::class, 'storeRuangan'])->name('ruangan.store');
    Route::put('/ruangan/{ruangan}', [LantaiController::class, 'updateRuangan'])->name('ruangan.update');
    Route::delete('/ruangan/{ruangan}', [LantaiController::class, 'deleteRuangan'])->name('ruangan.delete');

    // Export PDF
    Route::get('/ruangan/{ruangan}/export', [RuanganController::class, 'export'])->name('ruangan.export');

    // Staff Management
    Route::get('/staff', [StafAsetController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StafAsetController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StafAsetController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staf}/edit', [StafAsetController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staf}', [StafAsetController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staf}', [StafAsetController::class, 'destroy'])->name('staff.destroy');
});
