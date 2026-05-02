<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

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

    // إرسال رسالة
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message'         => 'nullable|string',
            'file'            => 'nullable|file|max:10240',
            'type'            => 'required|in:text,image,audio',
            'receiver_id'     => 'required|exists:users,id'
        ]);

        $userId = auth()->id();

        //   تأمين: التأكد إن المستخدم جزء من المحادثة
        $conversation = Conversation::where('id', $validated['conversation_id'])
            ->where(function ($q) use ($userId) {
                $q->where('user_one_id', $userId)
                  ->orWhere('user_two_id', $userId);
            })->first();

        if (!$conversation) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        //   تحقق منطقي حسب النوع
        if ($validated['type'] === 'text' && !$validated['message']) {
            return response()->json(['error' => 'Text message required'], 422);
        }

        if (in_array($validated['type'], ['image', 'audio']) && !$request->hasFile('file')) {
            return response()->json(['error' => 'File required'], 422);
        }

        $file_path = null;

        //   رفع الملف حسب النوع
        if ($request->hasFile('file')) {

            if ($validated['type'] === 'image') {
                $request->validate([
                    'file' => 'image|mimes:jpg,jpeg,png|max:10240'
                ]);
                $folder = 'chat/images';
            } else {
                $request->validate([
                    'file' => 'mimes:mp3,wav,ogg,m4a|max:10240'
                ]);
                $folder = 'chat/audio';
            }

            $file_path = $request->file('file')->store($folder, 'public');
        }

        //   إنشاء الرسالة
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $userId,
            'receiver_id'     => $validated['receiver_id'],
            'message'         => $validated['message'],
            'file_path'       => $file_path,
            'type'            => $validated['type'],
        ]);

        $message->load('sender');

        $receiver = User::find($validated['receiver_id']);

        //   real-time broadcasting
        broadcast(new \App\Events\MessageSent($message, $receiver))->toOthers();

        return response()->json([
            'status' => 'success',
            'data'   => $message
        ]);
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
}