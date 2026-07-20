<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;


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
    ];  
    
    
 
protected $appends = ['file_url'];

public function getFileUrlAttribute()
{
    return $this->file_path
        ? Storage::url($this->file_path)
        : null;
}

protected function image(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value ? url(Storage::url($value)) : null,
    );
}

    
    
    
    
    public function sender()
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
