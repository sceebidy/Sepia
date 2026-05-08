<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FolderController;

// ── Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// ── Data Pool / Folder
Route::prefix('datapool')->name('datapool.')->group(function () {
    Route::get('/',                [FolderController::class, 'index'])->name('index');
    Route::post('/',               [FolderController::class, 'store'])->name('store');
    Route::get('/{folder}',        [FolderController::class, 'show'])->name('show');
    Route::delete('/{folder}',     [FolderController::class, 'destroy'])->name('destroy');
    Route::post('/{folder}/items', [FolderController::class, 'addItem'])->name('addItem');
    Route::delete('/items/{item}', [FolderController::class, 'deleteItem'])->name('deleteItem');
});