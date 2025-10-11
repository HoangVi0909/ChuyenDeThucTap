<?php

namespace App\Http\Controllers\Api;

use App\Models\WorkSchedule;
use App\Models\Employee;
use App\Http\Requests\WorkScheduleRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    /**
     * Lấy danh sách lịch làm việc
     */
    public function index(Request $request)
    {
        try {
            $query = WorkSchedule::with('employee:id,username,name');

            // Lọc theo tháng nếu có
            if ($request->has('month') && $request->has('year')) {
                $query->inMonth($request->month, $request->year);
            }

            // Lọc theo nhân viên nếu có
            if ($request->has('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            // Lọc theo tuần nếu có
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->inWeek($request->start_date, $request->end_date);
            }

            $perPage = $request->get('per_page', 10);
            $workSchedules = $query->orderBy('work_date', 'desc')
                ->orderBy('employee_id')
                ->paginate($perPage);

            return response()->json($workSchedules);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không thể tải danh sách lịch làm việc',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin một lịch làm việc
     */
    public function show($id)
    {
        try {
            $workSchedule = WorkSchedule::with('employee:id,name')->findOrFail($id);
            return response()->json($workSchedule);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không tìm thấy lịch làm việc',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Tạo mới lịch làm việc
     */
    public function store(WorkScheduleRequest $request)
    {
        try {
            // Kiểm tra trung lịch
            $existing = WorkSchedule::where('employee_id', $request->employee_id)
                ->where('work_date', $request->work_date)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhân viên đã có lịch làm việc trong ngày này'
                ], 422);
            }

            $workSchedule = WorkSchedule::create($request->validated());
            $workSchedule->load('employee:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Tạo lịch làm việc thành công',
                'data' => $workSchedule
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo lịch làm việc: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật lịch làm việc
     */
    public function update(WorkScheduleRequest $request, $id)
    {
        try {
            $workSchedule = WorkSchedule::findOrFail($id);

            // Kiểm tra trung lịch (ngoại trừ bản ghi hiện tại)
            $existing = WorkSchedule::where('employee_id', $request->employee_id)
                ->where('work_date', $request->work_date)
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhân viên đã có lịch làm việc trong ngày này'
                ], 422);
            }

            $workSchedule->update($request->validated());
            $workSchedule->load('employee:id,name');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lịch làm việc thành công',
                'data' => $workSchedule
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa lịch làm việc
     */
    public function destroy($id)
    {
        try {
            $workSchedule = WorkSchedule::findOrFail($id);
            $workSchedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch làm việc thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa: ' . $e->getMessage()
            ], 500);
        }
    }
}
