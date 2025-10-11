<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // Debug input
        Log::info('Login attempt with data:', $request->all());
        
        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // Demo login check first (để test nhanh)
        if ($request->username === 'admin' && $request->password === '123456') {
            Log::info('Demo login successful');
            session(['admin' => [
                'id' => 1,
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@example.com',
                'role' => 'admin'
            ]]);
            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập demo thành công!');
        }

        Log::info('Demo login failed, trying API endpoints');

        // Try different API endpoints
        $endpoints = [
            'http://127.0.0.1:8000/api/admin/login',
            'http://127.0.0.1:8000/api/admin/auth/login',
            'http://127.0.0.1:8000/api/auth/admin/login',
            'http://127.0.0.1:8000/api/login'
        ];

        foreach ($endpoints as $endpoint) {
            try {
                Log::info("Trying endpoint: " . $endpoint);
                
                $response = Http::timeout(5)->post($endpoint, [
                    'username' => $request->username,
                    'password' => $request->password
                ]);
                
                Log::info("Response status: " . $response->status());
                
                if ($response->successful()) {
                    $data = $response->json();
                    Log::info("Response data:", $data);
                    
                    // Check for admin data in various response formats
                    $admin = null;
                    if (isset($data['data']['admin'])) {
                        $admin = $data['data']['admin'];
                    } elseif (isset($data['admin'])) {
                        $admin = $data['admin'];
                    } elseif (isset($data['user'])) {
                        $admin = $data['user'];
                    } elseif (isset($data['data']['user'])) {
                        $admin = $data['data']['user'];
                    }
                    
                    if ($admin) {
                        session(['admin' => $admin]);
                        if (isset($data['token'])) {
                            session(['auth_token' => $data['token']]);
                        }
                        return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
                    }
                }
                
            } catch (\Exception $e) {
                Log::error("API error for {$endpoint}: " . $e->getMessage());
                continue;
            }
        }
        
        Log::info('All endpoints failed, showing error');
        
        return back()
            ->with('error', 'Tên đăng nhập hoặc mật khẩu không đúng. Sử dụng admin/123456 để test demo.')
            ->withInput($request->only('username'));
    }

    public function showForgotForm()
    {
        return view('admin.auth.forgot');
    }

    public function forgot(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/api/auth/forgot-password', [
            'username' => $request->username
        ]);
        $data = $response->json();
        return back()->with('message', $data['message'] ?? 'Yêu cầu thất bại');
    }

    public function showChangePasswordForm()
    {
        return view('admin.auth.change_password');
    }

    public function changePassword(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/api/auth/change-password', [
            'username' => $request->username,
            'old_password' => $request->old_password,
            'new_password' => $request->new_password
        ]);
        $data = $response->json();
        return back()->with('message', $data['message'] ?? 'Đổi mật khẩu thất bại');
    }

    public function logout()
    {
        Http::post('http://127.0.0.1:8000/api/auth/logout');
        session()->forget('admin');
        return redirect()->route('admin.login')->with('message', 'Đã đăng xuất thành công!');
    }
}