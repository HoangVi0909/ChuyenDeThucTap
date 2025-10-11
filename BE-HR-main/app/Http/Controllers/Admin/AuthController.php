<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)
                     ->orWhere('email', $request->username)
                     ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors([
                'username' => 'Tên đăng nhập hoặc mật khẩu không đúng.'
            ])->withInput();
        }

        if ($admin->status !== 'active') {
            return back()->withErrors([
                'username' => 'Tài khoản đã bị vô hiệu hóa.'
            ])->withInput();
        }

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công!');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đã đăng xuất thành công!');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function showForgotForm()
    {
        return view('admin.auth.forgot');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email'
        ]);

        // Logic for sending password reset email
        return back()->with('success', 'Link đặt lại mật khẩu đã được gửi đến email của bạn.');
    }

    public function showChangePasswordForm()
    {
        return view('admin.auth.change_password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Auth::guard('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors([
                'current_password' => 'Mật khẩu hiện tại không đúng.'
            ]);
        }

        $admin->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}