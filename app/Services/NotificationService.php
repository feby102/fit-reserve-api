<?php 
namespace App\Services;

use App\Models\Notification;
use App\Models\User;
 use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationService{


public function sendToUser(array $data){

        Notification::create([
    'user_id' => $data['user_id'],
            'title' => $data['title'],
            'message' => $data['message'],
        ]);

        Firebase::database()
            ->getReference("notifications/{$data['userId']}")
            ->push([
                'title' => $data['title'],
                'message' => $data['message'],
                'image'=>$data['image']??null,
                'is_read' => false,
                'created_at' => now()->toDateTimeString(),
            ]);
    }

}




