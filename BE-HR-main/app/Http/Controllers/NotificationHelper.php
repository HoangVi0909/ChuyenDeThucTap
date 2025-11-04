<?php
namespace App\Http\Controllers;

use App\Models\Notification;

class NotificationHelper
{
    public static function send($userId, $userType, $title, $message, $type = null)
    {
        Notification::create([
            'user_id' => $userId,
            'user_type' => $userType,
            'title' => $title,
            'message' => $message,
            'type' => $type,
        ]);
    }
}
