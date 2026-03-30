<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymPackage extends Model
{
    
      protected $fillable = [

        'gym_id',
        'name',
        'hours',
        'price',
        'type'

    ];
 public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }
}
