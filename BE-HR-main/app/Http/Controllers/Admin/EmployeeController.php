<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department', 'position'])->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::where('status', 'active')->get();
        $positions = Position::where('status', 'active')->get();
        return view('admin.employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:Nam,Nữ,Khác',
            'email' => 'required|email|unique:employees',
            'username' => 'required|string|unique:employees',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'hire_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:active,inactive,terminated'
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        Employee::create($data);

        return redirect()->route('admin.employees.index')
                        ->with('success', 'Nhân viên đã được tạo thành công!');
    }

    public function show(Employee $employee)
    {
        $employee->load(['department', 'position']);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::where('status', 'active')->get();
        $positions = Position::where('status', 'active')->get();
        return view('admin.employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'nullable|in:Nam,Nữ,Khác',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'username' => 'required|string|unique:employees,username,' . $employee->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'hire_date' => 'required|date',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:active,inactive,terminated'
        ]);

        $data = $request->all();
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')
                        ->with('success', 'Nhân viên đã được cập nhật thành công!');
    }

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            return redirect()->route('admin.employees.index')
                ->with('success', 'Nhân viên đã được xóa thành công!');
        } catch (\Illuminate\Database\QueryException $e) {
            $msg = 'Không thể xóa nhân viên vì còn ràng buộc dữ liệu.';
            $detail = '';
            $error = $e->getMessage();
            if (strpos($error, 'salary') !== false) {
                $detail .= ' (Bảng lương)';
            }
            if (strpos($error, 'leave') !== false) {
                $detail .= ' (Đơn xin nghỉ phép)';
            }
            if (strpos($error, 'notification') !== false) {
                $detail .= ' (Thông báo)';
            }
            if (strpos($error, 'feedback') !== false) {
                $detail .= ' (Phản hồi)';
            }
            if (strpos($error, 'resignation') !== false) {
                $detail .= ' (Đơn xin nghỉ việc)';
            }
            // Thêm vị trí (chức vụ) và phòng ban nếu còn ràng buộc
            $position = $employee->position ? $employee->position->name : null;
            $department = $employee->department ? $employee->department->name : null;
            if ($position) {
                $detail .= ' | Vị trí: ' . $position;
            }
            if ($department) {
                $detail .= ' | Phòng ban: ' . $department;
            }
            if (!$detail) {
                $detail = ' (Có dữ liệu liên quan ở các bảng khác)';
            }
            return redirect()->route('admin.employees.index')
                ->with('error', $msg . $detail);
        }
    }
}