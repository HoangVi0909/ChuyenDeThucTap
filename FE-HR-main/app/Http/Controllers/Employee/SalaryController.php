<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        if (!session('employee_token')) {
            return redirect()->route('employee.login');
        }

        $token = session('employee_token');
        $baseUrl = config('services.backend_api.url');
        $salaries = [];
        $employee = null;

        try {
            // Lấy thông tin nhân viên hiện tại
            $employeeResponse = Http::withToken($token)->get("{$baseUrl}/api/employee/me");
            if ($employeeResponse->successful()) {
                $employeeData = $employeeResponse->json();
                $employee = isset($employeeData['data']) ? $employeeData['data'] : $employeeData;
            }

            // Lấy lịch sử lương của nhân viên sử dụng API chuyên dụng cho employee
            $salariesResponse = Http::withToken($token)->get("{$baseUrl}/api/employee/my-salary-records");

            if ($salariesResponse->successful()) {
                $salaryData = $salariesResponse->json();
                if (isset($salaryData['success']) && $salaryData['success'] && isset($salaryData['data']['data'])) {
                    $salaries = $salaryData['data']['data'];
                } elseif (isset($salaryData['data'])) {
                    $salaries = is_array($salaryData['data']) ? $salaryData['data'] : [];
                }
            }

        } catch (\Exception $e) {
            Log::error('Employee salary error: ' . $e->getMessage());
            $salaries = [];
        }

        // Tính toán statistics
        $totalSalaries = count($salaries);
        $totalAmount = array_sum(array_column($salaries, 'total_salary'));
        $averageAmount = $totalSalaries > 0 ? $totalAmount / $totalSalaries : 0;
        $latestSalary = $totalSalaries > 0 ? $salaries[0] : null;

        return view('employee.salary', compact('salaries', 'employee', 'totalSalaries', 'totalAmount', 'averageAmount', 'latestSalary'));
    }
}