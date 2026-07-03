<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LateTimeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OverTimeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeAssetController;
use App\Http\Controllers\AssetRequestController;
use App\Http\Controllers\UserAttendanceController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveAllocationController;
use App\Http\Controllers\EmployeeLeaveAllocationController;



Route::get('/', function () {
    return view('welcome');
});



// ─────────────────────────────────────────────
// SUPER ADMIN
// ─────────────────────────────────────────────
Route::middleware('superadmin')->prefix('super')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('super.dashboard');
    Route::get('/sample', [AdminController::class, 'sample'])->name('view.sample');

    Route::resource('employee', EmployeeController::class);
    Route::resource('department', DepartmentController::class);
    Route::resource('designation', DesignationController::class);
    Route::resource('payroll', PayrollController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('user', UserController::class);
    Route::resource('attendance', AttendanceController::class);
    Route::resource('schedule', ScheduleController::class);

    // Leaves (resource covers index/create/store/edit/update/destroy)
    // Leave Management
    Route::prefix('leaves')->group(function () {

        Route::get('/', [LeaveController::class, 'index'])
            ->name('leaves.index');

        Route::get('/create', [LeaveController::class, 'create'])
            ->name('leaves.create');

        Route::post('/', [LeaveController::class, 'store'])
            ->name('leaves.store');

        Route::get('/{id}/edit', [LeaveController::class, 'edit'])
            ->name('leaves.edit');

        Route::put('/{id}', [LeaveController::class, 'update'])
            ->name('leaves.update');

        Route::delete('/{id}', [LeaveController::class, 'destroy'])
            ->name('leaves.destroy');

        Route::post('/{id}/approve', [LeaveController::class, 'approve'])
            ->name('leaves.approve');

        Route::post('/{id}/reject', [LeaveController::class, 'reject'])
            ->name('leaves.reject');
    });
    // Leave Types
    Route::prefix('leave-types')->group(function () {

        Route::get('/', [LeaveTypeController::class, 'index'])
            ->name('leave-types.index');

        Route::post('/', [LeaveTypeController::class, 'store'])
            ->name('leave-types.store');

        Route::put('/{id}', [LeaveTypeController::class, 'update'])
            ->name('leave-types.update');

        Route::delete('/{id}', [LeaveTypeController::class, 'destroy'])
            ->name('leave-types.destroy');
    });
    // Leave Allocation
    Route::prefix('leave-allocation')->group(function () {

        Route::get('/', [LeaveAllocationController::class, 'index'])
            ->name('leave-allocation.index');

        Route::post('/', [LeaveAllocationController::class, 'store'])
            ->name('leave-allocation.store');

        Route::delete('/{id}', [LeaveAllocationController::class, 'destroy'])
            ->name('leave-allocation.destroy');
    });
    // Employee Personal Leave Assignments Pipeline
    Route::get('employee-leave-assignments', [EmployeeLeaveAllocationController::class, 'index'])->name('employee-leave.assignments.index');
    Route::post('employee-leave-assignments/assign', [EmployeeLeaveAllocationController::class, 'assignPolicy'])->name('employee-leave.assignments.assign');


    // Employee Assets
    Route::prefix('employee-assets')->group(function () {

        Route::get('/', [EmployeeAssetController::class, 'index'])
            ->name('employee-assets.index');

        Route::get('/create', [EmployeeAssetController::class, 'create'])
            ->name('employee-assets.create');

        Route::post('/', [EmployeeAssetController::class, 'store'])
            ->name('employee-assets.store');

        Route::get('/{id}/edit', [EmployeeAssetController::class, 'edit'])
            ->name('employee-assets.edit');

        Route::put('/{id}', [EmployeeAssetController::class, 'update'])
            ->name('employee-assets.update');

        Route::delete('/{id}', [EmployeeAssetController::class, 'destroy'])
            ->name('employee-assets.destroy');

        // Asset Requests
        Route::get('/{asset}/requests', [EmployeeAssetController::class, 'requests'])
            ->name('employee-assets.requests');

        Route::get('/{id}/asset-request-edit', [EmployeeAssetController::class, 'assetRequestEdit'])
            ->name('employee-assets.requests.edit');

        Route::put('/{id}/asset-request-update', [EmployeeAssetController::class, 'assetRequestUpdate'])
            ->name('employee-assets.requests.update');

        // Bulk Import
        Route::get('/bulk-import', [EmployeeAssetController::class, 'bulkView'])
            ->name('employee-assets.bulk-view');

        Route::get('/download-template', [EmployeeAssetController::class, 'downloadTemplate'])
            ->name('employee-assets.download-template');

        Route::post('/bulk-store', [EmployeeAssetController::class, 'bulkStore'])
            ->name('employee-assets.bulk-store');
    });

    // Employee live search endpoint API execution mapping
    Route::get('admin/salary-slip/search-employee', [SalarySlipController::class, 'searchEmployee'])->name('salary-slip.search-employee');
    Route::get('admin/salary-slip/{id}/pdf', [SalarySlipController::class, 'downloadPDF'])->name('salary-slip.pdf');

    // Salary Slip Management
    Route::prefix('salary-slip')->group(function () {

        Route::get('/', [SalarySlipController::class, 'index'])
            ->name('salary-slip.index');

        Route::get('/create', [SalarySlipController::class, 'create'])
            ->name('salary-slip.create');

        Route::post('/', [SalarySlipController::class, 'store'])
            ->name('salary-slip.store');

        Route::get('/search-employee', [SalarySlipController::class, 'searchEmployee'])
            ->name('salary-slip.search-employee');

        Route::get('/{id}', [SalarySlipController::class, 'show'])
            ->name('salary-slip.show');

        Route::get('/{id}/edit', [SalarySlipController::class, 'edit'])
            ->name('salary-slip.edit');

        Route::put('/{id}', [SalarySlipController::class, 'update'])
            ->name('salary-slip.update');

        Route::delete('/{id}', [SalarySlipController::class, 'destroy'])
            ->name('salary-slip.destroy');

        Route::get('/{id}/pdf', [SalarySlipController::class, 'downloadPDF'])
            ->name('salary-slip.pdf');
    });
    // Holiday Management
    Route::prefix('holiday')->group(function () {

        Route::get('/', [HolidayController::class, 'index'])
            ->name('holiday.index');

        Route::get('/create', [HolidayController::class, 'create'])
            ->name('holiday.create');

        Route::post('/', [HolidayController::class, 'store'])
            ->name('holiday.store');

        Route::get('/{id}', [HolidayController::class, 'show'])
            ->name('holiday.show');

        Route::get('/{id}/edit', [HolidayController::class, 'edit'])
            ->name('holiday.edit');

        Route::put('/{id}', [HolidayController::class, 'update'])
            ->name('holiday.update');

        Route::delete('/{id}', [HolidayController::class, 'destroy'])
            ->name('holiday.destroy');
    });
    Route::resource(
        'inventory',
        InventoryController::class
    );

    Route::get(
        '/employee-assets/{asset}/requests',
        [EmployeeAssetController::class, 'requests']
    )->name('employee-assets.requests');

    Route::put(
        '/employee-assets/{id}/asset-request-update',
        [EmployeeAssetController::class, 'assetRequestUpdate']
    )->name('employee-assets.requests.update');

    Route::get(
        '/employee-assets/{id}/asset-request-edit',
        [EmployeeAssetController::class, 'assetRequestEdit']
    )->name('employee-assets.requests.edit');

    Route::post('/check', [CheckController::class, 'CheckStore'])->name('check.store');
    Route::get('/report', [CheckController::class, 'sheetReport'])->name('sheet.report');
    Route::get('/gross-salary', [PayrollController::class, 'grossSalary'])->name('gross.salary');
    Route::get('/latetime', [LateTimeController::class, 'index'])->name('attendance.latetime');
    Route::post('/latetime', [LateTimeController::class, 'lateTime'])->name('late.time');
    Route::get('/overtime', [OverTimeController::class, 'index'])->name('attendance.overtime');
    Route::post('/overtime', [OverTimeController::class, 'overTime'])->name('over.time');
    Route::get('/barcode', [AttendanceController::class, 'barcode'])->name('attd.barcode');
    Route::post('/calculate', [PayrollController::class, 'calculatePayroll'])->name('calculate.payroll');
    Route::get('/sheet-report', [PayrollController::class, 'sheetReport'])->name('payroll.report');
    Route::post('/generate', [PayrollController::class, 'generateReport'])->name('generate.payroll');

    Route::get('/employees/{employee}/hierarchy-chain', [EmployeeController::class, 'hierarchyChain'])
        ->name('employees.hierarchy-chain');
    Route::get('/employees/by-department', [EmployeeController::class, 'getByDepartment'])
        ->name('employees.by-department');
    Route::get('/employees/by-hr-team', [EmployeeController::class, 'getHrDepartmentData'])
        ->name('employees.by-hr-team');

    Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');


    Route::post('checkin',  [UserAttendanceController::class, 'checkin'])->name('checkin');
    Route::post('checkout', [UserAttendanceController::class, 'checkout'])->name('checkout');
    Route::get('status',    [UserAttendanceController::class, 'status'])->name('status');
    Route::get('history',   [UserAttendanceController::class, 'history'])->name('history');

    Route::get('attendance-list',                   [AdminAttendanceController::class, 'index'])->name('attendance-list.index');
    Route::get('attendance-list/{attendance_log}',  [AdminAttendanceController::class, 'show'])->name('attendance-list.show');
    Route::delete('attendance-list/{attendance_log}',  [AdminAttendanceController::class, 'destroy'])->name('attendance-list.destroy');
    Route::patch('attendance-list/{attendance_log}/force-checkout', [AdminAttendanceController::class, 'forceCheckout'])->name('attendance-list.force-checkout');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/create', [NotificationController::class, 'create'])->name('notifications.create');


    Route::delete('notifications', [NotificationController::class, 'delete'])->name('notifications.delete');



    Route::post('notifications/send', [NotificationController::class, 'send'])->name('notifications.send');


    Route::get('notifications/history', [NotificationController::class, 'history'])->name('notifications.history');

    Route::get('user-notifications', [UserNotificationController::class, 'index'])->name('user-notifications.index');
    Route::get('user-notifications/show', [UserNotificationController::class, 'show'])->name('user-notifications.show');
    Route::get('user-notifications/create', [UserNotificationController::class, 'create'])->name('user-notifications.create');
    Route::post('user-notifications/store', [UserNotificationController::class, 'store'])->name('user-notifications.store');
    Route::post('user-notifications/send-now', [UserNotificationController::class, 'sendNow'])->name('user-notifications.sendNow');

    Route::delete('user-notifications/destroy', [UserNotificationController::class, 'destroy'])->name('user-notifications.destroy');

    Route::get('user-notifications/edit', [UserNotificationController::class, 'edit'])->name('user-notifications.edit');

    Route::get('user-notifications/duplicate', [UserNotificationController::class, 'duplicate'])->name('user-notifications.duplicate');

    // Kanban board
    Route::get('kanban', [ApplicationController::class, 'kanban'])->name('kanban');

    // CV download
    Route::get('{application}/cv', [ApplicationController::class, 'downloadCv'])->name('cv.download');

    // Stage management
    Route::post('{application}/stage', [ApplicationController::class, 'advanceStage'])->name('stage.update');
    Route::post('move-data/{application}/stage/ajax', [ApplicationController::class, 'updateStageAjax'])->name('stage.ajax');

    // Interview rounds
    Route::post('{application}/rounds', [ApplicationController::class, 'scheduleRound'])->name('rounds.store');
    Route::patch('{application}/rounds/{round}', [ApplicationController::class, 'updateRound'])->name('rounds.update');

    // Applications Management
    Route::prefix('applications')->group(function () {

        // CRUD
        Route::get('/', [ApplicationController::class, 'index'])
            ->name('applications.index');

        Route::get('/create', [ApplicationController::class, 'create'])
            ->name('applications.create');

        Route::post('/', [ApplicationController::class, 'store'])
            ->name('applications.store');

        Route::get('/{application}', [ApplicationController::class, 'show'])
            ->name('applications.show');

        Route::get('/{application}/edit', [ApplicationController::class, 'edit'])
            ->name('applications.edit');

        Route::put('/{application}', [ApplicationController::class, 'update'])
            ->name('applications.update');

        Route::delete('/{application}', [ApplicationController::class, 'destroy'])
            ->name('applications.destroy');

        // Kanban
        Route::get('/kanban/board', [ApplicationController::class, 'kanban'])
            ->name('kanban');

        // CV Download
        Route::get('/{application}/cv', [ApplicationController::class, 'downloadCv'])
            ->name('cv.download');

        // Stage Management
        Route::post('/{application}/stage', [ApplicationController::class, 'advanceStage'])
            ->name('stage.update');

        Route::post('/move-data/{application}/stage/ajax', [ApplicationController::class, 'updateStageAjax'])
            ->name('stage.ajax');

        // Interview Rounds
        Route::post('/{application}/rounds', [ApplicationController::class, 'scheduleRound'])
            ->name('rounds.store');

        Route::patch('/{application}/rounds/{round}', [ApplicationController::class, 'updateRound'])
            ->name('rounds.update');
    });
    // Job Positions
    Route::prefix('positions')->group(function () {

        Route::get('/', [JobPositionController::class, 'index'])
            ->name('positions.index');

        Route::get('/create', [JobPositionController::class, 'create'])
            ->name('positions.create');

        Route::post('/', [JobPositionController::class, 'store'])
            ->name('positions.store');

        Route::get('/{position}/edit', [JobPositionController::class, 'edit'])
            ->name('positions.edit');

        Route::put('/{position}', [JobPositionController::class, 'update'])
            ->name('positions.update');

        Route::delete('/{position}', [JobPositionController::class, 'destroy'])
            ->name('positions.destroy');
    });

    Route::get('employees/import',          [EmployeeController::class, 'bulkImportForm'])
        ->name('employee.import.form');

    Route::post('employees/import',         [EmployeeController::class, 'bulkImport'])
        ->name('employee.import');

    Route::get('employees/import/template', [EmployeeController::class, 'downloadTemplate'])
        ->name('employee.import.template');
});

