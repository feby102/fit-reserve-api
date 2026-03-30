<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymPlan extends Model
{
        protected $fillable = ['gym_id','price' ,'name','type','hours_per_day'];
        public function subscriptions(){

        return $this->hasMany(GymSubscription::class);
        }


        public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function gym() {
    return $this->belongsTo(Gym::class);
}
}
