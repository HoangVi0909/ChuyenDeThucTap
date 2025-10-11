<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $response = Http::get(config('services.backend_api.url') . '/api/employees');

        if ($response->successful()) {
            $data = $response->json();

            $employeesArray = array_map(function ($item) {
                $item = (object) $item;
                $item->department = $item->department ? (object) $item->department : null;
                $item->position = $item->position ? (object) $item->position : null;
                return $item;
            }, $data['data']);

            $collection = new Collection($employeesArray);

            $employees = new LengthAwarePaginator(
                $collection,
                $data['total'],
                $data['per_page'],
                $data['current_page'],
                ['path' => url()->current()]
            );
        } else {
            $employees = new LengthAwarePaginator([], 0, 10, 1);
        }

        return view('employees.index', compact('employees'));
    }

    public function show($id)
    {
        $response = Http::get(config('services.backend_api.url') . "/api/employees/{$id}");

        if ($response->successful()) {
            $employee = (object) $response->json();
            $employee->department = $employee->department ? (object) $employee->department : null;
            $employee->position = $employee->position ? (object) $employee->position : null;

            return view('employees.show', compact('employee'));
        }

        return redirect()->route('employees.index')->with('error', 'Không tìm thấy nhân viên');
    }

    /** 🟢 Hiển thị form tạo nhân viên */
    public function create()
    {
        // Gọi API lấy phòng ban
        $depResponse = Http::get(config('services.backend_api.url') . '/api/departments');
        $posResponse = Http::get(config('services.backend_api.url') . '/api/positions');

        $departments = collect($depResponse->json()['data'] ?? [])->map(function ($item) {
            return (object) $item;
        });

        $positions = collect($posResponse->json()['data'] ?? [])->map(function ($item) {
            return (object) $item;
        });

        return view('employees.create', [
            'departments' => $departments,
            'positions' => $positions,
            'employee' => null, // để _form không báo lỗi khi create
        ]);
    }
    /** 🟢 Gửi dữ liệu tạo nhân viên về API */
    public function store(Request $request)
    {
        $response = Http::post(config('services.backend_api.url') . '/api/employees', $request->all());

        if ($response->successful()) {
            return redirect()->route('employees.index')->with('message', 'Tạo nhân viên thành công');
        }

        return back()->withInput()->with('error', 'Tạo nhân viên thất bại');
    }
    public function edit($id)
    {
        // Gọi API lấy thông tin nhân viên
        $empResponse = Http::get(config('services.backend_api.url') . "/api/employees/{$id}");
        $depResponse = Http::get(config('services.backend_api.url') . '/api/departments');
        $posResponse = Http::get(config('services.backend_api.url') . '/api/positions');

        if (!$empResponse->successful()) {
            return redirect()->route('employees.index')->with('error', 'Không tìm thấy nhân viên');
        }

        // Ép dữ liệu về object để form xử lý
        $employee = (object) $empResponse->json();
        $departments = collect($depResponse->json()['data'] ?? [])->map(fn($item) => (object) $item);
        $positions = collect($posResponse->json()['data'] ?? [])->map(fn($item) => (object) $item);

        return view('employees.edit', compact('employee', 'departments', 'positions'));
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_code' => 'required|max:20',
            'fullname' => 'required|max:100',
            'cccd' => 'required|max:20',
            'dob' => 'nullable|date',
            'gender' => 'required|in:Nam,Nữ,Khác',
            'education_level' => 'nullable|max:50',
            'email' => 'required|email',
            'phone' => 'nullable|max:20',
            'address' => 'nullable|max:255',
            'department_id' => 'required|numeric',
            'position_id' => 'required|numeric',
        ]);

        $response = Http::put(config('services.backend_api.url') . "/api/employees/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('employees.index')->with('message', 'Cập nhật nhân viên thành công!');
        }

        return back()->withErrors(['message' => 'Lỗi khi cập nhật nhân viên']);
    }


    /** 🔴 Xóa nhân viên */
    public function destroy($id)
    {
        $response = Http::delete(config('services.backend_api.url') . "/api/employees/{$id}");

        if ($response->successful()) {
            return redirect()->route('employees.index')->with('message', 'Xóa nhân viên thành công');
        }

        return redirect()->route('employees.index')->with('error', 'Không thể xóa nhân viên');
    }
}
