<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EmployeeAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('employee.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'password' => 'required',
        ]);
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $response = Http::post($apiUrl . '/api/employee/login', [
                'username' => $request->code, // Gửi đúng trường username cho BE
                'password' => $request->password,
            ]);
            if ($response->successful() && isset($response['token'])) {
                session(['employee_token' => $response['token']]);
                return redirect()->route('employee.dashboard');
            } else {
                $error = $response->json('message') ?? 'Đăng nhập thất bại!';
                return back()->withErrors(['error' => $error])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối API'])->withInput();
        }
    }

    public function logout()
    {
        session()->forget('employee_token');
        return redirect()->route('employee.login');
    }
}
