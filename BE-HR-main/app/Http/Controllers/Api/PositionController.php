<?php

namespace App\Http\Controllers\Api;

use App\Models\Position;
use App\Http\Requests\PositionRequest;
use App\Http\Controllers\Controller;

class PositionController extends Controller
{
    public function index()
    {
        return Position::all();
    }
    public function show($id)
    {
        return Position::findOrFail($id);
    }
    public function store(PositionRequest $request)
    {
        $position = Position::create($request->validated());
        return response()->json($position, 201);
    }
    public function update(PositionRequest $request, $id)
    {
        $position = Position::findOrFail($id);
        $position->update($request->validated());
        return response()->json($position);
    }
    public function destroy($id)
    {
        Position::destroy($id);
        return response()->json(null, 204);
    }
}
