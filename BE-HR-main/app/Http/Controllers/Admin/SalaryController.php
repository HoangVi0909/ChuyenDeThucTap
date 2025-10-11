<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\Employee;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::with('employee')->get();
        return view('admin.salaries.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.salaries.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'effective_date' => 'required|date',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();
        $data['total_salary'] = $data['basic_salary'] + 
                               ($data['allowances'] ?? 0) + 
                               ($data['bonuses'] ?? 0) - 
                               ($data['deductions'] ?? 0);

        Salary::create($data);

        return redirect()->route('admin.salaries.index')
                        ->with('success', 'Lương đã được tạo thành công!');
    }

    public function show(Salary $salary)
    {
        $salary->load('employee');
        return view('admin.salaries.show', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.salaries.edit', compact('salary', 'employees'));
    }

    public function update(Request $request, Salary $salary)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'effective_date' => 'required|date',
            'status' => 'required|in:active,inactive'
        ]);

        $data = $request->all();
        $data['total_salary'] = $data['basic_salary'] + 
                               ($data['allowances'] ?? 0) + 
                               ($data['bonuses'] ?? 0) - 
                               ($data['deductions'] ?? 0);

        $salary->update($data);

        return redirect()->route('admin.salaries.index')
                        ->with('success', 'Lương đã được cập nhật thành công!');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();

        return redirect()->route('admin.salaries.index')
                        ->with('success', 'Lương đã được xóa thành công!');
    }
}