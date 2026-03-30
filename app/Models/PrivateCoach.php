<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateCoach extends Model
{
    protected $fillable = [

'name', 'sport', 'price_per_hour', 'bio', 'academy_id', 'vendor_id'
];


public function vendor()
{
    return $this->belongsTo(Vendor::class);
} 

 public function academy()
    {
        return $this->belongsTo(Academy::class,'academy_id');
    }



public function locations()
{
return $this->hasMany(CoachLocation::class);
}


public function services()
{
return $this->hasMany(CoachService::class);
}

 

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }


public function reviews()
{
return $this->morphMany(Review::class,'reviewable');
}


public function facilities()
{
    return $this->morphMany(Facility::class, 'facilityable');
}

public function packages()
{
    return $this->hasMany(PrivateCoachPackage::class);
}

 }
