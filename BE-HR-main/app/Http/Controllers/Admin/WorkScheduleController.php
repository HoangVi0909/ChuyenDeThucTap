<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkSchedule;
use App\Models\Employee;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    public function index()
    {
        $workSchedules = WorkSchedule::with('employee')->get();
        return view('admin.work_schedules.index', compact('workSchedules'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.work_schedules.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'total_hours' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:present,absent,late,early_leave'
        ]);

        WorkSchedule::create($request->all());

        return redirect()->route('admin.work_schedules.index')
                        ->with('success', 'Lịch làm việc đã được tạo thành công!');
    }

    public function show(WorkSchedule $workSchedule)
    {
        $workSchedule->load('employee');
        return view('admin.work_schedules.show', compact('workSchedule'));
    }

    public function edit(WorkSchedule $workSchedule)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.work_schedules.edit', compact('workSchedule', 'employees'));
    }

    public function update(Request $request, WorkSchedule $workSchedule)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'work_date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i|after:break_start_time',
            'total_hours' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'status' => 'required|in:present,absent,late,early_leave'
        ]);

        $workSchedule->update($request->all());

        return redirect()->route('admin.work_schedules.index')
                        ->with('success', 'Lịch làm việc đã được cập nhật thành công!');
    }

    public function destroy(WorkSchedule $workSchedule)
    {
        $workSchedule->delete();

        return redirect()->route('admin.work_schedules.index')
                        ->with('success', 'Lịch làm việc đã được xóa thành công!');
    }
}