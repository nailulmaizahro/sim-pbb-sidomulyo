<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortalWargaController;
use App\Http\Controllers\WajibPajakController;
use App\Http\Controllers\ObjekPajakController;
use App\Http\Controllers\SpptController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SimulasiUtsController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi PBB Kampung Sidomulyo Punggur
|--------------------------------------------------------------------------
*/

// ======================== PORTAL PUBLIK WARGA ========================
Route::get('/', [PortalWargaController::class, 'index'])->name('portal.home');
Route::any('/cek-tagihan', [PortalWargaController::class, 'cekTagihan'])->name('portal.cek');
Route::get('/upload-bukti/{id_sppt}', [PortalWargaController::class, 'formUpload'])->name('portal.upload.form');
Route::post('/upload-bukti', [PortalWargaController::class, 'simpanUpload'])->name('portal.upload.simpan');
Route::get('/pembayaran/status/{kode}', [PortalWargaController::class, 'statusPembayaran'])->name('portal.pembayaran.status');
Route::get('/pembayaran/check-status/{kode}', [PortalWargaController::class, 'checkStatusApi'])->name('portal.pembayaran.check-status');

// ======================== MODE SIMULASI PEMBAYARAN UTS ========================
Route::get('/simulasi-pembayaran/{id_sppt}', [SimulasiUtsController::class, 'formSimulasi'])->name('portal.simulasi.form');
Route::post('/simulasi-pembayaran/create', [SimulasiUtsController::class, 'createTransaction'])->name('portal.simulasi.create');
Route::post('/simulasi-pembayaran/verify', [SimulasiUtsController::class, 'verifyPayment'])->name('portal.simulasi.verify');
Route::post('/simulasi-pembayaran/notification', [SimulasiUtsController::class, 'notification'])->name('portal.simulasi.notification');
Route::get('/simulasi-pembayaran/{id}/kuitansi', [SimulasiUtsController::class, 'cetakKuitansi'])->name('portal.simulasi.kuitansi');
Route::post('/simulasi-pembayaran/konfirmasi-dana', [SimulasiUtsController::class, 'konfirmasiDana'])->name('portal.simulasi.konfirmasi-dana');
Route::get('/simulasi-pembayaran/status/{id}', [SimulasiUtsController::class, 'statusSimulasi'])->name('portal.simulasi.status');
Route::get('/simulasi-pembayaran/check-status/{id}', [SimulasiUtsController::class, 'checkStatusApi'])->name('portal.simulasi.check-status');

// ======================== AUTENTIKASI PETUGAS ========================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ======================== AREA SISTEM (LOGIN REQUIRED) ========================
Route::middleware(['auth'])->group(function () {
    // Dashboard & Monitoring
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaksi Pembayaran & Verifikasi Loket
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::post('/pembayaran/{id}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::post('/pembayaran/{id}/tolak', [PembayaranController::class, 'tolak'])->name('pembayaran.tolak');
    Route::get('/pembayaran/{id}/kuitansi', [PembayaranController::class, 'cetakKuitansi'])->name('pembayaran.kuitansi');
    Route::post('/pembayaran/simulasi/{id}/verifikasi-manual', [SimulasiUtsController::class, 'verifikasiManual'])->name('pembayaran.simulasi.verifikasi-manual');

    // Manajemen SPPT / DHKP
    Route::get('/sppt', [SpptController::class, 'index'])->name('sppt.index');
    Route::get('/sppt/create', [SpptController::class, 'create'])->name('sppt.create');
    Route::post('/sppt', [SpptController::class, 'store'])->name('sppt.store');
    Route::get('/sppt/import', [SpptController::class, 'importForm'])->name('sppt.import');
    Route::post('/sppt/import', [SpptController::class, 'importProcess'])->name('sppt.import.process');

    // Master Data Wajib Pajak & Objek Pajak
    Route::resource('wajib-pajak', WajibPajakController::class);
    Route::resource('objek-pajak', ObjekPajakController::class);

    // Laporan & Rekapitulasi Dispenda
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/realisasi', [LaporanController::class, 'realisasi'])->name('laporan.realisasi');
    Route::get('/laporan/tunggakan', [LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
    Route::get('/laporan/dispenda', [LaporanController::class, 'dispenda'])->name('laporan.dispenda');
});
