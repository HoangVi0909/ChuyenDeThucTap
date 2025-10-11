<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class HRBEApiController extends Controller
{
    // Ví dụ: Lấy danh sách nhân viên từ HR-BE
    public function getEmployees()
    {
        $response = Http::get('http://127.0.0.1:8000/api/employees');
        return $response->json();
    }

    // Ví dụ: Thêm nhân viên mới vào HR-BE
    public function addEmployee(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/api/employees', $request->all());
        return $response->json();
    }

    // Ví dụ: Lấy danh sách phòng ban từ HR-BE
    public function getDepartments()
    {
        $response = Http::get('http://127.0.0.1:8000/api/departments');
        return $response->json();
    }
}
