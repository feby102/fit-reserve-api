<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademySubscription extends Model
{
     protected $fillable = [ 'user_id' ,'academy_plan_id','start_date' ,'end_date' ];




public function plan()
    {
        return $this->belongsTo(AcademyPlan::class, 'academy_plan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
