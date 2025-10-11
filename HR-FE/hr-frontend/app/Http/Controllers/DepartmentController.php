<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentController extends Controller
{
    public function index()
    {
        $response = Http::get(config('services.backend_api.url') . '/api/departments');

        if ($response->successful()) {
            $data = $response->json();

            // Nếu API trả dữ liệu không có pagination, dùng toàn bộ mảng
            $departmentsArray = array_map(fn($item) => (object) $item, $data['data'] ?? $data);

            // Collection
            $collection = new Collection($departmentsArray);

            // Pagination nếu API trả theo chuẩn Laravel
            $departments = new LengthAwarePaginator(
                $collection,
                $data['total'] ?? count($collection),
                $data['per_page'] ?? count($collection),
                $data['current_page'] ?? 1,
                ['path' => url()->current()]
            );

        } else {
            $departments = new LengthAwarePaginator([], 0, 10, 1);
        }

        return view('departments.index', compact('departments'));
    }
}
