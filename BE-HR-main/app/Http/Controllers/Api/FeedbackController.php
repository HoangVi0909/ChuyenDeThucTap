<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use App\Http\Requests\FeedbackRequest;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    public function index()
    {
        return Feedback::all();
    }
    public function show($id)
    {
        return Feedback::findOrFail($id);
    }
    public function store(FeedbackRequest $request)
    {
        $feedback = Feedback::create($request->validated());
        return response()->json($feedback, 201);
    }
    public function update(FeedbackRequest $request, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update($request->validated());
        return response()->json($feedback);
    }
    public function destroy($id)
    {
        Feedback::destroy($id);
        return response()->json(null, 204);
    }
}
