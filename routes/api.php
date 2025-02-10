<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerprintClocklogsController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\AttendanceScheduleController;
use App\Http\Controllers\Auth\DashboardController;
use App\Http\Controllers\PinVerificationController;
use App\Http\Controllers\LeavePredictController;

Route::middleware('api')->group(function () {
    Route::post('/store-fingerprint', [FingerprintClocklogsController::class, 'storeFingerprint']);
    Route::post('/scan-fingerprint', [FingerprintClocklogsController::class, 'scanFingerprint']);
    Route::post('/clock-in-out', [FingerprintClocklogsController::class, 'clockInOut']);
    Route::get('/check-scanner-status', [FingerprintController::class, 'checkScannerStatus']);
    Route::post('/verify-pin', [PinVerificationController::class, 'verify']);
});

Route::get('/overtime-data/{month}', [DashboardController::class, 'getOvertimeData']);
Route::get('/overtime-data/{month}', [App\Http\Controllers\Auth\DashboardController::class, 'getOvertimeData']);

Route::get('/get-leave-data', [LeavePredictController::class, 'getLeaveData']);
Route::post('/store-predictions', [LeavePredictController::class, 'storePredictions']);
