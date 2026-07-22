<?php 
namespace App\Services;

use App\Models\Notification;
use App\Models\User;
 use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
class NotificationService{


public function sendToUser(array $data){

$user=User::with('deviceTokens')->find($data['user_id']);


if(!$user){return false;}



        Notification::create([
    'user_id' => $data['user_id'],
            'title' => $data['title'],
            'message' => $data['message'],
        ]);


        $tokens=$user->deviceTokens()->pluck('token')->toArray();

      if (!empty($tokens)) {
            $messaging = Firebase::messaging();

            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($data['title'], $data['message']))
                ->withData([
                    'type' => 'general_notification',
                    'user_id' => (string) $user->id,
                ]);

            try {
                // sendMulticast بتبعث لمجموعة توكنات في ريكويست واحد لفايربيز
                $messaging->sendMulticast($message, $tokens);
            } catch (\Exception $e) {
                \Log::error("FCM Bulk Error: " . $e->getMessage());
            }
        }

        // 4. التخزين في Realtime Database
        Firebase::database()
            ->getReference("notifications/{$user->id}")
            ->push([
                'title'      => $data['title'],
                'message'    => $data['message'],
                'is_read'    => false,
                'created_at' => now()->toDateTimeString()
            ]);

        return true;
    }
}




