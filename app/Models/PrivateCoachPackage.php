<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateCoachPackage extends Model
{
     protected $fillable = [

        'private_coach_id',
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
