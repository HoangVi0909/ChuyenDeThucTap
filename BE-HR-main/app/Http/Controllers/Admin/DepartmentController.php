<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive'
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Phòng ban đã được tạo thành công!');
    }

    public function show(Department $department)
    {
        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive'
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Phòng ban đã được cập nhật thành công!');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')
                        ->with('success', 'Phòng ban đã được xóa thành công!');
    }
}