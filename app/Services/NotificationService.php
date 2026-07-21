<?php 
namespace App\Services;

use App\Models\Notification;
use App\Models\User;
 use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationService{


public function sendToUser($userId,$title,$message){

        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
        ]);

        Firebase::database()
            ->getReference("notifications/$userId")
            ->push([
                'title' => $title,
                'message' => $message,
                'is_read' => false,
                'created_at' => now()->toDateTimeString(),
            ]);
    }

}




