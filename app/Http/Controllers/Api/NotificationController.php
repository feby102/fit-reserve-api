<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class NotificationController extends Controller
{
    // جلب الإشعارات الخاصة بالمستخدم
    public function myNotifications()
    {
        $user = auth()->user();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

     public function sendToUser(Request $request)
    {
        $data = $request->validate([
            'user_id'=>'required',
            'title'=>'required',
            'message'=>'required'
        ]);

         $notification = Notification::create($data);

        // إرسال للفايزبيز
        Firebase::database()
            ->getReference("notifications/{$data['user_id']}")
            ->push([
                'title' => $data['title'],
                'message' => $data['message'],
                'is_read' => false,
                'created_at' => now()->toDateTimeString()
            ]);

        return response()->json(['message'=>'Notification sent']);
    }

    // إرسال إشعار عام لكل المستخدمين
    public function sendToAll(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'message' => 'required'
        ]);

        $users = User::all();

        foreach ($users as $user) {
            // حفظ في DB
            Notification::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'message' => $data['message']
            ]);

            // إرسال للفايزبيز
            Firebase::database()
                ->getReference("notifications/{$user->id}")
                ->push([
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'is_read' => false,
                    'created_at' => now()->toDateTimeString()
                ]);
        }

        return response()->json(['message' => 'Notification sent to all']);
    }

    // إشعار لفئة معينة
    public function sendToRole(Request $request)
    {
        $data = $request->validate([
            'title'=>'required',
            'message'=>'required',
            'role'=>'required',
        ]);

        $users = User::where('role',$request->role)->get();

        foreach($users as $user){
            // حفظ في DB
            Notification::create([
                'user_id'=>$user->id,
                'title'=>$data['title'],
                'message'=>$data['message']
            ]);

            // إرسال للفايزبيز
            Firebase::database()
                ->getReference("notifications/{$user->id}")
                ->push([
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'is_read' => false,
                    'created_at' => now()->toDateTimeString()
                ]);
        }

        return response()->json(['message'=>'Notification sent']);
    }
}