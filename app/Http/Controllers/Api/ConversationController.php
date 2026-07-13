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
        return \response()->json(Conversation::with('participants')->get());    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $data = $request->validate([
        'title' => 'nullable|string',
        'user_two_id' => 'required|exists:users,id'   
    ]);

    $conversation = Conversation::create([
        'title' => $data['title'] ?? null,
        'user_one_id' => $request->user()->id, 
           'user_two_id' => $data['user_two_id'], 
               ]);

    return response()->json($conversation, 201);
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


    return  \response()->json([ 'message'=>'user assgined in conversation successfull',200]);
    
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
