<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AttendanceController extends Controller
{
    public function index()
    {
        $response = Http::get(config('services.backend_api.url') . '/api/attendances');

        $attendances = collect($response->json()['data'] ?? [])->map(function ($item) {
            $item = (object) $item; // ép attendance thành object

            // ép employee nếu tồn tại
            if (isset($item->employee)) {
                $item->employee = (object) $item->employee;
            }

            return $item;
        });

        return view('attendances.index', compact('attendances'));
    }

    public function create()
    {
        $employees = Http::get(config('services.backend_api.url') . '/api/employees')->json()['data'] ?? [];
        return view('attendances.create', ['employees' => collect($employees)->map(fn($e) => (object) $e)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'status' => 'required',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'note' => 'nullable',
        ]);

        $response = Http::post(config('services.backend_api.url') . '/api/attendances', $validated);
        if ($response->successful()) {
            return redirect()->route('attendances.index')->with('message', 'Thêm chấm công thành công');
        }
        return back()->withInput()->with('error', 'Thêm thất bại');
    }

    public function edit($id)
    {
        $attendance = (object) Http::get(config('services.backend_api.url') . "/api/attendances/{$id}")->json()['data'];
        $employees = collect(Http::get(config('services.backend_api.url') . '/api/employees')->json()['data'] ?? [])->map(fn($e) => (object) $e);
        return view('attendances.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'status' => 'required',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'note' => 'nullable',
        ]);

        $response = Http::put(config('services.backend_api.url') . "/api/attendances/{$id}", $validated);
        if ($response->successful()) {
            return redirect()->route('attendances.index')->with('message', 'Cập nhật thành công');
        }
        return back()->withInput()->with('error', 'Cập nhật thất bại');
    }

    public function destroy($id)
    {
        $response = Http::delete(config('services.backend_api.url') . "/api/attendances/{$id}");
        if ($response->successful()) {
            return redirect()->route('attendances.index')->with('message', 'Xóa thành công');
        }
        return redirect()->route('attendances.index')->with('error', 'Xóa thất bại');
    }
}
