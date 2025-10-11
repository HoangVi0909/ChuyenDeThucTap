<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        $notifications = [];
        try {
            $response = Http::withToken($token)->get($apiUrl . '/api/admin/notifications');
            if ($response->successful()) {
                $notifications = $response->json();
                if (isset($notifications['data'])) {
                    $notifications = $notifications['data'];
                }
            }
        } catch (\Exception $e) {
            $notifications = [];
        }
        return view('admin.notifications.index', compact('notifications'));
    }
    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $notification = [];
        try {
            $response = Http::withToken($token)->get("{$apiUrl}/api/admin/notifications/{$id}");
            if ($response->successful()) {
                $notification = $response->json();
                if (isset($notification['data'])) {
                    $notification = $notification['data'];
                }
            }
        } catch (\Exception $e) {
            $notification = [];
        }
        return view('admin.notifications.show', compact('notification'));
    }
    public function create()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->except(['_token']);
            $response = Http::withToken($token)->post("{$apiUrl}/api/admin/notifications", $data);
            if ($response->successful()) {
                return redirect()->route('admin.notifications.index')->with('success', 'Thêm thông báo thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi thêm thông báo';
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $notification = [];
        try {
            $response = Http::withToken($token)->get("{$apiUrl}/api/admin/notifications/{$id}");
            if ($response->successful()) {
                $notification = $response->json();
                if (isset($notification['data'])) {
                    $notification = $notification['data'];
                }
                if (!isset($notification['id'])) {
                    return redirect()->route('admin.notifications.index')->withErrors('Không tìm thấy thông báo!');
                }
            } else {
                return redirect()->route('admin.notifications.index')->withErrors('Không tìm thấy thông báo!');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.notifications.index')->withErrors('Lỗi kết nối API');
        }
        return view('admin.notifications.edit', compact('notification'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->except(['_token', '_method']);
            $response = Http::withToken($token)->put("{$apiUrl}/api/admin/notifications/{$id}", $data);
            if ($response->successful()) {
                return redirect()->route('admin.notifications.index')->with('success', 'Cập nhật thông báo thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi cập nhật thông báo';
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $response = Http::withToken($token)->delete("{$apiUrl}/api/admin/notifications/{$id}");
            if ($response->successful()) {
                return redirect()->route('admin.notifications.index')->with('success', 'Xóa thông báo thành công!');
            } else {
                return redirect()->route('admin.notifications.index')->with('error', 'Xóa thông báo thất bại!');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.notifications.index')->with('error', 'Lỗi kết nối API');
        }
    }
}
