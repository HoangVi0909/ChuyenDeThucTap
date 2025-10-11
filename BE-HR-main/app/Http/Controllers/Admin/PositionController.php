<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('department')->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        $departments = Department::where('status', 'active')->get();
        return view('admin.positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'status' => 'required|in:active,inactive'
        ]);

        Position::create($request->all());

        return redirect()->route('admin.positions.index')
                        ->with('success', 'Chức vụ đã được tạo thành công!');
    }

    public function show(Position $position)
    {
        $position->load('department');
        return view('admin.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $departments = Department::where('status', 'active')->get();
        return view('admin.positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'status' => 'required|in:active,inactive'
        ]);

        $position->update($request->all());

        return redirect()->route('admin.positions.index')
                        ->with('success', 'Chức vụ đã được cập nhật thành công!');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('admin.positions.index')
                        ->with('success', 'Chức vụ đã được xóa thành công!');
    }
}