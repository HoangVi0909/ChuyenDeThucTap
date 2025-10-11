<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    private $apiBaseUrl = 'http://localhost:8000/api';
    
    /**
     * Hiển thị danh sách lương
     */
    public function index()
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . '/salaries');
            
            if ($response->successful()) {
                $salaries = $response->json();
                Log::info('Salaries loaded from API', ['count' => count($salaries)]);
                
                return view('admin.salaries.index', [
                    'salaries' => $salaries,
                    'error' => null
                ]);
            } else {
                Log::warning('Failed to load salaries', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return view('admin.salaries.index', [
                    'salaries' => [],
                    'error' => 'Không thể tải dữ liệu từ server backend (HTTP: ' . $response->status() . ')'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception loading salaries', ['error' => $e->getMessage()]);
            
            return view('admin.salaries.index', [
                'salaries' => [],
                'error' => 'Lỗi kết nối: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hiển thị form tạo mới lương
     */
    public function create()
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . '/employees');
            
            if ($response->successful()) {
                $employees = collect($response->json());
                Log::info('Employees loaded from API', ['count' => $employees->count()]);
                
                return view('admin.salaries.create', compact('employees'));
            } else {
                Log::warning('Failed to fetch employees from API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return back()->withErrors(['error' => 'Không thể tải danh sách nhân viên']);
            }
        } catch (\Exception $e) {
            Log::error('Exception when fetching employees from API', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    /**
     * Lưu lương mới
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'employee_id' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'month' => 'required|date_format:Y-m',
                'note' => 'nullable|string|max:1000'
            ]);

            $response = Http::timeout(10)->post($this->apiBaseUrl . '/salaries', $data);
            
            if ($response->successful()) {
                return redirect()->route('admin.salaries.index')
                    ->with('success', 'Thêm lương thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi thêm lương';
                
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                
                Log::warning('Salary store failed', [
                    'status' => $response->status(),
                    'error' => $error,
                    'data' => $data
                ]);
                
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Salary store failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Hiển thị thông tin một bản ghi lương
     */
    public function show($id)
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . "/salaries/{$id}");
            
            if ($response->successful()) {
                $salary = $response->json();
                return view('admin.salaries.show', compact('salary'));
            } else {
                return back()->withErrors(['error' => 'Không tìm thấy bản ghi lương']);
            }
        } catch (\Exception $e) {
            Log::error('Exception loading salary', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form chỉnh sửa lương
     */
    public function edit($id)
    {
        try {
            // Load salary data
            $salaryResponse = Http::timeout(10)->get($this->apiBaseUrl . "/salaries/{$id}");
            
            if (!$salaryResponse->successful()) {
                return back()->withErrors(['error' => 'Không tìm thấy bản ghi lương']);
            }
            
            // Load employees
            $employeesResponse = Http::timeout(10)->get($this->apiBaseUrl . '/employees');
            
            if (!$employeesResponse->successful()) {
                return back()->withErrors(['error' => 'Không thể tải danh sách nhân viên']);
            }
            
            $salary = $salaryResponse->json();
            $employees = collect($employeesResponse->json());
            
            return view('admin.salaries.edit', compact('salary', 'employees'));
        } catch (\Exception $e) {
            Log::error('Exception loading salary for edit', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    /**
     * Cập nhật lương
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'employee_id' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'month' => 'required|date_format:Y-m',
                'note' => 'nullable|string|max:1000'
            ]);

            $response = Http::timeout(10)->put($this->apiBaseUrl . "/salaries/{$id}", $data);
            
            if ($response->successful()) {
                return redirect()->route('admin.salaries.index')
                    ->with('success', 'Cập nhật lương thành công!');
            } else {
                $error = $response->json();
                $errorMessage = isset($error['message']) ? $error['message'] : 'Lỗi khi cập nhật lương';
                
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Salary update failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Xóa lương
     */
    public function destroy($id)
    {
        try {
            $response = Http::timeout(10)->delete($this->apiBaseUrl . "/salaries/{$id}");
            
            if ($response->successful()) {
                return redirect()->route('admin.salaries.index')
                    ->with('success', 'Xóa lương thành công!');
            } else {
                $error = $response->json();
                $errorMessage = isset($error['message']) ? $error['message'] : 'Lỗi khi xóa lương';
                
                return back()->withErrors(['error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            Log::error('Salary delete failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}