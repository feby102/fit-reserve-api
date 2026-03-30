<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
     protected $fillable = [

        'vendor_id',
        'name',
        'description',
        'city',
        'address',
        'price_per_hour',
        'status'

    ];

    //owner

   public function vendor(){
    return $this->belongsTo(Vendor::class);
}

    
 public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }
 

   public function schedules()
{
    return $this->hasMany(StadiumSchedule::class);
}



public function packages()
{
    return $this->hasMany(StadiumPackage::class);
}



public function reviews()
{
return $this->morphMany(Review::class,'reviewable');
}



public function facilities()
{
    return $this->morphMany(Facility::class, 'facilityable');
}
    }
