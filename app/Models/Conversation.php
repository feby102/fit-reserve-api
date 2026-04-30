<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
     protected $fillable = ['title','status'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    
   public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

   

    public function participants()
    {
        return $this->belongsToMany(user::class,'conversation_user');
    }
}
