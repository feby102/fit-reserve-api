<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudioPackage extends Model
{
  protected $fillable = [

        'studio_id',
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
