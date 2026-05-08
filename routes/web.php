<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;

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