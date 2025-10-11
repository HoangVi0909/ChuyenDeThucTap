<?php

namespace App\Http\Controllers\Api;

use App\Models\Department;
use App\Http\Requests\DepartmentRequest;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function index()
    {
        return Department::all();
    }
    public function show($id)
    {
        return Department::findOrFail($id);
    }
    public function store(DepartmentRequest $request)
    {
        $department = Department::create($request->validated());
        return response()->json($department, 201);
    }
    public function update(DepartmentRequest $request, $id)
    {
        $department = Department::findOrFail($id);
        $department->update($request->validated());
        return response()->json($department);
    }
    public function destroy($id)
    {
        Department::destroy($id);
        return response()->json(null, 204);
    }
}
