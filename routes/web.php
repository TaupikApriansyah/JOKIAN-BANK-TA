<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Akuntan\AkunController;
use App\Http\Controllers\Akuntan\DashboardController as AkuntanDashboard;
use App\Http\Controllers\Akuntan\JurnalController;
use App\Http\Controllers\Akuntan\LaporanController as AkuntanLaporan;
use App\Http\Controllers\Akuntan\KasKecilController;
use App\Http\Controllers\Akuntan\TransaksiController as AkuntanTransaksi;
use App\Http\Controllers\CS\ArsipDigitalController;
use App\Http\Controllers\CS\BerkasController;
use App\Http\Controllers\CS\DashboardController as CsDashboard;
use App\Http\Controllers\CS\NasabahController;
use App\Http\Controllers\CS\TrackingStatusController;
use App\Http\Controllers\DokumenBuktiController;
use App\Http\Controllers\CS\TransaksiAdministrasiController;

Route::get('/', fn () => redirect('/login'));
// Pendaftaran mandiri dan reset publik dimatikan. Akun dibuat oleh Admin.
Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');


// Bukti transaksi disimpan privat dan hanya dapat diunduh oleh CS pemilik berkas atau Akuntan.
Route::middleware(['auth', 'checkrole:cs,akuntan'])->get('/dokumen/bukti/{id}/download', [DokumenBuktiController::class, 'download'])->name('dokumen.bukti.download');

Route::middleware(['auth', 'checkrole:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class);

    Route::get('/monitoring/berkas', [MonitoringController::class, 'berkas'])->name('monitoring.berkas');
    Route::get('/monitoring/berkas/{id}', [MonitoringController::class, 'show'])->name('monitoring.berkas.show');
    Route::get('/monitoring/tracking', [MonitoringController::class, 'tracking'])->name('monitoring.tracking');
    Route::get('/monitoring/tracking/{id}', [MonitoringController::class, 'showTracking'])->name('monitoring.tracking.show');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
});

Route::middleware(['auth', 'checkrole:cs'])->prefix('cs')->name('cs.')->group(function () {
    Route::get('/dashboard', [CsDashboard::class, 'index'])->name('dashboard');
    Route::resource('nasabah', NasabahController::class);
    Route::resource('berkas', BerkasController::class);
    Route::put('/berkas/{id_berkas}/update-status', [BerkasController::class, 'updateStatus'])->name('berkas.updateStatus');

    Route::get('/arsip', [ArsipDigitalController::class, 'index'])->name('arsip.index');
    Route::get('/arsip/create', [ArsipDigitalController::class, 'create'])->name('arsip.create');
    Route::post('/arsip', [ArsipDigitalController::class, 'store'])->name('arsip.store');
    Route::get('/arsip/export/excel', [ArsipDigitalController::class, 'exportExcel'])->name('arsip.export.excel');
    Route::get('/arsip/export/pdf', [ArsipDigitalController::class, 'exportPdf'])->name('arsip.export.pdf');
    Route::get('/arsip/{id}/download', [ArsipDigitalController::class, 'download'])->name('arsip.download');
    Route::get('/arsip/{id}/edit', [ArsipDigitalController::class, 'edit'])->name('arsip.edit');
    Route::put('/arsip/{id}', [ArsipDigitalController::class, 'update'])->name('arsip.update');
    Route::delete('/arsip/{id}', [ArsipDigitalController::class, 'destroy'])->name('arsip.destroy');
    Route::get('/berkas/{id_berkas}/arsip', [ArsipDigitalController::class, 'perBerkas'])->name('arsip.perberkas');

    Route::resource('tracking', TrackingStatusController::class);
    Route::get('/berkas/{id_berkas}/transaksi', [TransaksiAdministrasiController::class, 'perBerkas'])->name('transaksi.perberkas');
    Route::resource('transaksi', TransaksiAdministrasiController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
});

// CS menginput transaksi, kemudian Akuntan memverifikasi dan memposting jurnal.
Route::middleware(['auth', 'checkrole:akuntan'])->prefix('akuntan')->name('akuntan.')->group(function () {
    Route::get('/dashboard', [AkuntanDashboard::class, 'index'])->name('dashboard');

    Route::get('/verifikasi-transaksi', [AkuntanTransaksi::class, 'index'])->name('transaksi.index');
    Route::post('/verifikasi-transaksi/{id}/posting', [AkuntanTransaksi::class, 'post'])->name('transaksi.post');
    Route::post('/verifikasi-transaksi/{id}/tolak', [AkuntanTransaksi::class, 'reject'])->name('transaksi.reject');

    Route::get('/daftar-akun', [AkunController::class, 'index'])->name('akun.index');
    Route::post('/daftar-akun', [AkunController::class, 'store'])->name('akun.store');
    Route::put('/daftar-akun/{id}', [AkunController::class, 'update'])->name('akun.update');

    Route::get('/kas-kecil', [KasKecilController::class, 'index'])->name('kas-kecil.index');
    Route::post('/kas-kecil', [KasKecilController::class, 'store'])->name('kas-kecil.store');
    Route::put('/kas-kecil/{id}', [KasKecilController::class, 'update'])->name('kas-kecil.update');
    Route::delete('/kas-kecil/{id}', [KasKecilController::class, 'destroy'])->name('kas-kecil.destroy');

    Route::get('/jurnal-umum', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/buku-besar', [JurnalController::class, 'ledger'])->name('jurnal.ledger');

    Route::get('/laporan-akuntansi', [AkuntanLaporan::class, 'index'])->name('laporan.index');
    Route::get('/laporan-akuntansi/export-excel', [AkuntanLaporan::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan-akuntansi/export-pdf', [AkuntanLaporan::class, 'exportPdf'])->name('laporan.pdf');
});
