<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        
        $baseUrl = config('services.backend_api.url');
        $token = session('admin_token');
        $salaries = [];
        $employees = [];
        
        try {
            // Lấy danh sách nhân viên cho dropdown filter
            $empResponse = Http::withToken($token)->get($baseUrl . '/api/admin/employees');
            if ($empResponse->successful()) {
                $empData = $empResponse->json();
                $employees = isset($empData['data']) ? $empData['data'] : $empData;
                // Debug log
                Log::info('Employees data:', ['employees' => $employees]);
            }
            
            // Tạo query parameters cho API
            $queryParams = [];
            if ($request->has('employee_id') && $request->employee_id) {
                $queryParams['employee_id'] = $request->employee_id;
            }
            if ($request->has('month') && $request->month) {
                $queryParams['month'] = $request->month;
            }
            if ($request->has('status') && $request->status) {
                $queryParams['status'] = $request->status;
            }
            
            // Gọi API với query parameters
            $url = $baseUrl . '/api/admin/salary-management';
            if (!empty($queryParams)) {
                $url .= '?' . http_build_query($queryParams);
            }
            
            $response = Http::withToken($token)->get($url);
            if ($response->successful()) {
                $responseData = $response->json();
                
                // Debug raw response structure
                Log::info('Raw API response structure:', [
                    'keys' => array_keys($responseData),
                    'has_salaries' => isset($responseData['salaries']),
                    'has_data' => isset($responseData['data']),
                    'success' => $responseData['success'] ?? 'not_set'
                ]);
                
                // Handle new API response structure: {"success": true, "data": paginated_data}
                if (isset($responseData['success']) && $responseData['success'] && isset($responseData['data'])) {
                    $paginatedData = $responseData['data'];
                    
                    // Extract actual salary records from paginated structure
                    if (isset($paginatedData['data'])) {
                        $salaries = $paginatedData['data']; // Laravel pagination format
                        Log::info('Using paginated data.data path');
                    } else {
                        $salaries = $paginatedData;
                        Log::info('Using paginated data directly');
                    }
                } elseif (isset($responseData['salaries']['data'])) {
                    $salaries = $responseData['salaries']['data'];
                    Log::info('Using old salaries.data path');
                } elseif (isset($responseData['data'])) {
                    $salaries = $responseData['data'];
                    Log::info('Using direct data path');
                } else {
                    $salaries = [];
                    Log::warning('No recognizable data structure found');
                }
                
                // Debug log - only log the actual salary records
                Log::info('Final salaries data:', [
                    'count' => is_array($salaries) ? count($salaries) : 0, 
                    'first_item' => is_array($salaries) && count($salaries) > 0 ? $salaries[0] : 'empty_array'
                ]);
            } else {
                Log::warning('Salary API failed:', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $salaries = [];
            Log::error('Salary index error: ' . $e->getMessage());
        }
        
        // Đảm bảo $employees và $salaries là array
        if (!is_array($employees)) {
            $employees = [];
        }
        if (!is_array($salaries)) {
            $salaries = [];
        }
        
        return view('admin.salaries.index', compact('salaries', 'employees'));
    }

    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            $response = Http::withToken($token)->get("{$baseUrl}/api/admin/salary-management/{$id}");
            $salary = $response->successful() ? $response->json() : [];
            return view('admin.salaries.show', ['salary' => $salary]);
        } catch (\Exception $e) {
            Log::error('Salary show error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    public function create(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            $employeesResponse = Http::withToken($token)->get("{$baseUrl}/api/admin/employees");
            $employees = $employeesResponse->successful() ? $employeesResponse->json() : [];
            
            // Nếu dữ liệu là dạng resource collection, lấy về mảng data
            if (isset($employees['data'])) {
                $employees = $employees['data'];
            }
            
            // Lấy employee_id từ URL nếu có
            $selectedEmployeeId = $request->get('employee_id');
            
        } catch (\Exception $e) {
            $employees = [];
            $selectedEmployeeId = null;
            Log::error('Salary create error: ' . $e->getMessage());
        }
        
        return view('admin.salaries.create', compact('employees', 'selectedEmployeeId'));
    }

    public function store(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            // Log input data để debug
            Log::info('Store salary request data:', $request->all());
            
            // Chuẩn bị dữ liệu để gửi API
            $data = [
                'employee_id' => $request->employee_id,
                'month_year' => $request->month_year,
                'total_hours' => $request->total_hours ?? 0,
                'hourly_rate' => $request->hourly_rate ?? 0,
                'position_allowance' => $request->position_allowance ?? 0,
                'bonus' => $request->bonus ?? 0,
                'penalty' => $request->penalty ?? 0,
                'status' => $request->status ?? 'draft',
                'notes' => $request->notes ?? ''
            ];
            
            Log::info('Sending data to API:', $data);
            
            // Gọi API tạo mới salary record
            $response = Http::withToken($token)->post("{$baseUrl}/api/admin/salary-management", $data);
            
            Log::info('API Response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            
            if ($response->successful()) {
                return redirect()->route('admin.salaries.index')->with('success', 'Thêm lương thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi thêm lương';
                
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                    
                    // Kiểm tra nếu là lỗi trùng lặp
                    if (strpos($error['message'], 'Đã tồn tại') !== false) {
                        // Thêm thông tin chi tiết nếu có
                        if (isset($error['existing_record'])) {
                            $existing = $error['existing_record'];
                            $statusText = $this->getStatusText($existing['status']);
                            $errorMessage .= "\n\nThông tin bảng lương hiện có:";
                            $errorMessage .= "\n- Tổng lương: " . number_format($existing['total_salary']) . " VNĐ";
                            $errorMessage .= "\n- Trạng thái: " . $statusText;
                        }
                        
                        return back()->withErrors(['duplicate' => $errorMessage])->withInput();
                    }
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                
                Log::error('API Error:', $error);
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Salary store error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()])->withInput();
        }
    }

    private function getStatusText($status)
    {
        switch($status) {
            case 'draft': return 'Chờ duyệt';
            case 'approved': return 'Đã duyệt';
            case 'paid': return 'Đã trả';
            default: return $status;
        }
    }

    public function edit($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            $salaryResponse = Http::withToken($token)->get("{$baseUrl}/api/admin/salary-management/{$id}");
            $employeesResponse = Http::withToken($token)->get("{$baseUrl}/api/admin/employees");
            
            $salaryData = $salaryResponse->successful() ? $salaryResponse->json() : [];
            $employeesData = $employeesResponse->successful() ? $employeesResponse->json() : [];
            
            // Handle salary response structure 
            if (isset($salaryData['success']) && $salaryData['success'] && isset($salaryData['data'])) {
                $salary = $salaryData['data'];
            } elseif (isset($salaryData['salary'])) {
                $salary = $salaryData['salary'];
            } else {
                $salary = $salaryData;
            }
            
            // Handle employees response structure
            if (isset($employeesData['data'])) {
                $employees = $employeesData['data'];
            } elseif (isset($employeesData['employees'])) {
                $employees = $employeesData['employees'];
            } else {
                $employees = $employeesData;
            }
            
            // Debug log
            Log::info('Edit salary data:', ['salary' => $salary, 'has_id' => isset($salary['id'])]);
            
            return view('admin.salaries.edit', compact('salary', 'employees'));
        } catch (\Exception $e) {
            Log::error('Salary edit error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            // Chuẩn bị dữ liệu để gửi API (mapping fields)
            $data = [];
            
            // Map các trường từ frontend sang backend
            if ($request->filled('total_hours')) {
                $data['total_hours'] = $request->total_hours;
            }
            if ($request->filled('hourly_rate')) {
                $data['hourly_rate'] = $request->hourly_rate;
            }
            if ($request->filled('position_allowance')) {
                $data['position_allowance'] = $request->position_allowance;
            }
            if ($request->filled('bonus')) {
                $data['bonus'] = $request->bonus;
            }
            if ($request->filled('penalty')) {
                $data['penalty'] = $request->penalty;
            }
            if ($request->filled('notes')) {
                $data['notes'] = $request->notes;
            }
            if ($request->filled('status')) {
                $data['status'] = $request->status;
            }

            // Debug log
            Log::info('Salary update request:', [
                'id' => $id,
                'original_data' => $request->except(['_token', '_method']),
                'mapped_data' => $data
            ]);
            
            $response = Http::withToken($token)->put("{$baseUrl}/api/admin/salary-management/{$id}", $data);
            
            // Debug response
            Log::info('Salary update response:', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            
            if ($response->successful()) {
                return redirect()->route('admin.salaries.index')->with('success', 'Cập nhật lương thành công!');
            } else {
                $error = $response->json();
                $errorMessage = isset($error['message']) ? $error['message'] : 'Lỗi khi cập nhật lương';
                
                if (isset($error['errors'])) {
                    $errorMessage .= ' - Validation: ' . json_encode($error['errors']);
                }
                
                Log::error('Salary update failed:', [
                    'status' => $response->status(),
                    'error' => $error
                ]);
                
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            Log::error('Salary update error: ' . $e->getMessage());
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
        
        try {
            $response = Http::withToken($token)->delete("{$baseUrl}/api/admin/salary-management/{$id}");
            
            if ($response->successful()) {
                return redirect()->route('admin.salaries.index')->with('success', 'Xóa lương thành công!');
            } else {
                $error = $response->json();
                $errorMessage = isset($error['message']) ? $error['message'] : 'Lỗi khi xóa lương';
                
                return back()->withErrors(['error' => $errorMessage]);
            }
        } catch (\Exception $e) {
            Log::error('Salary delete error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()]);
        }
    }

    /**
     * Tính lương cho tháng
     */
    public function calculateSalary(Request $request)
    {
        if (!session('admin_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            $data = $request->only(['employee_id', 'month']);
            $response = Http::withToken($token)->post("{$baseUrl}/api/admin/salary-management/calculate", $data);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Không thể tính lương'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Calculate salary error: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi kết nối API'], 500);
        }
    }

    /**
     * Duyệt lương
     */
    public function approve(Request $request)
    {
        if (!session('admin_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            $data = $request->only(['ids']);
            $response = Http::withToken($token)->post("{$baseUrl}/api/admin/salary-management/approve", $data);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Không thể duyệt lương'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Approve salary error: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi kết nối API'], 500);
        }
    }

    /**
     * Đánh dấu đã trả lương
     */
    public function markAsPaid(Request $request)
    {
        if (!session('admin_token')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');
        
        try {
            $data = $request->only(['ids']);
            $response = Http::withToken($token)->post("{$baseUrl}/api/admin/salary-management/mark-as-paid", $data);
            
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Không thể đánh dấu đã trả lương'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Mark as paid error: ' . $e->getMessage());
            return response()->json(['error' => 'Lỗi kết nối API'], 500);
        }
    }
}