<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Http\Requests\AdminRequest;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function index()
    {
        return Admin::all();
    }
    public function show($id)
    {
        return Admin::findOrFail($id);
    }
    public function store(AdminRequest $request)
    {
        $admin = Admin::create($request->validated());
        return response()->json($admin, 201);
    }
    public function update(AdminRequest $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $admin->update($request->validated());
        return response()->json($admin);
    }
    public function destroy($id)
    {
        Admin::destroy($id);
        return response()->json(null, 204);
    }
}
