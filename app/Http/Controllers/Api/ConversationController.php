<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index()
    {
        // جلب المحادثات الخاصة بالمستخدم الحالي فقط   
        $userId = auth()->id();
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo'])
            ->get();

        return response()->json($conversations);
    }
    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'nullable|string',
        'user_two_id' => 'required|exists:users,id'   
    ]);


    $user_one_id=$request->user()->id;
    $user_two_id=$data['user_two_id'];

    if ($user_one_id == $user_two_id) {
        return response()->json(['message' => 'You cannot start a conversation with yourself.'], 400);
    }

$conversation=Conversation::where(function($query) use($user_one_id,$user_two_id){

$query->where('user_one_id',$user_one_id)->where('user_two_id',$user_two_id);


})
    ->orWhere(function($query) use($user_one_id,$user_two_id){
        
    $query->where('user_one_id',$user_two_id)->where('user_two_id',$user_one_id);
    
    
    
    })->first();


if(!$conversation){

    $conversation = Conversation::create([
        'title' => $data['title'] ?? null,
        'user_one_id' => $request->user()->id, 
           'user_two_id' => $data['user_two_id'], 
               ]);
}
    return response()->json($conversation, 200);
}

     
        public function assignuser(Request $request,Conversation $conversation)
    { 
        $user=$request->user();
     if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
    $conversation->participants()->attach($user->id);
}
else{
    return  \response()->json([ 'message'=>'user already assgined in conversation']);
}


    return  \response()->json([ 'message'=>'user assgined in conversation successfull'],200);
    
        }


 
 
 
        /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
