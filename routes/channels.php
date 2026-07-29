<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


// Broadcast::channel('chat.{conversation_id}',function($user,$conversation_id){
// return $user->conversations()->where('id',$conversation_id)->exists();
// });


// Broadcast::channel('chat.{conversation_id}', function ($user, $conversation_id) {
//     \Log::info("Channel Auth", [
//         'user_id' => $user->id,
//         'conversation_id' => $conversation_id,
//     ]);

//     return true;
// });


Broadcast::channel('chat.{conversation_id}', function ($user, $conversation_id) {

    Log::info('Broadcast Auth',[
        'user'=>$user->id,
        'conversation'=>$conversation_id
    ]);

    return $user->conversations()
        ->where('id',$conversation_id)
        ->exists();
});
