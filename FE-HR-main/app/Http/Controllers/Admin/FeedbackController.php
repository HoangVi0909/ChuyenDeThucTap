<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    public function index()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        $feedbacks = [];
        try {
            $response = Http::withToken($token)->get($apiUrl . '/api/admin/feedback');
            if ($response->successful()) {
                $feedbacks = $response->json();
                if (isset($feedbacks['data'])) {
                    $feedbacks = $feedbacks['data'];
                }
            }
        } catch (\Exception $e) {
            $feedbacks = [];
        }
        return view('admin.feedback.index', compact('feedbacks'));
    }
    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        $feedback = [];
        try {
            $response = Http::withToken($token)->get($apiUrl . "/api/admin/feedback/{$id}");
            if ($response->successful()) {
                $feedback = $response->json();
                if (isset($feedback['data'])) {
                    $feedback = $feedback['data'];
                }
            }
        } catch (\Exception $e) {
            $feedback = [];
        }
        return view('admin.feedback.show', compact('feedback'));
    }
}
