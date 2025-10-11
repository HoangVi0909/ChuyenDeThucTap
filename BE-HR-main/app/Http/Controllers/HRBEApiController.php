<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Salary;
use App\Models\WorkSchedule;
use App\Models\Feedback;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class HRBEApiController extends Controller
{
    // Admin Authentication API
    public function adminLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)
                     ->orWhere('email', $request->username)
                     ->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không đúng.'
            ], 401);
        }

        if ($admin->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản đã bị vô hiệu hóa.'
            ], 403);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'admin' => $admin,
            'token' => $token
        ]);
    }

    public function adminLogout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!'
        ]);
    }

    // Department API
    public function getDepartments()
    {
        $departments = Department::with('employees')->get();
        return response()->json($departments);
    }

    public function createDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $department = Department::create($request->all());
        return response()->json($department, 201);
    }

    public function updateDepartment(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $department->update($request->all());
        return response()->json($department);
    }

    public function deleteDepartment($id)
    {
        Department::destroy($id);
        return response()->json(['message' => 'Phòng ban đã được xóa thành công'], 200);
    }

    // Employee API
    public function getEmployees()
    {
        $employees = Employee::with(['department', 'position'])->get();
        return response()->json($employees);
    }

    public function createEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'username' => 'required|string|unique:employees',
            'password' => 'required|string|min:8',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'status' => 'required|in:active,inactive,terminated'
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        $employee = Employee::create($data);
        return response()->json($employee->load(['department', 'position']), 201);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'username' => 'required|string|unique:employees,username,' . $employee->id,
            'password' => 'nullable|string|min:8',
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
        return response()->json($employee->load(['department', 'position']));
    }

    public function deleteEmployee($id)
    {
        Employee::destroy($id);
        return response()->json(['message' => 'Nhân viên đã được xóa thành công'], 200);
    }

    // Position API
    public function getPositions()
    {
        $positions = Position::with('department')->get();
        return response()->json($positions);
    }

    public function createPosition(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive'
        ]);

        $position = Position::create($request->all());
        return response()->json($position->load('department'), 201);
    }

    public function updatePosition(Request $request, $id)
    {
        $position = Position::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,inactive'
        ]);

        $position->update($request->all());
        return response()->json($position->load('department'));
    }

    public function deletePosition($id)
    {
        Position::destroy($id);
        return response()->json(['message' => 'Chức vụ đã được xóa thành công'], 200);
    }

    // Dashboard Stats
    public function getDashboardStats()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'total_departments' => Department::count(),
            'total_positions' => Position::count(),
            'total_feedback' => Feedback::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'departments_with_employees' => Department::withCount('employees')->get(),
            'recent_employees' => Employee::latest()->take(5)->get()
        ];

        return response()->json($stats);
    }
}