<?php

namespace App\Http\Controllers;

use App\Models\SalaryRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
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
            'bonus' => 'nullable|numeric|min:0',
            'penalty' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|in:draft,approved,paid'
        ]);

        $salaryRecord = SalaryRecord::findOrFail($id);
        
        // Cập nhật các trường được phép
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
