<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
 protected $fillable = [
        'conversation_id',
        'sender_id',
        'receiver_id',
        'message',
        'file_path',
        'type',
        'is_flagged'
    ];   public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
