<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $baseUrl = config('services.backend_api.url');
        $token = session('admin_token');
        $employees = [];
        $departments = [];
        $positions = [];
        
        try {
            // Lấy danh sách phòng ban cho dropdown
            $deptResponse = Http::withToken($token)->get($baseUrl . '/api/admin/departments');
            if ($deptResponse->successful()) {
                $deptData = $deptResponse->json();
                $departments = isset($deptData['data']) ? $deptData['data'] : $deptData;
            }
            
            // Lấy danh sách vị trí cho dropdown
            $posResponse = Http::withToken($token)->get($baseUrl . '/api/admin/positions');
            if ($posResponse->successful()) {
                $posData = $posResponse->json();
                $positions = isset($posData['data']) ? $posData['data'] : $posData;
            }
            
            // Tạo query parameters cho API
            $queryParams = [];
            if ($request->has('search') && $request->search) {
                $queryParams['search'] = $request->search;
            }
            if ($request->has('department_id') && $request->department_id) {
                $queryParams['department_id'] = $request->department_id;
            }
            if ($request->has('position_id') && $request->position_id) {
                $queryParams['position_id'] = $request->position_id;
            }
            if ($request->has('gender') && $request->gender) {
                $queryParams['gender'] = $request->gender;
            }
            
            // Gọi API với query parameters
            $url = $baseUrl . '/api/admin/employees';
            if (!empty($queryParams)) {
                $url .= '?' . http_build_query($queryParams);
            }
            
            $response = Http::withToken($token)->get($url);
            if ($response->successful()) {
                $employees = $response->json();
                if (isset($employees['data'])) {
                    $employees = $employees['data'];
                }
            }
        } catch (\Exception $e) {
            $employees = [];
            Log::error('Employee index error: ' . $e->getMessage());
        }
        
        return view('admin.employees.index', compact('employees', 'departments', 'positions'));
    }
    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            // Lấy thông tin nhân viên
            $employeeResponse = Http::withToken($token)->get("{$baseUrl}/api/admin/employees/{$id}");
            $employee = $employeeResponse->successful() ? $employeeResponse->json() : [];
            
            // Lấy lịch sử lương của nhân viên
            $salariesResponse = Http::withToken($token)->get("{$baseUrl}/api/admin/salary-management", [
                'employee_id' => $id
            ]);
            
            $salaries = [];
            if ($salariesResponse->successful()) {
                $salaryData = $salariesResponse->json();
                if (isset($salaryData['success']) && $salaryData['success'] && isset($salaryData['data']['data'])) {
                    $salaries = $salaryData['data']['data'];
                } elseif (isset($salaryData['data'])) {
                    $salaries = is_array($salaryData['data']) ? $salaryData['data'] : [];
                }
            }
            
            return view('admin.employees.show', [
                'employee' => $employee,
                'salaries' => $salaries
            ]);
        } catch (\Exception $e) {
            Log::error('Employee show error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Lỗi khi tải thông tin nhân viên']);
        }
    }
    public function create()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        try {
            $departmentsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$baseUrl}/api/admin/departments");
            $positionsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$baseUrl}/api/admin/positions");
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
        $baseUrl = config('services.backend_api.url');
        try {
            $data = $request->except(['_token', 'photo']);
            $response = \Illuminate\Support\Facades\Http::withToken($token)->post("{$baseUrl}/api/admin/employees", $data);
            if ($response->successful()) {
                $employee = $response->json();
                $employeeId = $employee['id'] ?? null;
                if ($employeeId && $request->hasFile('photo')) {
                    $file = $request->file('photo');
                    $uploadResponse = \Illuminate\Support\Facades\Http::withToken($token)
                        ->attach('photo', file_get_contents($file->path()), $file->getClientOriginalName())
                        ->post("{$baseUrl}/api/admin/employees/{$employeeId}/upload-photo");
                    if ($uploadResponse->successful()) {
                        $photoData = $uploadResponse->json();
                        \Illuminate\Support\Facades\Http::withToken($token)
                            ->put("{$baseUrl}/api/admin/employees/{$employeeId}", ['photo' => $photoData['photo_path']]);
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
        $baseUrl = config('services.backend_api.url');
        $employeeResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$baseUrl}/api/admin/employees/{$id}");
        $departmentsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$baseUrl}/api/admin/departments");
        $positionsResponse = \Illuminate\Support\Facades\Http::withToken($token)->get("{$baseUrl}/api/admin/positions");
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
        $baseUrl = config('services.backend_api.url');
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
                    ->post("{$baseUrl}/api/admin/employees/{$id}/upload-photo");
                if ($response->successful()) {
                    $photoData = $response->json();
                    $data['photo'] = $photoData['photo_path'];
                } else {
                    return back()->withErrors(['error' => 'Lỗi upload ảnh: ' . $response->body()])->withInput();
                }
            }
            $response = \Illuminate\Support\Facades\Http::withToken($token)->put("{$baseUrl}/api/admin/employees/{$id}", $data);
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
        $baseUrl = config('services.backend_api.url');
        $response = \Illuminate\Support\Facades\Http::withToken($token)->delete("{$baseUrl}/api/admin/employees/{$id}");
        if ($response->successful()) {
            return redirect()->route('admin.employees.index')->with('success', 'Xóa nhân viên thành công!');
        } else {
            return redirect()->route('admin.employees.index')->with('error', 'Xóa nhân viên thất bại!');
        }
    }
}