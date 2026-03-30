<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
     protected $fillable = [
        'title','description','url','type','user_id','academy_id','coach_id','views','likes','dislikes','status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function academy()
    {
        return $this->belongsTo(Academy::class);
    }

    public function coach()
    {
        return $this->belongsTo(PrivateCoach::class,'coach_id');
    }

    public function reports()
    {
        return $this->hasMany(VideoReport::class);
    }
}
