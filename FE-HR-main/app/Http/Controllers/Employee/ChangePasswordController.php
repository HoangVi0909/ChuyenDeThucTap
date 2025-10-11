<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function change(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        $token = session('employee_token');
        $baseUrl = config('services.backend_api.url');
        // Lấy thông tin employee hiện tại
        $employeeRes = Http::withToken($token)->get($baseUrl . '/api/employee/me');
        $employee = $employeeRes->json();
        if (!$employee || !isset($employee['username'])) {
            return back()->with('password_error', 'Không xác định được nhân viên!');
        }
        // Gọi API xác thực mật khẩu hiện tại
        $checkRes = Http::post($baseUrl . '/api/employee/check-password', [
            'username' => $employee['username'],
            'password' => $request->current_password,
        ]);
        if (!$checkRes->successful() || !$checkRes->json('valid')) {
            return back()->with('password_error', 'Mật khẩu hiện tại không đúng!');
        }
        // Gọi API đổi mật khẩu
        $changeRes = Http::withToken($token)->post($baseUrl . '/api/employee/change-password', [
            'new_password' => $request->new_password,
        ]);
        if ($changeRes->successful()) {
            return back()->with('password_success', 'Đổi mật khẩu thành công!');
        } else {
            return back()->with('password_error', 'Đổi mật khẩu thất bại!');
        }
    }
}
