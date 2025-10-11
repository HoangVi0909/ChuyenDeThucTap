<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use App\Http\Requests\NotificationRequest;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        return Notification::orderByDesc('created_at')->paginate(request('per_page', 10));
    }
    public function show($id)
    {
        return Notification::findOrFail($id);
    }
    public function store(NotificationRequest $request)
    {
        $notification = Notification::create($request->validated());
        return response()->json($notification, 201);
    }
    public function update(NotificationRequest $request, $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update($request->validated());
        return response()->json($notification);
    }
    public function destroy($id)
    {
        Notification::destroy($id);
        return response()->json(null, 204);
    }
}
