<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // Gửi query params (status, search) tới API
        $response = Http::get(config('services.backend_api.url') . '/api/projects', $request->only(['status', 'search']));
        $projects = collect($response->json()['data'] ?? [])->map(fn($p) => (object) $p);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $employees = Http::get(config('services.backend_api.url') . '/api/employees')->json()['data'] ?? [];
        return view('projects.create', ['employees' => collect($employees)->map(fn($e) => (object) $e)]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
            'budget' => 'nullable|numeric',
            'employee_ids' => 'nullable|array',
        ]);

        $response = Http::post(config('services.backend_api.url') . '/api/projects', $validated);

        if ($response->successful()) {
            return redirect()->route('projects.index')->with('message', 'Thêm dự án thành công');
        }
        return back()->withInput()->with('error', 'Không thể thêm dự án');
    }

    public function edit($id)
    {
        $project = Http::get(config('services.backend_api.url') . "/api/projects/{$id}")->json()['data'] ?? null;
        $employees = Http::get(config('services.backend_api.url') . '/api/employees')->json()['data'] ?? [];

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Không tìm thấy dự án');
        }

        return view('projects.edit', [
            'project' => (object) $project,
            'employees' => collect($employees)->map(fn($e) => (object) $e)
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
            'budget' => 'nullable|numeric',
            'employee_ids' => 'nullable|array',
        ]);

        $response = Http::put(config('services.backend_api.url') . "/api/projects/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('projects.index')->with('message', 'Cập nhật dự án thành công');
        }
        return back()->withInput()->with('error', 'Không thể cập nhật dự án');
    }

    public function destroy($id)
    {
        $response = Http::delete(config('services.backend_api.url') . "/api/projects/{$id}");

        if ($response->successful()) {
            return redirect()->route('projects.index')->with('message', 'Xóa dự án thành công');
        }
        return redirect()->route('projects.index')->with('error', 'Không thể xóa dự án');
    }
}
