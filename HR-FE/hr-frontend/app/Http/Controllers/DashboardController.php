<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        // Tổng số nhân viên
        $respEmployees = Http::get(config('services.backend_api.url') . '/api/employees');
        $totalEmployees = $respEmployees->successful() ? count($respEmployees->json()['data'] ?? []) : 0;

        // Tổng số phòng ban
        $respDepartments = Http::get(config('services.backend_api.url') . '/api/departments');
        $totalDepartments = $respDepartments->successful() ? count($respDepartments->json()['data'] ?? []) : 0;

        // Tổng số vị trí
        $respPositions = Http::get(config('services.backend_api.url') . '/api/positions');
        $totalPositions = $respPositions->successful() ? count($respPositions->json()['data'] ?? []) : 0;

        // 5 nhân viên mới nhất
        $latestEmployees = [];
        if ($respEmployees->successful()) {
            $employeesData = $respEmployees->json()['data'] ?? [];
            $latestEmployees = array_slice($employeesData, 0, 5); // lấy 5 nhân viên đầu tiên
        }

        return view('admin.dashboard', compact(
            'totalEmployees',
            'totalDepartments',
            'totalPositions',
            'latestEmployees'
        ));
    }
}
