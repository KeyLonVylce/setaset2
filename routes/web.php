<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LantaiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StafAsetController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotForm']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm']);
Route::post('/reset-password/{token}', [AuthController::class, 'resetPassword']);

// Routes yang bisa diakses semua user yang login (staff & admin)
Route::middleware('auth:stafaset')->group(function () {
    Route::get('/home', [LantaiController::class, 'index'])->name('home');

    // Lantai - hanya view
    Route::get('/lantai/{id}', [LantaiController::class, 'show'])->name('lantai.show');

    // Ruangan - semua bisa lihat, tapi CRUD hanya admin (akan di-group terpisah)
    Route::get('/ruangan/{id}', [RuanganController::class, 'show'])->name('ruangan.show');

    // Barang - semua bisa CRUD
    Route::prefix('barang')->group(function () {
        Route::get('/create/{ruangan_id}', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/store/{ruangan_id}', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/edit/{id}', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/update/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/destroy/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::get('/import/{ruangan_id}', [BarangController::class, 'importForm'])->name('barang.import.form');
        Route::post('/import/{ruangan_id}', [BarangController::class, 'import'])->name('barang.import');
        Route::get('/pindah', [BarangController::class, 'pindahForm'])->name('barang.pindah.form');
        Route::post('/pindah', [BarangController::class, 'pindahStore'])->name('barang.pindah.store');
        Route::get('/history', [BarangController::class, 'history'])->name('barang.history');
        Route::delete('/bulk-destroy', [BarangController::class, 'bulkDestroy'])->name('barang.bulk.destroy');
    });

    //
    Route::get('/pindah-barang', [BarangController::class, 'pindahForm'])
    ->name('pindah.form');

    // Notifikasi
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::get('/realtime', [NotificationController::class, 'realtime'])->name('notifications.realtime');
    });

    //Profile
    Route::get('/profile/edit', [StafAsetController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [StafAsetController::class, 'updateProfile'])->name('profile.update');
});

// Routes khusus admin
Route::middleware(['auth:stafaset', 'role:admin'])->prefix('admin')->group(function () {
    // Lantai Management
    Route::post('/lantai', [LantaiController::class, 'store'])->name('lantai.store');
    Route::put('/lantai/{id}', [LantaiController::class, 'update'])->name('lantai.update');
    Route::delete('/lantai/{id}', [LantaiController::class, 'destroy'])->name('lantai.destroy');

    // Ruangan Management
    Route::post('/ruangan/{lantai_id}', [RuanganController::class, 'store'])->name('ruangan.store');
    Route::put('/ruangan/{id}', [RuanganController::class, 'update'])->name('ruangan.update');
    Route::delete('/ruangan/{id}', [RuanganController::class, 'destroy'])->name('ruangan.destroy');

    // Export
    Route::get('/ruangan/{id}/export', [RuanganController::class, 'export'])->name('ruangan.export');
    Route::get('/ruangan/{id}/pdf', [RuanganController::class, 'exportPdf'])->name('ruangan.pdf');

    // Staff Management
    Route::get('/staff', [StafAsetController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StafAsetController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StafAsetController::class, 'store'])->name('staff.store');
    Route::get('/staff/{id}/edit', [StafAsetController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{id}', [StafAsetController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{id}', [StafAsetController::class, 'destroy'])->name('staff.destroy');

    // Pindah barang
    Route::get('/laporan-pemindahan', [BarangController::class, 'laporan'])
    ->name('pemindahan.laporanpindahbarang');

    // Laporan periodik
    Route::get('/laporan/periodik', [BarangController::class, 'periodik'])->name('laporan.periodik');
    Route::get('/laporan/periodik/export', [BarangController::class, 'exportPeriodik'])
    ->name('laporan.periodik.export');
    });
//

