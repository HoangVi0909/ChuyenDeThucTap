<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // Danh sách phòng ban
    public function index()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        $departments = [];
        try {
            $response = Http::withToken($token)->get($apiUrl . '/api/admin/departments');
            if ($response->successful()) {
                $departments = $response->json();
                if (isset($departments['data'])) {
                    $departments = $departments['data'];
                }
            }
        } catch (\Exception $e) {
            $departments = [];
        }
        return view('admin.departments.index', compact('departments'));
    }

    // Xem chi tiết phòng ban
    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $department = [];
        try {
            $response = Http::withToken($token)->get("{$apiUrl}/api/admin/departments/{$id}");
            if ($response->successful()) {
                $department = $response->json();
                if (isset($department['data'])) {
                    $department = $department['data'];
                }
            }
        } catch (\Exception $e) {
            $department = [];
        }
        return view('admin.departments.show', compact('department'));
    }

    // Form thêm mới
    public function create()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        // Nếu cần lấy thêm dữ liệu liên quan, thêm ở đây
        return view('admin.departments.create');
    }

    // Xử lý thêm mới
    public function store(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $response = Http::withToken($token)->post("{$apiUrl}/api/admin/departments", $request->all());
            if ($response->successful()) {
                return redirect()->route('admin.departments.index')->with('success', 'Thêm phòng ban thành công!');
            }
            $error = $response->json();
            $errorMessage = 'Lỗi khi thêm phòng ban';
            if (isset($error['message'])) {
                $errorMessage = $error['message'];
            } elseif (isset($error['errors'])) {
                $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
            }
            return back()->withErrors(['error' => $errorMessage])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()])->withInput();
        }
    }

    // Form chỉnh sửa

public function edit($id)
{
    if (!session('admin_token')) {
        return redirect('/admin/login');
    }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $department = [];
        try {
            $response = Http::withToken($token)->get("{$apiUrl}/api/admin/departments/{$id}");
            if ($response->successful()) {
                $department = $response->json();
                if (isset($department['data'])) {
                    $department = $department['data'];
                }
                if (!isset($department['id'])) {
                    return redirect()->route('admin.departments.index')->withErrors('Không tìm thấy phòng ban!');
                }
            } else {
                return redirect()->route('admin.departments.index')->withErrors('Không tìm thấy phòng ban!');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.departments.index')->withErrors('Lỗi kết nối API');
        }
        return view('admin.departments.edit', compact('department'));
}
    // Xử lý cập nhật
    public function update(Request $request, $id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $response = Http::withToken($token)->put("{$apiUrl}/api/admin/departments/{$id}", $request->all());
            if ($response->successful()) {
                return redirect()->route('admin.departments.index')->with('success', 'Cập nhật phòng ban thành công!');
            }
            $error = $response->json();
            $errorMessage = 'Lỗi khi cập nhật phòng ban';
            if (isset($error['message'])) {
                $errorMessage = $error['message'];
            } elseif (isset($error['errors'])) {
                $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
            }
            return back()->withErrors(['error' => $errorMessage])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()])->withInput();
        }
    }

    // Xóa phòng ban
    public function destroy($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $response = Http::withToken($token)->delete("{$apiUrl}/api/admin/departments/{$id}");
            if ($response->successful()) {
                return redirect()->route('admin.departments.index')->with('success', 'Xóa phòng ban thành công!');
            }
            return redirect()->route('admin.departments.index')->with('error', 'Xóa phòng ban thất bại!');
        } catch (\Exception $e) {
            return redirect()->route('admin.departments.index')->with('error', 'Lỗi kết nối API');
        }
    }
}
