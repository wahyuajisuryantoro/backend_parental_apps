<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\AkunOrangTuaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrangTuaController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;








// Route auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Route untuk orang tua dengan prefix
    Route::prefix('orang-tua')->group(function () {
        Route::get('/detail', [OrangTuaController::class, 'getOrangTua']);
        Route::get('/anak', [OrangTuaController::class, 'getAnak']);
        Route::get('/pengajuan-absensi', [OrangTuaController::class, 'getPengajuanAbsensi']);
    });

    Route::get('/siswa/{id_siswa}', [SiswaController::class, 'getDataSiswaById']);
   
    Route::get('/dashboard', [DashboardController::class, 'getDashboardData']);

    Route::get('/presensi/siswa', [DashboardController::class, 'getPresensiSiswa']);
    Route::get('/presensi/{id_siswa}', [PresensiController::class, 'getPresensiBySiswaID']);

    Route::get('/riwayat-absensi', [AbsensiController::class, 'listAbsensiSiswaByLoginUser']);
    Route::post('/post-absensi/{id_siswa}', [AbsensiController::class, 'postAbsensiByIdSiswa']);

    Route::get('/akun', [AkunOrangTuaController::class, 'getAkunOrangTua']);
    Route::post('/akun/check-username', [AkunOrangTuaController::class, 'checkUsername']);
    Route::post('/akun/update', [AkunOrangTuaController::class, 'updateProfile']);

    // Route logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
