<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PositionController extends Controller
{
    public function index()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        $positions = [];
        try {
            $response = Http::withToken($token)->get($apiUrl . '/api/admin/positions');
            if ($response->successful()) {
                $positions = $response->json();
                if (isset($positions['data'])) {
                    $positions = $positions['data'];
                }
            }
        } catch (\Exception $e) {
            $positions = [];
        }
        return view('admin.positions.index', compact('positions'));
    }
    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $position = [];
        try {
            $response = Http::withToken($token)->get("{$apiUrl}/api/admin/positions/{$id}");
            if ($response->successful()) {
                $position = $response->json();
                if (isset($position['data'])) {
                    $position = $position['data'];
                }
            }
        } catch (\Exception $e) {
            $position = [];
        }
        return view('admin.positions.show', compact('position'));
    }
    public function create()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        return view('admin.positions.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->except(['_token']);
            $response = Http::withToken($token)->post("{$apiUrl}/api/admin/positions", $data);
            if ($response->successful()) {
                return redirect()->route('admin.positions.index')->with('success', 'Thêm chức vụ thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi thêm chức vụ';
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $position = [];
        try {
            $response = Http::withToken($token)->get("{$apiUrl}/api/admin/positions/{$id}");
            if ($response->successful()) {
                $position = $response->json();
                if (isset($position['data'])) {
                    $position = $position['data'];
                }
                if (!isset($position['id'])) {
                    return redirect()->route('admin.positions.index')->withErrors('Không tìm thấy chức vụ!');
                }
            } else {
                return redirect()->route('admin.positions.index')->withErrors('Không tìm thấy chức vụ!');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.positions.index')->withErrors('Lỗi kết nối API');
        }
        return view('admin.positions.edit', compact('position'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $data = $request->except(['_token', '_method']);
            $response = Http::withToken($token)->put("{$apiUrl}/api/admin/positions/{$id}", $data);
            if ($response->successful()) {
                return redirect()->route('admin.positions.index')->with('success', 'Cập nhật chức vụ thành công!');
            } else {
                $error = $response->json();
                $errorMessage = 'Lỗi khi cập nhật chức vụ';
                if (isset($error['message'])) {
                    $errorMessage = $error['message'];
                } elseif (isset($error['errors'])) {
                    $errorMessage = 'Validation errors: ' . json_encode($error['errors']);
                }
                return back()->withErrors(['error' => $errorMessage])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi kết nối API: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $token = session('admin_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        try {
            $response = Http::withToken($token)->delete("{$apiUrl}/api/admin/positions/{$id}");
            if ($response->successful()) {
                return redirect()->route('admin.positions.index')->with('success', 'Xóa chức vụ thành công!');
            } else {
                return redirect()->route('admin.positions.index')->with('error', 'Xóa chức vụ thất bại!');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.positions.index')->with('error', 'Lỗi kết nối API');
        }
    }
}
