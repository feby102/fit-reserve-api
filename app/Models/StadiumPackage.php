<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StadiumPackage extends Model
{
      protected $fillable = [

        'stadium_id',
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
