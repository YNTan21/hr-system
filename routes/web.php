<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\UserLeaveController;
// home
Route::view('/', 'home')->name('home');

Route::middleware('auth')->group(function(){
    // logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    // admin dashboard
    Route::get('/admin/dashboard', [PageController::class, 'dashboard'])->name('admin.dashboard.index');

    // admin dashboard
    Route::get('/user/dashboard', [PageController::class, 'user_dashboard'])->name('user.dashboard');

    Route::middleware('is-admin')->group(function()
    {   // leaveType 
        Route::get('/admin/leaveType/create', [PageController::class, 'leaveType_create'])->name('admin.leaveType.create');
        Route::post('/admin/leaveType', [LeaveTypeController::class, 'store'])->name('admin.leaveType.store');
        Route::get('/admin/leaveType/index', [LeaveTypeController::class, 'index'])->name('admin.leaveType.index');
        Route::get('/admin/leaveType/{id}/edit', [LeaveTypeController::class, 'edit'])->name('admin.leaveType.edit');
        Route::put('/admin/leaveType/{id}', [LeaveTypeController::class, 'update'])->name('admin.leaveType.update');
        Route::delete('/admin/leaveType/{id}', [LeaveTypeController::class, 'destroy'])->name('admin.leaveType.destroy');

        // leave
        Route::get('/admin/leave/create', [LeaveController::class, 'create'])->name('admin.leave.create');
        Route::post('/admin/leave', [LeaveController::class, 'store'])->name('admin.leave.store');
        Route::get('/admin/leave/index', [LeaveController::class, 'index'])->name('admin.leave.index');
        Route::get('/admin/leave/{id}/edit', [LeaveController::class, 'edit'])->name('admin.leave.edit');
        Route::put('/admin/leave/{id}', [LeaveController::class, 'update'])->name('admin.leave.update');
    });

    // leave
    Route::get('/user/leave/create', [UserLeaveController::class, 'create'])->name('user.leave.create');
    Route::post('/user/leave', [UserLeaveController::class, 'store'])->name('user.leave.store');
    Route::get('/user/leave/index', [UserLeaveController::class, 'index'])->name('user.leave.index');
    Route::get('/user/leave/{id}/edit', [UserLeaveController::class, 'edit'])->name('user.leave.edit');
    Route::put('/user/leave/{id}', [UserLeaveController::class, 'update'])->name('user.leave.update');
});

Route::middleware('guest')->group(function(){
    // register
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

    // login
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

