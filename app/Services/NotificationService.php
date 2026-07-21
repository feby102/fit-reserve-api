<?php 
namespace App\Services;

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




}