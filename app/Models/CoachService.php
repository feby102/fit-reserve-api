<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachService extends Model
{
        protected $fillable = [  'private_coach_id' ,'name' ,'price',  ];


public function user()
{
return $this->belongsTo(User::class);
}


public function academy()
{
     return $this->belongsTo(Academy::class, 'academy_id');
}
public function privateCoach()
{
     return $this->belongsTo(PrivateCoach::class, 'private_coach_id');
}

public function plan()
{
return $this->belongsTo(GymPlan::class);
}


public function bookings()
{
    return $this->morphMany(Booking::class, 'bookable');
}

public function gym()
{
    return $this->belongsTo(Gym::class);
}

 
}