Route::resource(
    'inventory',
    InventoryController::class
);

// ─────────────────────────────────────────────
// ADMINISTRATOR
// ─────────────────────────────────────────────
Route::middleware('admin')->prefix('admin')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');



    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('admin.department.index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('admin.department.create');
        Route::post('/', [DepartmentController::class, 'create'])->name('admin.department.store');
        Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('admin.department.edit');
        Route::put('/{id}', [DepartmentController::class, 'update'])->name('admin.department.update');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('admin.department.destroy');
    });

    Route::prefix('designation')->group(function () {
        Route::get('/', [DesignationController::class, 'index'])->name('admin.designation.index');
        Route::get('/create', [DesignationController::class, 'create'])->name('admin.designation.create');
        Route::post('/', [DesignationController::class, 'create'])->name('admin.designation.store');
        Route::get('/{id}/edit', [DesignationController::class, 'edit'])->name('admin.designation.edit');
        Route::put('/{id}', [DesignationController::class, 'update'])->name('admin.designation.update');
        Route::delete('/{id}', [DesignationController::class, 'destroy'])->name('admin.designation.destroy');
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('admin.employee.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employee.create');
        Route::post('/', [EmployeeController::class, 'create'])->name('admin.employee.store');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employee.edit');
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('admin.employee.update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('admin.employee.destroy');
    });

    Route::prefix('schedule')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('admin.schedule.index');
        Route::get('/create', [ScheduleController::class, 'create'])->name('admin.schedule.create');
        Route::post('/', [ScheduleController::class, 'create'])->name('admin.schedule.store');
        Route::get('/{id}/edit', [ScheduleController::class, 'edit'])->name('admin.schedule.edit');
        Route::put('/{id}', [ScheduleController::class, 'update'])->name('admin.schedule.update');
        Route::delete('/{id}', [ScheduleController::class, 'destroy'])->name('admin.schedule.destroy');
    });

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('/check', [CheckController::class, 'CheckStore'])->name('admin.check.store');
    Route::get('/report', [CheckController::class, 'sheetReport'])->name('admin.sheet.report');

    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('admin.leaves.index');
        Route::get('/create', [LeaveController::class, 'create'])->name('admin.leaves.create');
        Route::post('/', [LeaveController::class, 'store'])->name('admin.leaves.store');  // ✅ fixed: was 'create'
        Route::get('/{id}/edit', [LeaveController::class, 'edit'])->name('admin.leaves.edit');
        Route::put('/{id}', [LeaveController::class, 'update'])->name('admin.leaves.update');
        Route::delete('/{id}', [LeaveController::class, 'destroy'])->name('admin.leaves.destroy');

        // ✅ Approve / Reject for administrator
        Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('admin.leaves.approve');
        Route::post('/{id}/reject',  [LeaveController::class, 'reject'])->name('admin.leaves.reject');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
    });

    Route::prefix('payroll')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('admin.payroll.index');
        Route::get('/create', [PayrollController::class, 'create'])->name('admin.payroll.create');
    });

    Route::post('/calculate', [PayrollController::class, 'calculatePayroll'])->name('admin.calculate.payroll');
});

