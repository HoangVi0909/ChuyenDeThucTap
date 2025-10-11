<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class HRBEApiController extends Controller
{
    // Ví dụ: Lấy danh sách nhân viên từ HR-BE
    public function getEmployees()
    {
        $response = Http::get(config('services.backend_api.url') . '/api/employees');
        return $response->json();
    }

    // Ví dụ: Thêm nhân viên mới vào HR-BE
    public function addEmployee(Request $request)
    {
        $response = Http::post(config('services.backend_api.url') . '/api/employees', $request->all());
        return $response->json();
    }

    // Ví dụ: Lấy danh sách phòng ban từ HR-BE
    public function getDepartments()
    {
        $response = Http::get(config('services.backend_api.url') . '/api/departments');
        return $response->json();
    }
}
