<?php

namespace App\Models;


use Attribute;
use Illuminate\Database\Eloquent\Model;
use Storage;
class Academy extends Model
{
  protected $fillable = [ 'type','vendor_id','name','location','is_active','price_per_hour',
        'image','is_verified'
];

protected $appends = ['image_url'];   

public function getImageUrlAttribute()
{
    if ($this->image) {
        return asset('storage/' . $this->image);
    }

    return null;  }


protected function image(): Attribute
{
    return Attribute::make(
        get: fn ($value) => $value ? url(Storage::url($value)) : null,
    );
}


public function videos()
{
    return $this->hasMany(Video::class);
}

public function vendor(){
    return $this->belongsTo(Vendor::class);
}



public function  privateCoich()
{
return $this->hasMany( PrivateCoach::class);
}



 

public function plans()
{
    return $this->hasMany(AcademyPlan::class);
}

public function services()
{
    return $this->hasMany(AcademyService::class);
}

public function students()
{
    return $this->hasMany(AcademyStudent::class);
}

 

public function reviews()
{
return $this->morphMany(Review::class,'reviewable');
}



public function subscriptions()
{
return $this->hasManyThrough(
 AcademySubscription::class,
 AcademyPlan::class
);
}




public function facilities()
{
    return $this->morphMany(Facility::class, 'facilityable');
}


 public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }


 public function packages()
{
    return $this->hasMany(AcademyPackage::class);
}   


public function challenge()
{
    return $this->hasMany(Challenge::class);
}   
}
