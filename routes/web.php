<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\AnalisisController;

// ── Dashboard ──────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// ── Data Pool / RPI ────────────────────────────
// URUTAN PENTING: route spesifik (items) harus di atas route wildcard ({folder})

Route::get('/datapool',                            [FolderController::class, 'index'])->name('datapool.index');
Route::post('/datapool',                           [FolderController::class, 'store'])->name('datapool.store');

// Items — harus di atas /{folder} supaya 'items' tidak di-resolve sebagai folder ID
Route::patch('/datapool/items/{item}',             [FolderController::class, 'updateItem'])->name('datapool.items.update');
Route::delete('/datapool/items/{item}',            [FolderController::class, 'deleteItem'])->name('datapool.items.delete');

// Folder
Route::get('/datapool/{folder}',                   [FolderController::class, 'show'])->name('datapool.show');
Route::put('/datapool/{folder}',                   [FolderController::class, 'update'])->name('datapool.update');
Route::delete('/datapool/{folder}',                [FolderController::class, 'destroy'])->name('datapool.destroy');
Route::post('/datapool/{folder}/items',            [FolderController::class, 'addItem'])->name('datapool.items.store');

// N8n Integration
Route::post('/n8n/process/{item}', [N8nController::class, 'process'])->name('n8n.process');
Route::post('/n8n/callback',       [N8nController::class, 'callback'])->name('n8n.callback');
Route::post('/n8n/test-callback',  [N8nController::class, 'testCallback'])->name('n8n.test-callback');
Route::get('/n8n/status/{aiUsage}',[N8nController::class, 'status'])->name('n8n.status');

// ── Analisis Kasus
Route::prefix('datapool/{folder}/analisis')->name('analisis.')->group(function () {
    Route::post('/',          [AnalisisController::class, 'store'])->name('store');
    Route::get('/{analisis}', [AnalisisController::class, 'show'])->name('show');
});

Route::post('/analisis/callback', [AnalisisController::class, 'callback'])->name('analisis.callback');