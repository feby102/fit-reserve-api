<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Storage;

class Stadium extends Model
{
     protected $fillable = [

        'vendor_id',
        'name',
        'description',
        'city',
        'address',
        'price_per_hour',
        'status',
        'image','is_verified'

    ];

    //owner


protected function image(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value ? url(Storage::url($value)) : null,
    );
}


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
