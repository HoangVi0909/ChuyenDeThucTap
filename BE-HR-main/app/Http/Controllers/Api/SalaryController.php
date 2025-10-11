<?php

namespace App\Http\Controllers\Api;

use App\Models\Salary;
use App\Models\Employee;
use App\Http\Requests\SalaryRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Lấy danh sách tất cả lương
     */
    public function index()
    {
        try {
            $salaries = Salary::with('employee:id,name,employee_code')
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
            return response()->json($salaries);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không thể tải danh sách lương',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin một bản ghi lương
     */
    public function show($id)
    {
        try {
            $salary = Salary::with('employee:id,name,employee_code')->findOrFail($id);
            return response()->json($salary);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Không tìm thấy bản ghi lương',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Tạo mới bản ghi lương
     */
    public function store(SalaryRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Chuyển đổi month từ Y-m thành Y-m-01 để lưu database
            $data['month'] = $data['month'] . '-01';
            
            $salary = Salary::create($data);
            $salary->load('employee:id,name,employee_code');
            
            return response()->json([
                'success' => true,
                'message' => 'Tạo lương thành công',
                'data' => $salary
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo lương: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật bản ghi lương
     */
    public function update(SalaryRequest $request, $id)
    {
        try {
            $salary = Salary::findOrFail($id);
            $data = $request->validated();
            
            // Chuyển đổi month từ Y-m thành Y-m-01 để lưu database
            $data['month'] = $data['month'] . '-01';
            
            $salary->update($data);
            $salary->load('employee:id,name,employee_code');
            
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lương thành công',
                'data' => $salary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa bản ghi lương
     */
    public function destroy($id)
    {
        try {
            $salary = Salary::findOrFail($id);
            $salary->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Xóa lương thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa: ' . $e->getMessage()
            ], 500);
        }
    }
}