<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Events\MessageRead;

class ChatController extends Controller
{
    // عرض اليوزرز + المحادثات
    public function index()
    {
        $userId = auth()->id();

        $users = User::where('id', '!=', $userId)->get();

        $myConversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo']) 
            ->get();

        return response()->json([
            'conversations' => $myConversations,
            'users' => $users
        ]);
    }


public function getMessages(Request $request, $conversationId)
{
     $userId = auth()->id(); 

     $conversation = Conversation::where('id', $conversationId)
        ->where(function ($query) use ($userId) {
            $query->where('user_one_id', $userId)
                  ->orWhere('user_two_id', $userId);
        })
        ->first();  
     if (!$conversation) {
        return response()->json([
            'message' => 'Unauthorized or Conversation not found.'
        ], 403); }

     $messages = Message::where('conversation_id', $conversation->id)
        ->with('sender')
        ->latest()
        ->paginate(20);

    return response()->json($messages);
}


    // إرسال رسالة
public function sendMessage(Request $request, NotificationService $notificationService)
{
    $validated = $request->validate([
        'conversation_id' => 'required|exists:conversations,id',
        'message'         => 'nullable|string',
        'file'            => 'nullable|file|max:10240',
        'type'            => 'required|in:text,image,audio',
        'receiver_id'     => 'required|exists:users,id'
    ]);

    $userId = auth()->id();

    // 1. تأمين: التأكد إن المستخدم جزء من المحادثة
    $conversation = Conversation::where('id', $validated['conversation_id'])
        ->where(function ($q) use ($userId) {
            $q->where('user_one_id', $userId)
              ->orWhere('user_two_id', $userId);
        })->first();

    if (!$conversation) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // 2. تحقق منطقي حسب النوع
    if ($validated['type'] === 'text' && empty($validated['message'])) {
        return response()->json(['error' => 'Text message required'], 422);
    }

    if (in_array($validated['type'], ['image', 'audio']) && !$request->hasFile('file')) {
        return response()->json(['error' => 'File required'], 422);
    }

    $file_path = null;

    // 3. رفع الملف حسب النوع
    if ($request->hasFile('file')) {
        if ($validated['type'] === 'image') {
            $request->validate([
                'file' => 'image|mimes:jpg,jpeg,png|max:10240'
            ]);
            $folder = 'chat/images';
        } else {
            $request->validate([
                'file' => 'mimes:mp3,wav,ogg,m4a,mp4,x-m4a|max:10240'
            ]);
            $folder = 'chat/audio';
        }

        $file_path = $request->file('file')->store($folder, 'public');
    }

    // 4. إنشاء الرسالة
    $message = Message::create([
        'conversation_id' => $conversation->id,
        'sender_id'       => $userId,
        'receiver_id'     => $validated['receiver_id'],
        'message'         => $validated['message'],
        'file_path'       => $file_path,
        'type'            => $validated['type'],
    ]);

    // 5. تجهيز بيانات الإشعار حسب نوع الرسالة
    $notificationText = match ($message->type) {
        'text'  => $message->message,
        'image' => '📷 أرسل لك صورة',
        'audio' => '🎤 أرسل لك تسجيلاً صوتياً',
        default => 'أرسل لك رسالة جديدة',
    };

    $notificationData = [
        'user_id'         => $message->receiver_id,
        'title'           => auth()->user()->name,
        'message'         => $notificationText,
        'conversation_id' => (string) $message->conversation_id,
        'type'            => 'chat_message',
    ];

    // 6. إرسال Push Notification عبر الفايربيز
    try {
        $notificationService->sendToUser($notificationData);
    } catch (\Exception $e) {
        \Log::error("Failed to send chat push notification: " . $e->getMessage());
    }

    // 7. إرسال الـ Real-time Event عبر WebSockets (Pusher/Reverb)
    $message->load('sender');

    try {
        event(new \App\Events\MessageSent($message, $validated['receiver_id']));
    } catch (\Exception $e) {
        \Log::error("WebSocket Event Error: " . $e->getMessage());
    }

    // 8. إرجاع الـ Response
    return response()->json([
        'success' => true,
        'message' => $message
    ], 201);
}

     public function closeConversation($id)
    {
        $conversation = Conversation::findOrFail($id);

         if (!in_array(auth()->id(), [$conversation->user_one_id, $conversation->user_two_id])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->status = 'closed';
        $conversation->save();

        return response()->json(['message' => 'Conversation closed']);
    }

     public function blockUser($user_id)
    {
        $user = User::findOrFail($user_id);

        $user->is_active = false;
        $user->save();

        return response()->json(['message' => 'User blocked']);
    }

     public function reports()
    {
        $reports = Message::with(['reports', 'sender'])
            ->whereHas('reports')
            ->get();

        return response()->json($reports);
    }

     public function flaggedMessages()
    {
        $messages = Message::with(['sender', 'conversation'])
            ->where('is_flagged', true)
            ->get();

        return response()->json($messages);
    }


 public function markAsRead($conversation_id)
{
    $userId = auth()->id();

     Message::where('conversation_id', $conversation_id)
        ->where('receiver_id', $userId)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

broadcast(new MessageRead($conversation_id, $userId))->toOthers();
    return response()->json(['status' => 'Messages marked as read']);
}

    }