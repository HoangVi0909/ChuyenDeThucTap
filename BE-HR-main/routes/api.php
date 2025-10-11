<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WorkScheduleController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\EmployeeAuthController;

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
    Route::apiResource('work-schedules', WorkScheduleController::class);
    Route::apiResource('salaries', SalaryController::class);
    Route::apiResource('feedback', FeedbackController::class);
});

// CLIENT AREA (EMPLOYEE)
Route::middleware(['auth:sanctum', 'role:employee'])->prefix('employee')->group(function () {
    Route::get('me', [\App\Http\Controllers\Api\EmployeeController::class, 'me']); // Thông tin cá nhân
    Route::get('my-work-schedules', [WorkScheduleController::class, 'index']);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('feedback', [FeedbackController::class, 'store']);
    Route::post('change-password', [\App\Http\Controllers\Api\EmployeeController::class, 'changePassword']);
});
