<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymSchedule extends Model
{
    protected $fillable = [ 'gym_id' ,'day' ,'start_time','end_time' ];
}
