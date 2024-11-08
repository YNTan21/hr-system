<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\UserLeaveController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PositionController;
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
        Route::get('/admin/leave/leave-balance', [LeaveController::class, 'leaveBalance'])->name('admin.leave.leave-balance');
        Route::get('/admin/leave/process', [LeaveController::class, 'processLeaves'])->name('admin.leave.process');
        // Route::get('/admin/leave/{id}/edit', [LeaveController::class, 'edit'])->name('admin.leave.edit');
        // Route::put('/admin/leave/{id}', [LeaveController::class, 'update'])->name('admin.leave.update');
        Route::put('/admin/leave/{leave}/approve', [LeaveController::class, 'approve'])->name('admin.leave.approve');
        Route::put('/admin/leave/{leave}/reject', [LeaveController::class, 'reject'])->name('admin.leave.reject');  
        Route::get('/admin/leave/{leave}', [LeaveController::class, 'show'])->name('admin.leave.show');

        // employee
        Route::get('/admin/employee/index', [EmployeeController::class, 'index'])->name('admin.employee.index');
        Route::get('/admin/employee/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employee.edit');
        Route::put('/admin/employee/{id}', [EmployeeController::class, 'update'])->name('admin.employee.update');
        Route::get('/admin/employee/create', [EmployeeController::class, 'create'])->name('admin.employee.create');
        Route::get('/admin/employee/sCreate', [EmployeeController::class, 'sCreate'])->name('admin.employee.sCreate');
        Route::post('/admin/employee', [EmployeeController::class, 'store'])->name('admin.employee.store');
        Route::get('/admin/employee/{id}', [EmployeeController::class, 'show'])->name('admin.employee.show');
        Route::delete('/admin/employee/{id}', [EmployeeController::class, 'destroy'])->name('admin.employee.destroy');
        Route::get('/admin/employee/{employee}/edit-password', [EmployeeController::class, 'editPassword'])->name('admin.employee.edit-password');
        Route::put('/admin/employee/{employee}/update-password', [EmployeeController::class, 'updatePassword'])->name('admin.employee.update-password');

        // position
        Route::get('/admin/employee/positions/index', [PositionController::class, 'index'])->name('admin.employee.positions.index');
        Route::get('/admin/employee/positions/create', [PositionController::class, 'create'])->name('admin.employee.positions.create');
        Route::post('/admin/employee/positions', [PositionController::class, 'store'])->name('admin.employee.positions.store');
        Route::get('/admin/employee/positions/{id}/edit', [PositionController::class, 'edit'])->name('admin.employee.positions.edit');
        Route::put('/admin/employee/positions/{id}', [PositionController::class, 'update'])->name('admin.employee.positions.update');
        Route::delete('/admin/employee/positions/{id}', [PositionController::class, 'destroy'])->name('admin.employee.positions.destroy');

        //attendance
        // Route to view the attendance page
        Route::get('/admin/attendance/index', [AttendanceController::class, 'index'])->name('admin.attendance.index');
        // Route to view the latetime page
        Route::get('/attendance/latetime', [AttendanceController::class, 'indexLatetime'])->name('attendance.latetime');
        // Route to assign attendance to an employee
        Route::post('/attendance/assign', [AttendanceController::class, 'assign'])->name('attendance.assign');

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

