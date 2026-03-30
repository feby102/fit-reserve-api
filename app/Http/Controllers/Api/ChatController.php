<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(){

    $conversations = Conversation::with(['messages' => function($q){
        $q->latest()->limit(1);
    }])->get();
    return response()->json($conversations);
    }

//send a message



public function sendMessage(Request $request)
{
    $validated = $request->validate([
        'conversation_id' => 'required|exists:conversations,id',
        'message'         => 'nullable|string',
        'file'            => 'nullable|file|mimes:jpg,jpeg,png,mp3,wav,ogg,m4a|max:10240',
        'type'            => 'required|in:text,image,audio',
        'receiver_id'     =>'required|exists:users,id'
    ]);

if (!$request->message && !$request->hasFile('file')) {
    return response()->json(['error' => 'Message or file required'], 422);
}
 

    $file_path = null;
    if ($request->hasFile('file')) {
        // تنظيم الفولدرات حسب النوع
        $folder = $request->type === 'image' ? 'chat/images' : 'chat/audio';
        $file_path = $request->file('file')->store($folder, 'public');
    }

    $message = Message::create([
        'conversation_id' => $validated['conversation_id'],
        'sender_id'       => auth()->id(),
        'message'         => $validated['message'],
        'file_path'       => $file_path,
        'type'            => $validated['type'],
        'receiver_id'     => $validated['receiver_id']
    ]);

    $message->load('sender');
$receiver = User::find($validated['receiver_id']);
    broadcast(new \App\Events\MessageSent($message,$receiver))->toOthers();

    return response()->json([
        'status' => 'success',
        'data'   => $message
    ]);
}




  // إغلاق المحادثة
    public function closeConversation($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->status = 'closed';
        $conversation->save();

        return response()->json(['message'=>'Conversation closed']);
    }


    //  ban
    public function blockUser($user_id)
    {
        $user = User::findOrFail($user_id);
        $user->is_active = false;
        $user->save();

        return response()->json(['message'=>'User blocked']);
    }

//show reported message

public function reports()
    {
$reports=Message::with(' reports','sender')->whereHas('reports')->get();
  return response()->json($reports);
    
    }

     public function flaggedMessages()
    {
        $messages = Message::with('sender','conversation')->where('is_flagged',true)->get();
        return response()->json($messages);
    }

}