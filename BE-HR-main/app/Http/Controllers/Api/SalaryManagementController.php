<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalaryManagementController extends Controller
{
    // Lấy danh sách bản ghi lương
    public function index(Request $request)
    {
        $query = SalaryRecord::with(['employee.department', 'employee.position']);
        
        // Lọc theo tháng
        if ($request->has('month_year')) {
            $query->forMonth($request->month_year);
        }
        
        // Lọc theo trạng thái
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }
        
        // Lọc theo nhân viên
        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }
        
        $salaryRecords = $query->orderBy('month_year', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $salaryRecords
        ]);
    }

    // Tính lương cho tháng (tạo bản ghi lương mới hoặc cập nhật)
    public function calculateForMonth(Request $request)
    {
        $request->validate([
            'month_year' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'employee_ids' => 'array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        $monthYear = $request->month_year;
        $employeeIds = $request->employee_ids;

        // Nếu không chỉ định employee_ids, tính cho tất cả nhân viên
        if (empty($employeeIds)) {
            $employeeIds = Employee::pluck('id')->toArray();
        }

        $results = [];
        foreach ($employeeIds as $employeeId) {
            $salaryRecord = SalaryRecord::createOrUpdateForMonth($employeeId, $monthYear);
            if ($salaryRecord) {
                $results[] = $salaryRecord->load(['employee.department', 'employee.position']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã tính lương cho ' . count($results) . ' nhân viên',
            'data' => $results
        ]);
    }

    // Tạo bản ghi lương mới
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month_year' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'total_hours' => 'nullable|numeric|min:0|max:744',
            'hourly_rate' => 'nullable|numeric|min:0',
            'position_allowance' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'penalty' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,approved,paid'
        ]);

        // Kiểm tra xem đã có bản ghi lương cho nhân viên này trong tháng này chưa
        $existingRecord = SalaryRecord::where('employee_id', $request->employee_id)
                                     ->where('month_year', $request->month_year)
                                     ->first();

        if ($existingRecord) {
            // Lấy thông tin nhân viên để hiển thị thông báo rõ ràng hơn
            $employee = Employee::find($request->employee_id);
            $employeeName = $employee ? $employee->name : 'nhân viên này';
            
            return response()->json([
                'success' => false,
                'message' => "Đã tồn tại bảng lương cho {$employeeName} trong tháng {$request->month_year}. Vui lòng chọn nhân viên khác hoặc tháng khác.",
                'existing_record' => [
                    'id' => $existingRecord->id,
                    'employee_name' => $employeeName,
                    'month_year' => $existingRecord->month_year,
                    'total_salary' => $existingRecord->total_salary,
                    'status' => $existingRecord->status
                ]
            ], 422);
        }

        // Tạo bản ghi lương mới
        $salaryRecord = new SalaryRecord();
        $salaryRecord->employee_id = $request->employee_id;
        $salaryRecord->month_year = $request->month_year;
        $salaryRecord->total_hours = $request->total_hours ?? 0;
        $salaryRecord->hourly_rate = $request->hourly_rate ?? 0;
        $salaryRecord->position_allowance = $request->position_allowance ?? 0;
        $salaryRecord->bonus = $request->bonus ?? 0;
        $salaryRecord->penalty = $request->penalty ?? 0;
        $salaryRecord->notes = $request->notes ?? '';
        $salaryRecord->status = $request->status ?? 'draft';

        // Tính toán lương
        $salaryRecord->calculateSalary();
        $salaryRecord->save();

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo bản ghi lương mới',
            'data' => $salaryRecord->load(['employee.department', 'employee.position'])
        ]);
    }

    // Lấy chi tiết một bản ghi lương
    public function show($id)
    {
        $salaryRecord = SalaryRecord::with(['employee.department', 'employee.position'])
                                   ->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $salaryRecord
        ]);
    }

    // Cập nhật bản ghi lương (thưởng, phạt, ghi chú)
    public function update(Request $request, $id)
    {
        $request->validate([
            'total_hours' => 'nullable|numeric|min:0|max:744',
            'hours_worked' => 'nullable|numeric|min:0|max:744', // Alias cho total_hours
            'hourly_rate' => 'nullable|numeric|min:0',
            'position_allowance' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:1|max:5',
            'bonus' => 'nullable|numeric|min:0',
            'penalty' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,approved,paid'
        ]);

        $salaryRecord = SalaryRecord::findOrFail($id);
        
        // Cập nhật các trường được phép
        if ($request->has('total_hours')) {
            $salaryRecord->total_hours = $request->total_hours;
        }
        // Hỗ trợ alias hours_worked -> total_hours
        if ($request->has('hours_worked')) {
            $salaryRecord->total_hours = $request->hours_worked;
        }
        if ($request->has('hourly_rate')) {
            $salaryRecord->hourly_rate = $request->hourly_rate;
        }
        if ($request->has('position_allowance')) {
            $salaryRecord->position_allowance = $request->position_allowance;
        }
        if ($request->has('bonus')) {
            $salaryRecord->bonus = $request->bonus;
        }
        if ($request->has('penalty')) {
            $salaryRecord->penalty = $request->penalty;
        }
        if ($request->has('notes')) {
            $salaryRecord->notes = $request->notes;
        }
        if ($request->has('status')) {
            $salaryRecord->status = $request->status;
        }
        
        // Handle overtime calculation (if provided)
        if ($request->has('overtime_hours') && $request->has('overtime_rate')) {
            $overtimeAmount = $request->overtime_hours * $salaryRecord->hourly_rate * $request->overtime_rate;
            // Add overtime to bonus or create separate calculation
            $currentBonus = $salaryRecord->bonus ?? 0;
            $salaryRecord->bonus = $currentBonus + $overtimeAmount;
        }
        
        // Tính lại tổng lương
        $salaryRecord->calculateSalary();
        $salaryRecord->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã cập nhật bản ghi lương',
            'data' => $salaryRecord->load(['employee.department', 'employee.position'])
        ]);
    }

    // Tính lại lương (tính lại số giờ từ work_schedules)
    public function recalculate($id)
    {
        $salaryRecord = SalaryRecord::findOrFail($id);
        
        // Tính lại số giờ từ lịch làm việc
        $salaryRecord->calculateHoursFromSchedules();
        
        // Cập nhật lương theo giờ từ employee hiện tại
        $employee = $salaryRecord->employee;
        $salaryRecord->hourly_rate = $employee->hourly_rate ?? 0;
        $salaryRecord->position_allowance = $employee->position->allowance ?? 0;
        
        // Tính lại tổng lương
        $salaryRecord->calculateSalary();
        $salaryRecord->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Đã tính lại lương',
            'data' => $salaryRecord->load(['employee.department', 'employee.position'])
        ]);
    }

    // Duyệt bản ghi lương
    public function approve(Request $request)
    {
        $request->validate([
            'salary_record_ids' => 'required|array',
            'salary_record_ids.*' => 'exists:salary_records,id'
        ]);

        $updatedCount = SalaryRecord::whereIn('id', $request->salary_record_ids)
                                   ->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => "Đã duyệt $updatedCount bản ghi lương"
        ]);
    }

    // Đánh dấu đã trả lương
    public function markAsPaid(Request $request)
    {
        $request->validate([
            'salary_record_ids' => 'required|array',
            'salary_record_ids.*' => 'exists:salary_records,id'
        ]);

        $updatedCount = SalaryRecord::whereIn('id', $request->salary_record_ids)
                                   ->where('status', 'approved')
                                   ->update(['status' => 'paid']);

        return response()->json([
            'success' => true,
            'message' => "Đã đánh dấu trả lương cho $updatedCount bản ghi"
        ]);
    }

    // Báo cáo lương theo tháng
    public function monthlyReport(Request $request)
    {
        $request->validate([
            'month_year' => 'required|string|regex:/^\d{4}-\d{2}$/'
        ]);

        $monthYear = $request->month_year;
        
        $salaryRecords = SalaryRecord::with(['employee.department', 'employee.position'])
                                    ->forMonth($monthYear)
                                    ->get();

        $summary = [
            'total_employees' => $salaryRecords->count(),
            'total_hours' => $salaryRecords->sum('total_hours'),
            'total_base_salary' => $salaryRecords->sum('base_salary'),
            'total_allowance' => $salaryRecords->sum('position_allowance'),
            'total_bonus' => $salaryRecords->sum('bonus'),
            'total_penalty' => $salaryRecords->sum('penalty'),
            'total_salary' => $salaryRecords->sum('total_salary'),
            'by_status' => [
                'draft' => $salaryRecords->where('status', 'draft')->count(),
                'approved' => $salaryRecords->where('status', 'approved')->count(),
                'paid' => $salaryRecords->where('status', 'paid')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'month_year' => $monthYear,
                'summary' => $summary,
                'details' => $salaryRecords
            ]
        ]);
    }

    // EMPLOYEE METHODS - Xem lương cá nhân
    public function mySalaryRecords(Request $request)
    {
        $employee = Auth::user();
        
        $query = SalaryRecord::where('employee_id', $employee->id)
                            ->with(['employee.department', 'employee.position']);
        
        // Lọc theo tháng nếu có
        if ($request->has('month_year')) {
            $query->forMonth($request->month_year);
        }
        
        $salaryRecords = $query->orderBy('month_year', 'desc')
                              ->paginate(12);
        
        return response()->json([
            'success' => true,
            'data' => $salaryRecords
        ]);
    }

    public function mySalaryRecordDetail($id)
    {
        $employee = Auth::user();
        
        $salaryRecord = SalaryRecord::where('employee_id', $employee->id)
                                   ->where('id', $id)
                                   ->with(['employee.department', 'employee.position'])
                                   ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => $salaryRecord
        ]);
    }
}
