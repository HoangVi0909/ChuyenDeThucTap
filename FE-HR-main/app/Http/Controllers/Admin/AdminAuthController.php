<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/api/admin/login', [
            'username' => $request->username,
            'password' => $request->password
        ]);
        $data = $response->json();
        if ($response->status() === 200 && isset($data['user']) && isset($data['token'])) {
            session(['admin' => $data['user'], 'admin_token' => $data['token']]);
            return redirect()->route('admin.dashboard');
        }
        return back()->with('error', $data['message'] ?? 'Đăng nhập thất bại');
    }

    public function showForgotForm()
    {
        return view('admin.admin.forgot');
    }

    public function forgot(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/api/admin/forgot-password', [
            'username' => $request->username
        ]);
        $data = $response->json();
        return back()->with('message', $data['message'] ?? 'Yêu cầu thất bại');
    }

    public function showChangePasswordForm()
    {
        return view('admin.admin.change_password');
    }

    public function changePassword(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/api/admin/change-password', [
            'username' => $request->username,
            'old_password' => $request->old_password,
            'new_password' => $request->new_password
        ]);
        $data = $response->json();
        return back()->with('message', $data['message'] ?? 'Đổi mật khẩu thất bại');
    }

    public function logout()
    {
        $token = session('admin_token');
        if ($token) {
            Http::withToken($token)->post('http://127.0.0.1:8000/api/admin/logout');
        }
        session()->forget(['admin', 'admin_token']);
        return redirect()->route('admin.login')->with('message', 'Đã đăng xuất thành công!');
    }
}