<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachBooking extends Model
{
    protected $fillable = [

'user_id',

'private_coach_id',

'start_time',

'end_time',

'hours',

'total_price',

'status'

];



public function coach()
{
return $this->belongsTo(PrivateCoach::class,'private_coach_id');
}



public function user()
{
return $this->belongsTo(User::class);
}

}
