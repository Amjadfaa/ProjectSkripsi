<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pemohon\PermohonanController;
use App\Http\Controllers\Pemohon\FormulirController;
use App\Http\Controllers\Verifikator\VerifikasiController;
use App\Http\Controllers\Administrator\DashboardAdminController;
use App\Http\Controllers\Administrator\KartuPasController;
use App\Http\Controllers\Administrator\FormulirAdminController;
use App\Http\Controllers\Administrator\PermohonanAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administrator\InstansiController;
use App\Http\Controllers\Administrator\BerkasPersyaratanController as AdminBerkasController;
use App\Http\Controllers\Pemohon\BerkasPersyaratanController as PemohonBerkasController;
use App\Http\Controllers\Administrator\MonitoringKuotaController;
use App\Http\Controllers\Administrator\LaporanController;
use App\Http\Controllers\Administrator\ImportController;
use App\Http\Controllers\Administrator\UserController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Pemohon
Route::middleware(['auth', 'role:pemohon'])->prefix('pemohon')->name('pemohon.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'pemohon'])->name('dashboard');
    Route::get('/permohonan', [PermohonanController::class, 'index'])->name('permohonan.index');
    Route::get('/permohonan/create', [PermohonanController::class, 'create'])->name('permohonan.create');
    Route::post('/permohonan', [PermohonanController::class, 'store'])->name('permohonan.store');
    Route::get('/permohonan/{id}', [PermohonanController::class, 'show'])->name('permohonan.show');
    Route::get('/berkas-persyaratan', [PemohonBerkasController::class, 'index'])->name('berkas-persyaratan.index');
    Route::get('/berkas-persyaratan/download/{id}', [PemohonBerkasController::class, 'download'])->name('berkas-persyaratan.download');
    Route::delete('/permohonan/{id}', [PermohonanController::class, 'destroy'])->name('permohonan.destroy');
});

// Administrator
Route::middleware(['auth', 'role:administrator'])->prefix('administrator')->name('administrator.')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::get('/permohonan', [PermohonanAdminController::class, 'index'])->name('permohonan.index');
    Route::get('/permohonan/{id}', [PermohonanAdminController::class, 'show'])->name('permohonan.show');
    Route::get('/kartu-pas', [KartuPasController::class, 'index'])->name('kartu-pas.index');
    Route::get('/kartu-pas/tambah', [KartuPasController::class, 'tambah'])->name('kartu-pas.tambah');
    Route::post('/kartu-pas/simpan', [KartuPasController::class, 'simpan'])->name('kartu-pas.simpan');
    Route::get('/kartu-pas/export/excel', [KartuPasController::class, 'exportExcel'])->name('kartu-pas.export.excel');
    Route::get('/kartu-pas/export/pdf', [KartuPasController::class, 'exportPdf'])->name('kartu-pas.export.pdf');
    Route::get('/kartu-pas/{id}/edit', [KartuPasController::class, 'edit'])->name('kartu-pas.edit');
    Route::put('/kartu-pas/{id}', [KartuPasController::class, 'update'])->name('kartu-pas.update');
    Route::delete('/kartu-pas/{id}', [KartuPasController::class, 'destroy'])->name('kartu-pas.destroy');
    Route::delete('/kartu-pas-destroy-all', [KartuPasController::class, 'destroyAll'])->name('kartu-pas.destroy-all');
    Route::delete('/kartu-pas-destroy-selected', [KartuPasController::class, 'destroySelected'])->name('kartu-pas.destroy-selected');
    Route::get('/berkas-persyaratan', [AdminBerkasController::class, 'index'])->name('berkas-persyaratan.index');
    Route::get('/berkas-persyaratan/create', [AdminBerkasController::class, 'create'])->name('berkas-persyaratan.create');
    Route::post('/berkas-persyaratan', [AdminBerkasController::class, 'store'])->name('berkas-persyaratan.store');
    Route::put('/berkas-persyaratan/{id}/toggle', [AdminBerkasController::class, 'toggle'])->name('berkas-persyaratan.toggle');
    Route::delete('/berkas-persyaratan/{id}', [AdminBerkasController::class, 'destroy'])->name('berkas-persyaratan.destroy');
    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    // Manajemen User
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Monitoring Kuota
    Route::get('/monitoring-kuota', [MonitoringKuotaController::class, 'index'])->name('monitoring-kuota.index');
    Route::get('/monitoring-kuota/{id}', [MonitoringKuotaController::class, 'show'])->name('monitoring-kuota.show');
    Route::post('/monitoring-kuota/nonaktifkan/{id}', [MonitoringKuotaController::class, 'nonaktifkan'])->name('monitoring-kuota.nonaktifkan');
    Route::put('/monitoring-kuota/kuota/{id}', [MonitoringKuotaController::class, 'updateKuota'])->name('monitoring-kuota.update-kuota'); 

    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/kartu-pas', [ImportController::class, 'importKartuPas'])->name('import.kartu-pas');
        
    // Instansi
    Route::get('/instansi', [InstansiController::class, 'index'])->name('instansi.index');
    Route::get('/instansi/create', [InstansiController::class, 'create'])->name('instansi.create');
    Route::post('/instansi', [InstansiController::class, 'store'])->name('instansi.store');
    Route::get('/instansi/{id}/edit', [InstansiController::class, 'edit'])->name('instansi.edit');
    Route::put('/instansi/{id}', [InstansiController::class, 'update'])->name('instansi.update');
    Route::delete('/instansi/{id}', [InstansiController::class, 'destroy'])->name('instansi.destroy');
    });

    // Verifikator
    Route::middleware(['auth', 'role:verifikator'])->prefix('verifikator')->name('verifikator.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'verifikator'])->name('dashboard');
        Route::get('/permohonan', [VerifikasiController::class, 'index'])->name('permohonan.index');
        Route::get('/permohonan/{id}', [VerifikasiController::class, 'show'])->name('permohonan.show');
        Route::post('/permohonan/{id}/verifikasi', [VerifikasiController::class, 'verifikasi'])->name('permohonan.verifikasi');
    });

    Route::get('/reset-password-manual', [App\Http\Controllers\Auth\ResetPasswordManualController::class, 'create'])->name('reset.manual');
    Route::post('/reset-password-manual', [App\Http\Controllers\Auth\ResetPasswordManualController::class, 'store'])->name('reset.manual.store');

    require __DIR__.'/auth.php';