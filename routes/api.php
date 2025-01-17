<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerprintClocklogsController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\AttendanceScheduleController;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware('api')->group(function () {
    Route::post('/store-fingerprint', [FingerprintClocklogsController::class, 'storeFingerprint']);
    Route::post('/scan-fingerprint', [FingerprintClocklogsController::class, 'scanFingerprint']);
    Route::post('/clock-in-out', [FingerprintClocklogsController::class, 'clockInOut']);
    Route::get('/check-scanner-status', [FingerprintController::class, 'checkScannerStatus']);
});

Route::get('/overtime-data/{month}', [App\Http\Controllers\Auth\DashboardController::class, 'getOvertimeData']);
