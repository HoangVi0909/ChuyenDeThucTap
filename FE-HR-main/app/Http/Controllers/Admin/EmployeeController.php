<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        $employees = [];
        try {
            $response = \Illuminate\Support\Facades\Http::withToken($token)->get($apiUrl . '/api/admin/employees');
            if ($response->successful()) {
                $employees = $response->json();
                if (isset($employees['data'])) {
                    $employees = $employees['data'];
                }
            }
        } catch (\Exception $e) {
            $employees = [];
        }
        return view('admin.employees.index', compact('employees'));
    }
    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $response = \Illuminate\Support\Facades\Http::withToken($token)->get("{$apiUrl}/api/admin/employees/{$id}");
        $employee = $response->successful() ? $response->json() : [];
        return view('admin.employees.show', ['employee' => $employee]);
    }
    public function create()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $departmentsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$apiUrl}/api/admin/departments");
            $positionsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$apiUrl}/api/admin/positions");
            $departments = $departmentsResponse->successful() ? $departmentsResponse->json() : [];
            $positions = $positionsResponse->successful() ? $positionsResponse->json() : [];
            // Nếu dữ liệu là dạng resource collection, lấy về mảng data
            if (isset($departments['data'])) {
                $departments = $departments['data'];
            }
            if (isset($positions['data'])) {
                $positions = $positions['data'];
            }
        } catch (\Exception $e) {
            $departments = [];
            $positions = [];
        }
        return view('admin.employees.create', compact('departments', 'positions'));
    }
    public function store(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->except(['_token', 'photo']);
            $response = \Illuminate\Support\Facades\Http::withToken($token)->post("{$apiUrl}/api/admin/employees", $data);
            if ($response->successful()) {
                $employee = $response->json();
                $employeeId = $employee['id'] ?? null;
                if ($employeeId && $request->hasFile('photo')) {
                    $file = $request->file('photo');
                    $uploadResponse = \Illuminate\Support\Facades\Http::withToken($token)
                        ->attach('photo', file_get_contents($file->path()), $file->getClientOriginalName())
                        ->post("{$apiUrl}/api/admin/employees/{$employeeId}/upload-photo");
                    if ($uploadResponse->successful()) {
                        $photoData = $uploadResponse->json();
                        \Illuminate\Support\Facades\Http::withToken($token)
                            ->put("{$apiUrl}/api/admin/employees/{$employeeId}", ['photo' => $photoData['photo_path']]);
                    }
                }
                return redirect()->route('admin.employees.index')->with('success', 'Thêm nhân viên thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi thêm nhân viên';
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
        $employeeResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$apiUrl}/api/admin/employees/{$id}");
        $departmentsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$apiUrl}/api/admin/departments");
        $positionsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$apiUrl}/api/admin/positions");
        $employee = $employeeResponse->successful() ? $employeeResponse->json() : [];
        $departments = $departmentsResponse->successful() ? $departmentsResponse->json() : [];
        $positions = $positionsResponse->successful() ? $positionsResponse->json() : [];
        if (isset($departments['data'])) {
            $departments = $departments['data'];
        }
        if (isset($positions['data'])) {
            $positions = $positions['data'];
        }
    return view('admin.employees.edit', compact('employee', 'departments', 'positions'));
    }
    public function update(Request $request, $id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->except(['_token', '_method', 'photo']);
            // Chỉ thêm password nếu người dùng nhập mới
            if (empty($request->password)) {
                unset($data['password']);
            }
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $response = \Illuminate\Support\Facades\Http::withToken($token)
                    ->attach('photo', file_get_contents($file->path()), $file->getClientOriginalName())
                    ->post("{$apiUrl}/api/admin/employees/{$id}/upload-photo");
                if ($response->successful()) {
                    $photoData = $response->json();
                    $data['photo'] = $photoData['photo_path'];
                } else {
                    return back()->withErrors(['error' => 'Lỗi upload ảnh: ' . $response->body()])->withInput();
                }
            }
            $response = \Illuminate\Support\Facades\Http::withToken($token)->put("{$apiUrl}/api/admin/employees/{$id}", $data);
            if ($response->successful()) {
                return redirect()->route('admin.employees.index')->with('success', 'Cập nhật nhân viên thành công!');
            } else {
                return back()->withErrors(['error' => 'Lỗi khi cập nhật nhân viên'])->withInput();
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
        $response = \Illuminate\Support\Facades\Http::withToken($token)->delete("{$apiUrl}/api/admin/employees/{$id}");
        if ($response->successful()) {
            return redirect()->route('admin.employees.index')->with('success', 'Xóa nhân viên thành công!');
        } else {
            return redirect()->route('admin.employees.index')->with('error', 'Xóa nhân viên thất bại!');
        }
    }
}
