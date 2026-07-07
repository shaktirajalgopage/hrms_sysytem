<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\UserAttendanceController;
use App\Http\Controllers\Api\AppNotificationController;
use App\Http\Controllers\Api\HolidayController;
use App\Http\Controllers\Api\SalarySlipController;
use App\Http\Controllers\Api\RecentActivityController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AttendanceLogsController;
use App\Http\Controllers\Api\AllLeavesController;


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



Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group( function () {
    
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::post('/logout', [AuthController::class, 'logout']);

     Route::get('/leaves', [LeaveController::class, 'index']);

    Route::get('/leave-details/{id}', [LeaveController::class, 'show']);

    Route::post('/leaves', [LeaveController::class, 'store']);

    Route::put('/leaves/{id}', [LeaveController::class, 'update']);

    Route::delete('/leaves/{id}', [LeaveController::class, 'destroy']);

    Route::get('/my-leaveType', [LeaveController::class, 'getMyLeaveTypes']);

    Route::get('/holidays', [HolidayController::class, 'index']);

    Route::get('/admin/leaves', [AllLeavesController::class, 'index']);
    Route::get('/admin/leaves/{id}', [AllLeavesController::class, 'show']);

    Route::get('/attendance-logs', [AttendanceLogsController::class, 'index']);
    Route::get('/attendance-logs/{id}', [AttendanceLogsController::class, 'show']);
    Route::get('/attendance-export', [AttendanceLogsController::class, 'export'])
    ->name('attendance.export');

    
    // 2. Mid list data array endpoints
    Route::get('salary-slips', [SalarySlipController::class, 'index']);

    Route::get('/roles-list', [RoleController::class, 'getTitlesList']);



    
    Route::post('checkin',  [UserAttendanceController::class, 'checkin']) ->name('checkin');
    Route::post('checkout', [UserAttendanceController::class, 'checkout'])->name('checkout');
    Route::get('status',    [UserAttendanceController::class, 'status'])  ->name('status');
    Route::get('today',     [UserAttendanceController::class, 'today'])   ->name('today');
    Route::get('history',   [UserAttendanceController::class, 'history']) ->name('history');

    Route::get('app-notification', [AppNotificationController::class, 'index']);
    Route::get('/unread-count', [AppNotificationController::class, 'unreadCount']);
        
        // Full Lifecycle Tracking Telemetry
    Route::post('/track-delivery', [AppNotificationController::class, 'trackDelivery']);
    Route::post('/{id}/read', [AppNotificationController::class, 'markAsRead']);
    Route::post('/{id}/dismiss', [AppNotificationController::class, 'dismiss']);
    Route::post('/read-all', [AppNotificationController::class, 'markAllAsRead']);

    Route::get('/get-all-employees', [AuthController::class, 'getAllEmployees']);

    Route::get('/recent-activities', [RecentActivityController::class, 'getRecentActivities']);
});
