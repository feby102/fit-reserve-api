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
    
    public function participants()
    {
        return $this->belongsToMany(user::class,'conversation_user');
    }
}
