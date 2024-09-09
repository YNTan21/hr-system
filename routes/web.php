<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LeaveTypeController;

// home
Route::view('/', 'home')->name('home');

Route::middleware('auth')->group(function(){
    // logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    // dashboard
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard.index');

    // leaveType (new)
    Route::get('/leaveType/create', [PageController::class, 'leaveType_create'])->name('leaveType.create');
    Route::post('/leaveType', [LeaveTypeController::class, 'store'])->name('leaveType.store');
    Route::get('/leaveType/index', [LeaveTypeController::class, 'index'])->name('leaveType.index');
    Route::get('/leaveType/{id}/edit', [LeaveTypeController::class, 'edit'])->name('leaveType.edit');
    Route::put('/leaveType/{id}', [LeaveTypeController::class, 'update'])->name('leaveType.update');
    Route::delete('/leaveType/{id}', [LeaveTypeController::class, 'destroy'])->name('leaveType.destroy');
});

Route::middleware('guest')->group(function(){
    // register
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    // login
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