// ─────────────────────────────────────────────
// MODERATOR
// ─────────────────────────────────────────────
Route::middleware('moderator')->prefix('moderator')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('moderator.dashboard');

    Route::prefix('schedule')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('moderator.schedule.index');
        Route::get('/create', [ScheduleController::class, 'create'])->name('moderator.schedule.create');
        Route::post('/', [ScheduleController::class, 'create'])->name('moderator.schedule.store');
        Route::get('/{id}/edit', [ScheduleController::class, 'edit'])->name('moderator.schedule.edit');
        Route::put('/{id}', [ScheduleController::class, 'update'])->name('moderator.schedule.update');
        Route::delete('/{id}', [ScheduleController::class, 'destroy'])->name('moderator.schedule.destroy');
    });

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('moderator.attendance.index');
    Route::post('/check', [CheckController::class, 'CheckStore'])->name('moderator.check.store');
    Route::get('/report', [CheckController::class, 'sheetReport'])->name('moderator.sheet.report');
});

// ─────────────────────────────────────────────
// EMPLOYEE  (also used by team-lead & manager
//            since they share this middleware)
// ─────────────────────────────────────────────
Route::middleware('employee')->prefix('employee')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('employee.dashboard');

    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('employee.leaves.index');
        Route::get('/create', [LeaveController::class, 'create'])->name('employee.leaves.create');
        Route::post('/', [LeaveController::class, 'store'])->name('employee.leaves.store');
        Route::get('/{id}/edit', [LeaveController::class, 'edit'])->name('employee.leaves.edit');
        Route::put('/{id}', [LeaveController::class, 'update'])->name('employee.leaves.update');
        Route::delete('/{id}', [LeaveController::class, 'destroy'])->name('employee.leaves.destroy');

        // ✅ Approve / Reject — team-lead & manager log in via employee middleware
        Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('employee.leaves.approve');
        Route::post('/{id}/reject',  [LeaveController::class, 'reject'])->name('employee.leaves.reject');
    });

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('employee.attendance.index');
    Route::post('/check', [CheckController::class, 'CheckStore'])->name('employee.check.store');
    Route::get('/report', [CheckController::class, 'sheetReport'])->name('employee.sheet.report');

    Route::get('/employees/{employee}/hierarchy-chain', [EmployeeController::class, 'hierarchyChain'])
        ->name('employee.leaves.hierarchy-chain');

    Route::get(
        '/my-assets',
        [EmployeeAssetController::class, 'myAssets']
    )->name('employee.assets');

    Route::get(
        '/my-holidays',
        [HolidayController::class, 'myHolidays']
    )->name('employee.holidays');

    Route::get('admin/my-salary-slips', [SalarySlipController::class, 'mySalarySlips'])->name('salary-slip.my-slips');
    // Inside routes/web.php -> Route::middleware('employee')->...
    Route::get('/my-salary-slip/{id}/pdf', [SalarySlipController::class, 'downloadPDF'])->name('employee.salary-slip.pdf');



    Route::get(
        '/asset-requests/create/{asset}',
        [AssetRequestController::class, 'create']
    )->name('employee.asset-request.create');

    Route::post(
        '/asset-requests',
        [AssetRequestController::class, 'store']
    )->name('employee.asset-request.store');

    Route::get(
        '/my-asset-requests/{asset}',
        [AssetRequestController::class, 'myRequests']
    )->name('employee.asset-request.index');
});

