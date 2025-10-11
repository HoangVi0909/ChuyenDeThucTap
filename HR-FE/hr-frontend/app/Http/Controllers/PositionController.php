<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    // Danh sách vị trí
    public function index()
    {
        $response = Http::get(config('services.backend_api.url') . '/api/positions');

        $positions = collect($response->json()['data'] ?? [])->map(fn($item) => (object) $item);

        return view('positions.index', compact('positions'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $response = Http::post(config('services.backend_api.url') . '/api/positions', $validated);

        if ($response->successful()) {
            return redirect()->route('positions.index')->with('message', 'Thêm vị trí thành công!');
        }

        return back()->withInput()->with('error', 'Không thể thêm vị trí');
    }

    public function edit($id)
    {
        $response = Http::get(config('services.backend_api.url') . "/api/positions/{$id}");

        if (!$response->successful()) {
            return redirect()->route('positions.index')->with('error', 'Không tìm thấy vị trí');
        }

        $position = (object) $response->json()['data'];
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $response = Http::put(config('services.backend_api.url') . "/api/positions/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('positions.index')->with('message', 'Cập nhật vị trí thành công!');
        }

        return back()->withInput()->with('error', 'Không thể cập nhật vị trí');
    }

    public function destroy($id)
    {
        $response = Http::delete(config('services.backend_api.url') . "/api/positions/{$id}");

        if ($response->successful()) {
            return redirect()->route('positions.index')->with('message', 'Xóa vị trí thành công');
        }

        return redirect()->route('positions.index')->with('error', 'Không thể xóa vị trí');
    }
}
