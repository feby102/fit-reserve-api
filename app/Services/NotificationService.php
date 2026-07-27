<?php 

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class NotificationService {

    public function sendToUser(array $data): bool
    {
        $user = User::with('deviceTokens')->find($data['user_id']);

        if (!$user) {
            return false;
        }

        // 1. تجهيز البيانات الإضافية
        $extraData = $data['extra_data'] ?? [];
        if (isset($data['status'])) {
            $extraData['status'] = $data['status'];
        }

        // 2. الحفظ في قاعدة بيانات MySQL
        Notification::create([
            'user_id' => $data['user_id'],
            'title'   => $data['title'],
            'message' => $data['message'],
            'type'    => $data['type'] ?? 'general',
            'data'    => $extraData,
        ]);

        // 3. إرسال Push Notification عبر FCM (لو فيه توكنات)
        $tokens = $user->deviceTokens()->pluck('token')->filter()->toArray();

        if (!empty($tokens)) {
            // تحويل كل القيم لـ Strings بأمان حتي لو فيه Nested Arrays
            $fcmData = array_merge([
                'type'    => (string) ($data['type'] ?? 'general'),
                'user_id' => (string) $user->id,
            ], $this->formatDataForFcm($extraData));

            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($data['title'], $data['message']))
                ->withData($fcmData);

            try {
                Firebase::messaging()->sendMulticast($message, $tokens);
            } catch (\Exception $e) {
                Log::error("FCM Send Error for user {$user->id}: " . $e->getMessage());
            }
        }

        // 4. التخزين في Firebase Realtime Database
        try {
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
        } catch (\Exception $e) {
            Log::error("Firebase Realtime DB Error for user {$user->id}: " . $e->getMessage());
        }

        return true;
    }

    /**
     * دالة مساعدة لتحويل أي Array لـ Flat Strings متوافقة مع FCM
     */
    private function formatDataForFcm(array $data): array
    {
        $formatted = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $formatted[$key] = json_encode($value); // تحويل الـ Array لـ JSON string
            } else {
                $formatted[$key] = (string) $value;
            }
        }
        return $formatted;
    }
}