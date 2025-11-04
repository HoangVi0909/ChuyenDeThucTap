<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WorkScheduleController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\EmployeeAuthController;
use App\Http\Controllers\Api\SalaryManagementController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('test', fn() => 'API test works!');
Route::get('ping', fn() => response()->json(['pong' => true]));

// Temporary public routes for testing


// Admin login/logout
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('admin/logout', [AdminAuthController::class, 'logout']);
Route::post('admin/forgot-password', [AdminAuthController::class, 'forgotPassword']);
Route::patch('admin/change-password', [AdminAuthController::class, 'changePassword']);

// Employee login/logout (client)
Route::post('emplpoyee/login', [EmployeeAuthController::class, 'login']);
Route::post('employee/logout', [EmployeeAuthController::class, 'logout']);
Route::post('employee/login', [EmployeeAuthController::class, 'login']);
Route::post('employee/logout', [EmployeeAuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Protected routes (Sanctum)
|--------------------------------------------------------------------------
*/

// ADMIN AREA
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('positions', PositionController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('notifications', NotificationController::class);
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::apiResource('work-schedules', WorkScheduleController::class);
    // Route::apiResource('salaries', SalaryController::class); // Đã thay thế bằng salary-management
    Route::apiResource('feedback', FeedbackController::class);
    
    // Quản lý lương (mới)
    Route::prefix('salary-management')->group(function () {
        Route::get('/', [SalaryManagementController::class, 'index']); // Danh sách bản ghi lương
        Route::post('/', [SalaryManagementController::class, 'store']); // Tạo bản ghi lương mới
        Route::post('calculate', [SalaryManagementController::class, 'calculateForMonth']); // Tính lương tháng
        Route::get('{id}', [SalaryManagementController::class, 'show']); // Chi tiết bản ghi lương
        Route::put('{id}', [SalaryManagementController::class, 'update']); // Cập nhật thưởng/phạt
        Route::post('{id}/recalculate', [SalaryManagementController::class, 'recalculate']); // Tính lại lương
        Route::post('approve', [SalaryManagementController::class, 'approve']); // Duyệt lương
        Route::post('mark-as-paid', [SalaryManagementController::class, 'markAsPaid']); // Đánh dấu đã trả
        Route::get('reports/monthly', [SalaryManagementController::class, 'monthlyReport']); // Báo cáo tháng
    });
    
    // Quản lý đơn xin nghỉ phép
    Route::get('leave-requests', [\App\Http\Controllers\Api\LeaveRequestController::class, 'index']);
    Route::patch('leave-requests/{id}/review', [\App\Http\Controllers\Api\LeaveRequestController::class, 'review']);
    // Quản lý đơn xin nghỉ việc
    Route::get('resignation-requests', [\App\Http\Controllers\Api\ResignationRequestController::class, 'index']);
    Route::get('resignation-requests/{id}', [\App\Http\Controllers\Api\ResignationRequestController::class, 'show']);
    Route::patch('resignation-requests/{id}/status', [\App\Http\Controllers\Api\ResignationRequestController::class, 'updateStatus']);
});

// CLIENT AREA (EMPLOYEE)
Route::middleware(['auth:sanctum', 'role:employee'])->prefix('employee')->group(function () {
    Route::get('me', [\App\Http\Controllers\Api\EmployeeController::class, 'me']); // Thông tin cá nhân
    Route::get('my-work-schedules', [\App\Http\Controllers\Api\EmployeeController::class, 'myWorkSchedules']);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('feedback', [FeedbackController::class, 'store']);
    Route::post('change-password', [\App\Http\Controllers\Api\EmployeeController::class, 'changePassword']);
    
    // Xem lương cá nhân
    Route::get('my-salary-records', [SalaryManagementController::class, 'mySalaryRecords']);
    Route::get('my-salary-records/{id}', [SalaryManagementController::class, 'mySalaryRecordDetail']);
    
    // Đơn xin nghỉ phép
    Route::post('leave-requests', [\App\Http\Controllers\Api\LeaveRequestController::class, 'store']);
    Route::get('my-leave-requests', [\App\Http\Controllers\Api\LeaveRequestController::class, 'myRequests']);
    // Đơn xin nghỉ việc
    Route::post('resignation-requests', [\App\Http\Controllers\Api\ResignationRequestController::class, 'store']);
    Route::get('my-resignation-requests', [\App\Http\Controllers\Api\ResignationRequestController::class, 'myRequests']);
});
