<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\GoogleSearchController;
use App\Http\Controllers\ExportDocxController;
use App\Http\Controllers\N8nController;

// ── Dashboard ──────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// ── Data Pool / RPI ────────────────────────────
Route::get('/datapool',  [FolderController::class, 'index'])->name('datapool.index');
Route::post('/datapool', [FolderController::class, 'store'])->name('datapool.store');

// Items — harus di atas /{folder}
Route::patch('/datapool/items/{item}',  [FolderController::class, 'updateItem'])->name('datapool.items.update');
Route::delete('/datapool/items/{item}', [FolderController::class, 'deleteItem'])->name('datapool.items.delete');

// Folder
Route::get('/datapool/{folder}',        [FolderController::class, 'show'])->name('datapool.show');
Route::put('/datapool/{folder}',        [FolderController::class, 'update'])->name('datapool.update');
Route::delete('/datapool/{folder}',     [FolderController::class, 'destroy'])->name('datapool.destroy');
Route::post('/datapool/{folder}/items', [FolderController::class, 'addItem'])->name('datapool.items.store');

// ── N8n Integration ────────────────────────────
Route::post('/n8n/process/{item}',  [N8nController::class, 'process'])->name('n8n.process');
Route::post('/n8n/callback',        [N8nController::class, 'callback'])->name('n8n.callback');
Route::post('/n8n/test-callback',   [N8nController::class, 'testCallback'])->name('n8n.test-callback');
Route::get('/n8n/status/{aiUsage}', [N8nController::class, 'status'])->name('n8n.status');

// ── Analisis Kasus ─────────────────────────────
Route::prefix('datapool/{folder}/analisis')->name('analisis.')->group(function () {
    Route::post('/',                       [AnalisisController::class,  'store'])->name('store');
    Route::get('/{analisis}',              [AnalisisController::class,  'show'])->name('show');
    Route::get('/{analisis}/export-docx',  [ExportDocxController::class, 'export'])->name('export.docx');
});

Route::post('/analisis/callback', [AnalisisController::class, 'callback'])->name('analisis.callback');

// ── Distribusi ─────────────────────────────────
Route::get('/datapool/{folder}/distribusi/{analisis}',          [DistribusiController::class, 'show'])->name('distribusi.show');
Route::post('/datapool/{folder}/distribusi/{analisis}/generate', [DistribusiController::class, 'generate'])->name('distribusi.generate');

// ── Laporan ────────────────────────────────────
Route::get('/laporan',                                          [LaporanController::class, 'index'])->name('laporan.index');
Route::get('/laporan/{laporan}',                                [LaporanController::class, 'show'])->name('laporan.show');
Route::post('/datapool/{folder}/laporan/{analisis}',            [LaporanController::class, 'store'])->name('laporan.store');
Route::get('/datapool/{folder}/laporan/{analisis}/buat',        [LaporanController::class, 'store'])->name('laporan.buat');

// ── Google Search ──────────────────────────────
Route::get('/datapool/{folder}/search',         [GoogleSearchController::class, 'search'])->name('google.search');
Route::post('/datapool/{folder}/search/simpan', [GoogleSearchController::class, 'simpan'])->name('google.simpan');


Route::patch('/datapool/{folder}/analisis/{analisis}/update-info', [AnalisisController::class, 'updateInfo'])->name('analisis.update');

Route::view('/penjabaran-strategis', 'penjabaran-strategis')->name('penjabaran-strategis');
Route::view('/laporan-informasi', 'laporan-informasi')->name('laporan-informasi');
Route::view('/laporan-intelijen', 'laporan-intelijen')->name('laporan-intelijen');
Route::view('/infografis-intelijen', 'infografis-intelijen')->name('infografis-intelijen');
Route::view('/profiling-subjek', 'profiling-subjek')->name('profiling-subjek');
Route::view('/presentasi-intelijen', 'presentasi-intelijen')->name('presentasi-intelijen');
