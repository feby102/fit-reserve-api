<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
  protected $fillable = [

'title',

'academy_id',

'max_players',

'price',

'duration',

'status',
'vendor_id'
];

public function vendor(){
    return $this->belongsTo(Vendor::class);
}

public function academy()
{
return $this->belongsTo(Stadium::class);
}


public function participants()
{
return $this->hasMany(ChallengeParticipant::class);
}


public function acceptedParticipants()
{
return $this->participants()->where('status','accepted');
}


public function playerRatings()
{
    return $this->hasMany(PlayerRating::class);
}



public function reviews()
{
    return $this->morphMany(Review::class, 'reviewable');
}
}
