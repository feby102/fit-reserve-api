<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachSchedule extends Model
{
 protected $fillable = ['private_coach_id','date','day','start_time','end_time','is_booked'];




public function coach(){

return $this->belongsTo(PrivateCoach::class);
}



    }
