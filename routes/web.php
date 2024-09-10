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
    Route::get('/admin/leaveType/create', [PageController::class, 'leaveType_create'])->name('admin.leaveType.create');
    Route::post('/admin/leaveType', [LeaveTypeController::class, 'store'])->name('admin.leaveType.store');
    Route::get('/admin/leaveType/index', [LeaveTypeController::class, 'index'])->name('admin.leaveType.index');
    Route::get('/admin/leaveType/{id}/edit', [LeaveTypeController::class, 'edit'])->name('admin.leaveType.edit');
    Route::put('/admin/leaveType/{id}', [LeaveTypeController::class, 'update'])->name('admin.leaveType.update');
    Route::delete('/admin/leaveType/{id}', [LeaveTypeController::class, 'destroy'])->name('admin.leaveType.destroy');
});

Route::middleware('guest')->group(function(){
    // register
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    // login
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

