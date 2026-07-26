<?php 
namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class NotificationService {

    public function sendToUser(array $data)
    {
        $user = User::with('deviceTokens')->find($data['user_id']);

        if (!$user) {
            return false;
        }
         $extraData = $data['extra_data'] ?? [];
        if (isset($data['status'])) {
            $extraData['status'] = $data['status'];
        }

         Notification::create([
            'user_id' => $data['user_id'],
            'title'   => $data['title'],
            'message' => $data['message'],
            'type'    => $data['type'] ?? 'general',
            'data'    => $extraData, // تخزين مصفوفة الداتا والـ status كـ JSON
        ]);

        // 3. إرسال Push Notification عبر FCM
        $tokens = $user->deviceTokens()->pluck('token')->toArray();

        if (!empty($tokens)) {
            $messaging = Firebase::messaging();

            // تحويل كل قيم الداتا لـ string لضمان قبول FCM لها
            $fcmPayload = array_map('strval', array_merge([
                'type'    => $data['type'] ?? 'general',
                'user_id' => (string) $user->id,
            ], $extraData));

            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($data['title'], $data['message']))
                ->withData($fcmPayload);

            try {
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
                'type'       => $data['type'] ?? 'general',
                'data'       => $extraData,
                'is_read'    => false,
                'created_at' => now()->toDateTimeString()
            ]);

        return true;
    }
}