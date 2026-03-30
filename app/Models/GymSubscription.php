<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymSubscription extends Model
{
         protected $fillable = [ 'user_id' ,'gym_plan_id','start_date' ,'end_date' ,'status','auto_renew'];

}
