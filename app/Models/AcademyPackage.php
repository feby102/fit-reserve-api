<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyPackage extends Model
{
      protected $fillable = [

        'acadmy_id',
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
