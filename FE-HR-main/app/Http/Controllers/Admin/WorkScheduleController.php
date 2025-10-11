<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class WorkScheduleController extends Controller
{
    private $apiBaseUrl = 'http://127.0.0.1:8000/api';

    /**
     * Hiển thị danh sách lịch làm việc
     */
    public function index(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        $workSchedules = [];
        $employees = [];
        $error = null;
        $page = $request->input('page', 1);
        $perPage = 6;
        try {
            $wsRes = Http::withToken($token)->get($apiUrl . "/api/admin/work-schedules?page=$page&per_page=$perPage");
            $wsData = $wsRes->json();
            if ($wsRes->successful() && isset($wsData['data'])) {
                $workSchedules = $wsData['data'];
                $total = $wsData['total'] ?? count($workSchedules);
                $paginator = new \Illuminate\Pagination\LengthAwarePaginator($workSchedules, $total, $perPage, $page, [
                    'path' => url()->current(),
                    'query' => $request->query(),
                ]);
            } else {
                $paginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, $page);
            }
            $empRes = Http::withToken($token)->get($apiUrl . '/api/admin/employees');
            if ($empRes->successful()) {
                $employees = $empRes->json();
                if (isset($employees['data'])) {
                    $employees = $employees['data'];
                }
            }
        } catch (\Exception $e) {
            $error = 'Lỗi kết nối: ' . $e->getMessage();
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, $page);
        }
        return view('admin.work_schedules.index', [
            'workSchedules' => $paginator,
            'employees' => collect($employees),
            'error' => $error
        ]);
    }

    /**
     * Hiển thị form tạo mới lịch làm việc
     */
    public function create()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $employees = Http::withToken($token)->get($apiUrl . '/api/admin/employees')->json() ?? [];
            if (isset($employees['data'])) {
                $employees = $employees['data'];
            }
            return view('admin.work_schedules.create', ['employees' => collect($employees)]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    /**
     * Lưu lịch làm việc mới
     */
    public function store(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->validate([
                'employee_id' => 'required|integer|min:1',
                'work_date' => 'required|date',
                'shift' => 'required|in:S,C'
            ]);
            $response = Http::withToken($token)->post($apiUrl . '/api/admin/work-schedules', $data);
            if ($response->successful()) {
                return redirect()->route('admin.work-schedules.index')
                    ->with('success', 'Thêm lịch làm việc thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi thêm lịch làm việc';
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Hiển thị thông tin một lịch làm việc
     */
    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $workSchedule = Http::withToken($token)->get($apiUrl . "/api/admin/work-schedules/{$id}")->json() ?? [];
            if (isset($workSchedule['data'])) {
                $workSchedule = $workSchedule['data'];
            }
            return view('admin.work_schedules.show', ['workSchedule' => $workSchedule]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form chỉnh sửa lịch làm việc
     */
    public function edit($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $workSchedule = Http::withToken($token)->get($apiUrl . "/api/admin/work-schedules/{$id}")->json() ?? [];
            if (isset($workSchedule['data'])) {
                $workSchedule = $workSchedule['data'];
            }
            
            // Đảm bảo work_date có định dạng đúng cho input date
            if (isset($workSchedule['work_date'])) {
                $workSchedule['work_date'] = date('Y-m-d', strtotime($workSchedule['work_date']));
            }
            
            $employees = Http::withToken($token)->get($apiUrl . '/api/admin/employees')->json() ?? [];
            if (isset($employees['data'])) {
                $employees = $employees['data'];
            }
            return view('admin.work_schedules.edit', [
                'workSchedule' => $workSchedule,
                'employees' => collect($employees)
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối: ' . $e->getMessage()]);
        }
    }

    /**
     * Cập nhật lịch làm việc
     */
    public function update(Request $request, $id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->validate([
                'employee_id' => 'required|integer|min:1',
                'work_date' => 'required|date',
                'shift' => 'required|in:S,C'
            ]);
            $response = Http::withToken($token)->put($apiUrl . "/api/admin/work-schedules/{$id}", $data);
            if ($response->successful()) {
                return redirect()->route('admin.work-schedules.index')
                    ->with('success', 'Cập nhật lịch làm việc thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi cập nhật lịch làm việc';
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Xóa lịch làm việc
     */
    public function destroy($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $response = Http::withToken($token)->delete($apiUrl . "/api/admin/work-schedules/{$id}");
            if ($response->successful()) {
                return redirect()->route('admin.work-schedules.index')
                    ->with('success', 'Xóa lịch làm việc thành công!');
            } else {
                return back()->withErrors(['error' => 'Lỗi khi xóa lịch làm việc']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
}