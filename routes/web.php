<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Administrator\DashboardAdminController;
use App\Http\Controllers\Administrator\KartuPasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Administrator\InstansiController;
use App\Http\Controllers\Administrator\MonitoringKuotaController;
use App\Http\Controllers\Administrator\LaporanController;
use App\Http\Controllers\Administrator\ImportController;
use App\Http\Controllers\Administrator\MasterOptionController;
use App\Http\Controllers\Administrator\CameraDeviceController;
use App\Http\Controllers\ScanController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('administrator.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Administrator
Route::middleware(['auth', 'role:administrator'])->prefix('administrator')->name('administrator.')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
    
    // Kartu PAS
    Route::get('/kartu-pas', [KartuPasController::class, 'index'])->name('kartu-pas.index');
    Route::get('/kartu-pas/tambah', [KartuPasController::class, 'tambah'])->name('kartu-pas.tambah');
    Route::post('/kartu-pas/simpan', [KartuPasController::class, 'simpan'])->name('kartu-pas.simpan');
    Route::get('/kartu-pas/export/excel', [KartuPasController::class, 'exportExcel'])->name('kartu-pas.export.excel');
    Route::get('/kartu-pas/export/pdf', [KartuPasController::class, 'exportPdf'])->name('kartu-pas.export.pdf');
    Route::get('/kartu-pas/{id}/qrcode', [KartuPasController::class, 'downloadQrCode'])->name('kartu-pas.qrcode');
    Route::get('/kartu-pas/{id}/edit', [KartuPasController::class, 'edit'])->name('kartu-pas.edit');
    Route::put('/kartu-pas/{id}', [KartuPasController::class, 'update'])->name('kartu-pas.update');
    Route::delete('/kartu-pas/{id}', [KartuPasController::class, 'destroy'])->name('kartu-pas.destroy');
    Route::delete('/kartu-pas-destroy-all', [KartuPasController::class, 'destroyAll'])->name('kartu-pas.destroy-all');
    Route::delete('/kartu-pas-destroy-selected', [KartuPasController::class, 'destroySelected'])->name('kartu-pas.destroy-selected');

    // Laporan Kartu PAS
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

    // Laporan Aktivitas Area Akses
    Route::get('/laporan-aktivitas', [\App\Http\Controllers\Administrator\LaporanAktivitasController::class, 'index'])->name('laporan-aktivitas.index');
    Route::get('/laporan-aktivitas/export/excel', [\App\Http\Controllers\Administrator\LaporanAktivitasController::class, 'exportExcel'])->name('laporan-aktivitas.export.excel');
    Route::get('/laporan-aktivitas/export/pdf', [\App\Http\Controllers\Administrator\LaporanAktivitasController::class, 'exportPdf'])->name('laporan-aktivitas.export.pdf');

    // Monitoring Kuota
    Route::get('/monitoring-kuota', [MonitoringKuotaController::class, 'index'])->name('monitoring-kuota.index');
    Route::get('/monitoring-kuota/{id}', [MonitoringKuotaController::class, 'show'])->name('monitoring-kuota.show');
    Route::get('/monitoring-kuota/{id}/detail-ajax', [MonitoringKuotaController::class, 'getDetailAjax'])->name('monitoring-kuota.detail-ajax');
    Route::post('/monitoring-kuota/nonaktifkan/{id}', [MonitoringKuotaController::class, 'nonaktifkan'])->name('monitoring-kuota.nonaktifkan');
    Route::put('/monitoring-kuota/kuota/{id}', [MonitoringKuotaController::class, 'updateKuota'])->name('monitoring-kuota.update-kuota'); 

    // Import
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/kartu-pas', [ImportController::class, 'importKartuPas'])->name('import.kartu-pas');
        
    // Master Options AJAX
    Route::post('/area-akses/store-ajax', [MasterOptionController::class, 'storeAreaAkses'])->name('area-akses.store-ajax');
    Route::post('/area-akses/delete-ajax', [MasterOptionController::class, 'deleteAreaAkses'])->name('area-akses.delete-ajax');
    Route::post('/jabatan/store-ajax', [MasterOptionController::class, 'storeJabatan'])->name('jabatan.store-ajax');
    Route::post('/jabatan/delete-ajax', [MasterOptionController::class, 'deleteJabatan'])->name('jabatan.delete-ajax');

    // Instansi
    Route::get('/instansi', [InstansiController::class, 'index'])->name('instansi.index');
    Route::get('/instansi/create', [InstansiController::class, 'create'])->name('instansi.create');
    Route::post('/instansi', [InstansiController::class, 'store'])->name('instansi.store');
    Route::get('/instansi/{id}/edit', [InstansiController::class, 'edit'])->name('instansi.edit');
    Route::put('/instansi/{id}', [InstansiController::class, 'update'])->name('instansi.update');
    Route::delete('/instansi/{id}', [InstansiController::class, 'destroy'])->name('instansi.destroy');

    // Perangkat Kamera (Admin Setting)
    Route::get('/perangkat-kamera', [CameraDeviceController::class, 'index'])->name('perangkat-kamera.index');
    Route::post('/perangkat-kamera', [CameraDeviceController::class, 'store'])->name('perangkat-kamera.store');
    Route::put('/perangkat-kamera/{id}', [CameraDeviceController::class, 'update'])->name('perangkat-kamera.update');
    Route::delete('/perangkat-kamera/{id}', [CameraDeviceController::class, 'destroy'])->name('perangkat-kamera.destroy');
});

// Camera Scan Login (no auth required)
Route::get('/scan/login', [ScanController::class, 'loginForm'])->name('scan.login');
Route::post('/scan/login', [ScanController::class, 'login'])->name('scan.login.submit');
Route::get('/scan/scanner', [ScanController::class, 'scanner'])->name('scan.scanner');
Route::post('/scan/process', [ScanController::class, 'processScan'])->name('scan.process');
Route::post('/scan/catatan/{id}', [ScanController::class, 'updateCatatan'])->name('scan.catatan');
Route::post('/scan/logout', [ScanController::class, 'logout'])->name('scan.logout');

Route::get('/reset-password-manual', [App\Http\Controllers\Auth\ResetPasswordManualController::class, 'create'])->name('reset.manual');
Route::post('/reset-password-manual', [App\Http\Controllers\Auth\ResetPasswordManualController::class, 'store'])->name('reset.manual.store');

// Route Uji Coba Kirim Email (Untuk Server Hosting / cPanel)
Route::get('/test-email', function (\Illuminate\Http\Request $request) {
    $to = $request->query('to', env('MAIL_FROM_ADDRESS', 'admin@example.com'));
    
    try {
        \Illuminate\Support\Facades\Mail::raw("Halo! Ini tes sukses pengiriman email dari MONPASKU Online.\nDikirim pada: " . now()->format('d/m/Y H:i:s'), function ($message) use ($to) {
            $message->to($to)->subject('✅ Tes Pengiriman Email MONPASKU Online');
        });
        
        return response()->json([
            'status' => 'SUCCESS',
            'message' => "Email tes BERHASIL dikirim ke [{$to}]! Silakan cek folder Inbox atau Spam.",
            'from' => config('mail.from.address'),
            'mailer' => config('mail.default'),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'ERROR',
            'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            'from' => config('mail.from.address'),
            'mailer' => config('mail.default'),
        ], 500);
    }
});

require __DIR__.'/auth.php';