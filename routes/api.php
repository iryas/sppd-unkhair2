<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'verifyApiKey'], function () {
    Route::controller(App\Http\Controllers\Api\SpdController::class)->group(function () {
        Route::post('/sppd', 'index')->name('api.sppd.index');
    });

    Route::controller(App\Http\Controllers\Api\StdController::class)->group(function () {
        Route::post('/std', 'index')->name('api.std.index');
    });

    // Integrasi SIMPEG (absensi): pegawai yang sedang tugas dinas (luar & dalam kota)
    Route::get('/dinas', [App\Http\Controllers\Api\DinasController::class, 'index'])->name('api.dinas.index');
    // Pencarian surat tugas berdasarkan NIP dan/atau nomor surat
    Route::get('/dinas/cari', [App\Http\Controllers\Api\DinasController::class, 'cari'])->name('api.dinas.cari');
});
