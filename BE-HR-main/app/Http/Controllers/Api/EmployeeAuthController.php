<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeAuthRequest;

class EmployeeAuthController extends Controller
{
    public function logout(EmployeeAuthRequest $request)
    {
        Auth::logout();
        return response()->json(['message' => 'Đã đăng xuất']);
    }
    public function login(EmployeeAuthRequest $request)
    {
        $data = $request->all();
        $employee = Employee::where('username', $data['username'] ?? null)->first();
        if (!$employee || !Hash::check($data['password'] ?? '', $employee->password)) {
            return response()->json(['message' => 'Sai tài khoản hoặc mật khẩu'], 401);
        }
        Auth::login($employee);
        // Tạo token cho employee (yêu cầu đã cài Laravel Sanctum)
        $token = $employee->createToken('employee_token')->plainTextToken;
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $employee
        ]);
    }
}
