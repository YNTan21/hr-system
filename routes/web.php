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
use App\Http\Controllers\KPIController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\KPIEntryController;

use App\Http\Controllers\TimetableController;
use App\Http\Controllers\ShiftController;
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
        Route::get('/admin/attendance/create', [AttendanceController::class, 'create'])->name('admin.attendance.create');
        Route::post('/admin/attendance/store', [AttendanceController::class, 'store'])->name('admin.attendance.store');

        // kpi
        Route::get('/admin/kpi/index', [KPIController::class, 'index'])->name('admin.kpi.index');
        Route::get('/admin/kpi/create/{position_id}', [KPIController::class, 'create'])->name('admin.kpi.create');
        Route::post('/admin/kpi', [KPIController::class, 'store'])->name('admin.kpi.store');
        // Route::get('/admin/kpi/{id}/edit', [KPIController::class, 'edit'])->name('admin.kpi.edit');
        Route::put('/admin/kpi/{id}', [KPIController::class, 'update'])->name('admin.kpi.update');
        // Route::delete('/admin/kpi/{id}', [KPIController::class, 'destroy'])->name('admin.kpi.destroy');
        Route::get('/admin/kpi/manage/{position_id}', [KPIController::class, 'manage'])->name('admin.kpi.manage');

        // Rating Category
        Route::get('/admin/rating-category/index', [RatingCategoryController::class, 'index'])->name('admin.rating-category.index');

        // Route to create a category under a specific KPI
        // Route::post('/admin/kpi/{kpiId}/category/store', [CategoryController::class, 'store'])->name('category.store');

        // Route to add a goal to a category under a specific KPI
        // Route::post('/admin/kpi/{kpiId}/category/{categoryId}/goal/store', [GoalController::class, 'store'])->name('category.goal.store');

        // Goal
        Route::get('/admin/kpi/create/{position_id}', [GoalController::class, 'create'])->name('admin.kpi.create');
        Route::post('/admin/kpi/{position_id}', [GoalController::class, 'store'])->name('admin.kpi.store');
        Route::get('/admin/kpi/{position_id}/goal/{id}/edit', [GoalController::class, 'edit'])->name('admin.kpi.edit');
        Route::put('/admin/kpi/{position_id}/goal/{id}', [GoalController::class, 'update'])->name('admin.kpi.update');
        Route::delete('/admin/kpi/{goal_id}', [GoalController::class, 'destroy'])->name('admin.kpi.destroy');

        // kpi entry
        Route::get('/admin/kpi/kpiEntry/index', [KpiEntryController::class, 'index'])->name('admin.kpi.kpiEntry.index');
        Route::post('/admin/kpi/kpiEntry/store', [KpiEntryController::class, 'store'])->name('admin.kpi.kpiEntry.store');
        Route::get('/admin/kpi/kpiEntry/create', [KpiEntryController::class, 'create'])->name('admin.kpi.kpiEntry.create');
        Route::put('/admin/kpi/kpiEntry/{id}', [KpiEntryController::class, 'update'])->name('admin.kpi.kpiEntry.update');
        Route::get('/admin/kpi/kpiEntry/{id}', [KpiEntryController::class, 'show'])->name('admin.kpi.kpiEntry.show');
        Route::get('/admin/kpi/kpiEntry/{id}/edit', [KpiEntryController::class, 'edit'])->name('admin.kpi.kpiEntry.edit');
        Route::delete('/admin/kpi/kpiEntry/{id}', [KpiEntryController::class, 'destroy'])->name('admin.kpi.kpiEntry.destroy');
        // timetable
        Route::get('/admin/timetable/index', [TimetableController::class, 'index'])->name('admin.timetable.index');
        Route::get('/form/shiftlist/page', [ShiftController::class, 'shiftList'])->name('form/shiftlist/page');
        Route::post('/shifts/assign', [ShiftController::class, 'assign'])->name('shifts.assign');
        Route::post('form/shift/save', [App\Http\Controllers\ShiftController::class, 'saveRecord'])->name('form/shift/save');
        Route::get('/timetable', [App\Http\Controllers\TimetableController::class, 'index'])->name('timetable.index');
    Route::get('/timetable/create', [App\Http\Controllers\TimetableController::class, 'create'])->name('timetable.create');
    Route::post('/timetable', [App\Http\Controllers\TimetableController::class, 'store'])->name('timetable.store');
    Route::get('/timetable/{timetable}/edit', [App\Http\Controllers\TimetableController::class, 'edit'])->name('timetable.edit');
    Route::put('/timetable/{timetable}', [App\Http\Controllers\TimetableController::class, 'update'])->name('timetable.update');
    Route::delete('/timetable/{timetable}', [App\Http\Controllers\TimetableController::class, 'destroy'])->name('timetable.destroy');
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

Route::get('/shift-list', [ShiftController::class, 'shiftList'])->name('shift.list');