// ─────────────────────────────────────────────
// HR MANAGER
// ─────────────────────────────────────────────
Route::middleware('hr')->prefix('hr-manager')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('hr.dashboard');

    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('hr.department.index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('hr.department.create');
        Route::post('/', [DepartmentController::class, 'create'])->name('hr.department.store');
        Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('hr.department.edit');
        Route::put('/{id}', [DepartmentController::class, 'update'])->name('hr.department.update');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('hr.department.destroy');
    });

    Route::prefix('designation')->group(function () {
        Route::get('/', [DesignationController::class, 'index'])->name('hr.designation.index');
        Route::get('/create', [DesignationController::class, 'create'])->name('hr.designation.create');
        Route::post('/', [DesignationController::class, 'create'])->name('hr.designation.store');
        Route::get('/{id}/edit', [DesignationController::class, 'edit'])->name('hr.designation.edit');
        Route::put('/{id}', [DesignationController::class, 'update'])->name('hr.designation.update');
        Route::delete('/{id}', [DesignationController::class, 'destroy'])->name('hr.designation.destroy');
    });

    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('hr.employee.index');
        Route::get('/import', [EmployeeController::class, 'bulkImportForm'])->name('hr.employee.import.form');
        Route::post('/import', [EmployeeController::class, 'bulkImport'])->name('hr.employee.import');
        Route::get('/import/template', [EmployeeController::class, 'downloadTemplate'])->name('hr.employee.import.template');
        Route::get('/create', [EmployeeController::class, 'create'])->name('hr.employee.create');
        Route::get('/{id}/show', [EmployeeController::class, 'show'])->name('hr.employee.show');
        Route::post('/', [EmployeeController::class, 'create'])->name('hr.employee.store');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('hr.employee.edit');
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('hr.employee.update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('hr.employee.destroy');
        // Route::get('notifications/read/{id}',[NotificationController::class,'read'])->name('hr.notifications.read');

        // Route::get('import',[EmployeeController::class, 'bulkImportForm'])->name('hr.import.form');

        // Route::post('employees/import',[EmployeeController::class, 'bulkImport'])->name('hr.import');

        // Route::get('employees/import/template', [EmployeeController::class, 'downloadTemplate'])->name('hr.import.template');
    });

    Route::prefix('holiday')->group(function () {

        Route::get('/', [HolidayController::class, 'index'])
            ->name('hr.holiday.index');

        Route::get('/create', [HolidayController::class, 'create'])
            ->name('hr.holiday.create');

        Route::post('/', [HolidayController::class, 'store'])
            ->name('hr.holiday.store');

        Route::get('/{id}', [HolidayController::class, 'show'])
            ->name('hr.holiday.show');

        Route::get('/{id}/edit', [HolidayController::class, 'edit'])
            ->name('hr.holiday.edit');

        Route::put('/{id}', [HolidayController::class, 'update'])
            ->name('hr.holiday.update');

        Route::delete('/{id}', [HolidayController::class, 'destroy'])
            ->name('hr.holiday.destroy');
    });

    Route::prefix('salary-slip')->group(function () {

        Route::get('/', [SalarySlipController::class, 'index'])
            ->name('hr.salary-slip.index');

        Route::get('/create', [SalarySlipController::class, 'create'])
            ->name('hr.salary-slip.create');

        Route::post('/', [SalarySlipController::class, 'store'])
            ->name('hr.salary-slip.store');

        Route::get('/search-employee', [SalarySlipController::class, 'searchEmployee'])
            ->name('hr.salary-slip.search-employee');

        Route::get('/{id}', [SalarySlipController::class, 'show'])
            ->name('hr.salary-slip.show');

        Route::get('/{id}/edit', [SalarySlipController::class, 'edit'])
            ->name('hr.salary-slip.edit');

        Route::put('/{id}', [SalarySlipController::class, 'update'])
            ->name('hr.salary-slip.update');

        Route::delete('/{id}', [SalarySlipController::class, 'destroy'])
            ->name('hr.salary-slip.destroy');

        Route::get('/{id}/pdf', [SalarySlipController::class, 'downloadPDF'])
            ->name('hr.salary-slip.pdf');
    });

    // Leave Types
    Route::prefix('leave-types')->group(function () {

        Route::get('/', [LeaveTypeController::class, 'index'])
            ->name('hr.leave-types.index');

        Route::post('/', [LeaveTypeController::class, 'store'])
            ->name('hr.leave-types.store');

        Route::put('/{id}', [LeaveTypeController::class, 'update'])
            ->name('hr.leave-types.update');

        Route::delete('/{id}', [LeaveTypeController::class, 'destroy'])
            ->name('hr.leave-types.destroy');
    });
    // Leave Allocation
    Route::prefix('leave-allocation')->group(function () {

        Route::get('/', [LeaveAllocationController::class, 'index'])
            ->name('hr.leave-allocation.index');

        Route::post('/', [LeaveAllocationController::class, 'store'])
            ->name('hr.leave-allocation.store');

        Route::delete('/{id}', [LeaveAllocationController::class, 'destroy'])
            ->name('hr.leave-allocation.destroy');
    });

    // Job Positions
    Route::prefix('positions')->group(function () {

        Route::get('/', [JobPositionController::class, 'index'])
            ->name('hr.positions.index');

        Route::get('/create', [JobPositionController::class, 'create'])
            ->name('hr.positions.create');

        Route::post('/', [JobPositionController::class, 'store'])
            ->name('hr.positions.store');

        Route::get('/{position}/edit', [JobPositionController::class, 'edit'])
            ->name('hr.positions.edit');

        Route::put('/{position}', [JobPositionController::class, 'update'])
            ->name('hr.positions.update');

        Route::delete('/{position}', [JobPositionController::class, 'destroy'])
            ->name('hr.positions.destroy');
    });

    // Applications Management
    Route::prefix('applications')->group(function () {

        // CRUD
        Route::get('/', [ApplicationController::class, 'index'])
            ->name('hr.applications.index');

        Route::get('/create', [ApplicationController::class, 'create'])
            ->name('hr.applications.create');

        Route::post('/', [ApplicationController::class, 'store'])
            ->name('hr.applications.store');

        Route::get('/{application}', [ApplicationController::class, 'show'])
            ->name('hr.applications.show');

        Route::get('/{application}/edit', [ApplicationController::class, 'edit'])
            ->name('hr.applications.edit');

        Route::put('/{application}', [ApplicationController::class, 'update'])
            ->name('hr.applications.update');

        Route::delete('/{application}', [ApplicationController::class, 'destroy'])
            ->name('hr.applications.destroy');

        // Kanban
        Route::get('/kanban/board', [ApplicationController::class, 'kanban'])
            ->name('hr.kanban');

        // CV Download
        Route::get('/{application}/cv', [ApplicationController::class, 'downloadCv'])
            ->name('hr.cv.download');

        // Stage Management
        Route::post('/{application}/stage', [ApplicationController::class, 'advanceStage'])
            ->name('hr.stage.update');

        Route::post('/move-data/{application}/stage/ajax', [ApplicationController::class, 'updateStageAjax'])
            ->name('hr.stage.ajax');

        // Interview Rounds
        Route::post('/{application}/rounds', [ApplicationController::class, 'scheduleRound'])
            ->name('hr.rounds.store');

        Route::patch('/{application}/rounds/{round}', [ApplicationController::class, 'updateRound'])
            ->name('hr.rounds.update');
    });

    // Employee Personal Leave Assignments Pipeline
    Route::get('employee-leave-assignments', [EmployeeLeaveAllocationController::class, 'index'])->name('hr.employee-leave.assignments.index');
    Route::post('employee-leave-assignments/assign', [EmployeeLeaveAllocationController::class, 'assignPolicy'])->name('hr.employee-leave.assignments.assign');

    Route::prefix('leaves')->group(function () {
        Route::get('/', [LeaveController::class, 'index'])->name('hr.leaves.index');
        Route::get('/create', [LeaveController::class, 'create'])->name('hr.leaves.create');
        Route::post('/', [LeaveController::class, 'store'])->name('hr.leaves.store');  // ✅ fixed: was 'create'
        Route::get('/{id}/edit', [LeaveController::class, 'edit'])->name('hr.leaves.edit');
        Route::put('/{id}', [LeaveController::class, 'update'])->name('hr.leaves.update');
        Route::delete('/{id}', [LeaveController::class, 'destroy'])->name('hr.leaves.destroy');

        // ✅ Approve / Reject for HR
        Route::post('/{id}/approve', [LeaveController::class, 'approve'])->name('hr.leaves.approve');
        Route::post('/{id}/reject',  [LeaveController::class, 'reject'])->name('hr.leaves.reject');
    });



    Route::get('attendance-list', [AdminAttendanceController::class, 'index'])->name('hr.attendance-list.index');
    Route::get('attendance-list/{attendance_log}',  [AdminAttendanceController::class, 'show'])->name('hr.attendance-list.show');
    Route::delete('attendance-list/{attendance_log}',  [AdminAttendanceController::class, 'destroy'])->name('hr.attendance-list.destroy');
    Route::patch('attendance-list/{attendance_log}/force-checkout', [AdminAttendanceController::class, 'forceCheckout'])->name('hr.attendance-list.force-checkout');

    Route::get('notifications', [NotificationController::class, 'index'])->name('hr.notifications.index');
    Route::get('notifications/create', [NotificationController::class, 'create'])->name('hr.notifications.create');


    Route::delete('notifications', [NotificationController::class, 'delete'])->name('hr.notifications.delete');



    Route::post('notifications/send', [NotificationController::class, 'send'])->name('hr.notifications.send');


    Route::get('notifications/history', [NotificationController::class, 'history'])->name('hr.notifications.history');

    Route::get('user-notifications', [UserNotificationController::class, 'index'])->name('hr.user-notifications.index');
    Route::get('user-notifications/show', [UserNotificationController::class, 'show'])->name('hr.user-notifications.show');
    Route::get('user-notifications/create', [UserNotificationController::class, 'create'])->name('hr.user-notifications.create');
    Route::post('user-notifications/store', [UserNotificationController::class, 'store'])->name('hr.user-notifications.store');
    Route::post('user-notifications/send-now', [UserNotificationController::class, 'sendNow'])->name('hr.user-notifications.sendNow');

    Route::delete('user-notifications/destroy', [UserNotificationController::class, 'destroy'])->name('hr.user-notifications.destroy');

    Route::get('user-notifications/edit', [UserNotificationController::class, 'edit'])->name('hr.user-notifications.edit');

    Route::get('user-notifications/duplicate', [UserNotificationController::class, 'duplicate'])->name('hr.user-notifications.duplicate');

    // Kanban board
    Route::get('kanban', [ApplicationController::class, 'kanban'])->name('hr.kanban');

    // CV download
    Route::get('{application}/cv', [ApplicationController::class, 'downloadCv'])->name('hr.cv.download');

    // Stage management
    Route::post('{application}/stage', [ApplicationController::class, 'advanceStage'])->name('hr.stage.update');
    Route::post('move-data/{application}/stage/ajax', [ApplicationController::class, 'updateStageAjax'])->name('hr.stage.ajax');

    // Interview rounds
    Route::post('{application}/rounds', [ApplicationController::class, 'scheduleRound'])->name('hr.rounds.store');
    Route::patch('{application}/rounds/{round}', [ApplicationController::class, 'updateRound'])->name('hr.rounds.update');




    Route::get('/attendance', [AttendanceController::class, 'index'])->name('hr.attendance.index');
    Route::post('/check', [CheckController::class, 'CheckStore'])->name('hr.check.store');
    Route::get('/report', [CheckController::class, 'sheetReport'])->name('hr.sheet.report');
});
Route::resource(
    'employee-assets',
    EmployeeAssetController::class
);

// ─────────────────────────────────────────────
// PAYROLL MANAGER
// ─────────────────────────────────────────────
Route::middleware('payroll')->prefix('manager')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('payroll.dashboard');

    Route::prefix('payroll')->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('manager.payroll.index');
        Route::get('/create', [PayrollController::class, 'create'])->name('manager.payroll.create');
    });

    Route::post('/calculate', [PayrollController::class, 'calculatePayroll'])->name('manager.calculate.payroll');
});
Route::get('notification/read/{id}', [NotificationController::class, 'read'])->name('notification.read');

// ─────────────────────────────────────────────
// AUTH (profile)
// ─────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// super-admin
Route::get('employee/export', [EmployeeController::class, 'export'])->name('employee.export');

// administrator
Route::get('admin/employee/export', [EmployeeController::class, 'export'])->name('admin.employee.export');

// hr-manager
Route::get('hr/employee/export', [EmployeeController::class, 'export'])->name('hr.employee.export');

require __DIR__ . '/auth.php';
