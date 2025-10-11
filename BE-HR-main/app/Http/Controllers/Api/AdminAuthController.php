<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAuthRequest;

class AdminAuthController extends Controller
{
    // Đăng nhập admin
    public function login(AdminAuthRequest $request)
    {
        $data = $request->all();
        $admin = Admin::where('username', $data['username'] ?? null)->first();
        if (!$admin || !Hash::check($data['password'] ?? '', $admin->password)) {
            return response()->json(['message' => 'Sai tài khoản hoặc mật khẩu'], 401);
        }
        $token = $admin->createToken('admin-token')->plainTextToken;
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'user' => $admin
        ]);
    }

    // Đăng xuất admin
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Đã đăng xuất']);
    }

    // Quên mật khẩu admin
    public function forgotPassword(AdminAuthRequest $request)
    {
        $data = $request->all();
        $admin = Admin::where('username', $data['username'] ?? null)->first();
        if (!$admin) {
            return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
        }
        $newPassword = substr(md5(time()), 0, 8);
        $admin->password = Hash::make($newPassword);
        $admin->save();
        return response()->json(['message' => 'Mật khẩu mới', 'new_password' => $newPassword]);
    }

    // Đổi mật khẩu admin
    public function changePassword(AdminAuthRequest $request)
    {
        $data = $request->all();
        $admin = Admin::where('username', $data['username'] ?? null)->first();
        if (!$admin || !Hash::check($data['old_password'] ?? '', $admin->password)) {
            return response()->json(['message' => 'Sai mật khẩu cũ'], 401);
        }
        $admin->password = Hash::make($data['new_password'] ?? '');
        $admin->save();
        return response()->json(['message' => 'Đổi mật khẩu thành công']);
    }
}