<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // 1. جلب الإشعارات الخاصة بالمستخدم
    public function myNotifications()
    {
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $notifications
        ]);
    }

    // 2. إرسال إشعار لمستخدم واحد (عن طريق الـ API)
    public function sendToUser(Request $request, NotificationService $service)
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'title'      => 'required|string',
            'message'    => 'required|string',
            'type'       => 'nullable|string',
            'extra_data' => 'nullable|array'
        ]);

        $service->sendToUser($data);

        return response()->json([
            'message' => 'Notification sent successfully',
            'data'    => $data
        ]);
    }

    // 3. إرسال إشعار عام لكل المستخدمين
    public function sendToAll(Request $request, NotificationService $service)
    {
        $data = $request->validate([
            'title'   => 'required|string',
            'message' => 'required|string',
            'type'    => 'nullable|string',
        ]);

        $users = User::all();

        foreach ($users as $user) {
            $service->sendToUser([
                'user_id' => $user->id,
                'title'   => $data['title'],
                'message' => $data['message'],
                'type'    => $data['type'] ?? 'general_announcement',
            ]);
        }

        return response()->json(['message' => 'Notification sent to all users']);
    }

    // 4. إشعار لفئة/دور معين (Role)
    public function sendToRole(Request $request, NotificationService $service)
    {
        $data = $request->validate([
            'title'   => 'required|string',
            'message' => 'required|string',
            'role'    => 'required|string',
            'type'    => 'nullable|string',
        ]);

        $users = User::where('role', $request->role)->get();

        foreach ($users as $user) {
            $service->sendToUser([
                'user_id' => $user->id,
                'title'   => $data['title'],
                'message' => $data['message'],
                'type'    => $data['type'] ?? 'role_announcement',
            ]);
        }

        return response()->json(['message' => "Notification sent to role: {$request->role}"]);
    }
}