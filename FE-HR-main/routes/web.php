<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\WorkScheduleController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Employee\SalaryController as EmployeeSalaryController;

// Trang mặc định -> Dùng test-api cho healthcheck luôn
Route::get('/', function () {
    // Nếu là healthcheck từ Railway/monitoring tools, return JSON
    if (request()->header('User-Agent') && (
        str_contains(request()->header('User-Agent'), 'healthcheck') ||
        str_contains(request()->header('User-Agent'), 'bot') ||
        request()->wantsJson()
    )) {
        return response()->json([
            'status' => 'ok',
            'message' => 'HR Frontend is running',
            'service' => 'frontend',
            'timestamp' => now()->toISOString(),
        ], 200);
    }

    // User thường thì redirect login
    return redirect()->route('admin.login');
});

// Health check endpoint for Railway
Route::get('/test-api', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'HR Frontend is running',
        'service' => 'frontend',
        'timestamp' => now()->toISOString(),
    ], 200);
});

// ================= PUBLIC AUTH ROUTES =================
// Không cần middleware ở đây
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.post');

    Route::get('forgot-password', [AdminAuthController::class, 'showForgotForm'])->name('admin.forgot');
    Route::post('forgot-password', [AdminAuthController::class, 'forgot'])->name('admin.forgot.post');


    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

// ================= PROTECTED ADMIN ROUTES =================
// Xóa middleware 'admin' vì FE không cần, dùng session check trong controller
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', EmployeeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('positions', PositionController::class);
    Route::resource('notifications', NotificationController::class);
    Route::view('internal-notifications', 'admin.notifications')->name('internal-notifications');
    Route::resource('salaries', SalaryController::class);
    
    // Salary management additional routes
    Route::post('salaries/calculate', [SalaryController::class, 'calculateSalary'])->name('salaries.calculate');
    Route::post('salaries/approve', [SalaryController::class, 'approve'])->name('salaries.approve');
    Route::post('salaries/mark-as-paid', [SalaryController::class, 'markAsPaid'])->name('salaries.mark-as-paid');
    
    Route::resource('work-schedules', WorkScheduleController::class);
    
    // Resignation requests
    Route::get('resignation-requests', [App\Http\Controllers\Admin\ResignationRequestController::class, 'index'])->name('resignation-requests.index');
    Route::get('resignation-requests/{id}', [App\Http\Controllers\Admin\ResignationRequestController::class, 'show'])->name('resignation-requests.show');
    Route::patch('resignation-requests/{id}/status', [App\Http\Controllers\Admin\ResignationRequestController::class, 'updateStatus'])->name('resignation-requests.update-status');
    Route::resource('feedback', FeedbackController::class);
    
    Route::view('leave-requests', 'admin.leave_requests')->name('leave-requests.index');
});

// ================= EMPLOYEE AUTH ROUTES =================
// Thêm route cho login và dashboard của employee (nhân viên).
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('login', [App\Http\Controllers\Employee\EmployeeAuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [App\Http\Controllers\Employee\EmployeeAuthController::class, 'login'])->name('login.post');
    Route::post('logout', [App\Http\Controllers\Employee\EmployeeAuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [App\Http\Controllers\Employee\DashboardController::class, 'index'])->name('dashboard');
    Route::get('work-schedule', [App\Http\Controllers\Employee\DashboardController::class, 'workSchedule'])->name('work-schedule');
    Route::get('salary', [EmployeeSalaryController::class, 'index'])->name('salary');
    Route::get('resignation', [App\Http\Controllers\Employee\DashboardController::class, 'resignation'])->name('resignation');
    Route::post('change-password', [App\Http\Controllers\Employee\ChangePasswordController::class, 'change'])->name('change-password');
    Route::post('send-feedback', [App\Http\Controllers\Employee\FeedbackController::class, 'send'])->name('send-feedback');

    Route::get('info', function () {
        $token = session('employee_token');
        $baseUrl = config('services.backend_api.url');
        $employee = null;
        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)->get($baseUrl . '/api/employee/me');
            if ($response->successful()) {
                $employee = $response->json();
            }
        } catch (Exception $e) {
        }
        return view('employee.info', compact('employee'));
    })->name('info');

    Route::get('notifications', function () {
        $token = session('employee_token');
        $baseUrl = config('services.backend_api.url');
        $notifications = [];
        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)->get($baseUrl . '/api/employee/notifications');
            if ($response->successful()) {
                $data = $response->json();
                // Nếu là phân trang, lấy ra mảng 'data'
                if (isset($data['data'])) {
                    $notifications = $data['data'];
                } else {
                    $notifications = $data;
                }
            }
        } catch (Exception $e) {
        }
        return view('employee.notifications', compact('notifications'));
    })->name('notifications');

    Route::get('feedback', function () {
        return view('employee.feedback');
    })->name('feedback');
});
