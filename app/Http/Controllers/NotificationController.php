<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\CustomNotificationByEmail;

class NotificationController extends Controller
{
    public static function sendTestEmailNotification($user)
    {
        $msg = 'Welcome to '.env('APP_NAME').'.<br>
                This is a test mail.<br>
                You can ignore for now, as we are still on development stage.';
        try {
            $user->notify(new CustomNotificationByEmail('Welcome to '.env('APP_NAME'), $msg));
        } catch (\Exception $e) {  // Capture the exception in $e
            Log::error('Email sending failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
}
