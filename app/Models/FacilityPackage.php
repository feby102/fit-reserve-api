<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityPackage extends Model
{
     protected $fillable = [

        'facility_id',
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
