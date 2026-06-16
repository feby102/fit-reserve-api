<?php

namespace App\Models;


use Attribute;
use Illuminate\Database\Eloquent\Model;
use Storage;
class Gym extends Model
{
    protected $fillable = ['name','type','vendor_id','location','description',
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


   public function plans(){

  return $this->hasMany(GymPlan::class);

   }


   public function subscriptions()
{
return $this->hasManyThrough(
GymSubscription::class,
GymPlan::class
);
}



public function schedules()
{
return $this->hasMany(GymSchedule::class);
}



public function services()
{
    return $this->hasMany(AcademyService::class, 'academy_id');  
}



public function reviews()
{
return $this->morphMany(Review::class,'reviewable');
}


 public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
 
       }
      
      public function packages()
{
    return $this->hasMany(GymPackage::class);
}


public function facilities()
{
    return $this->morphMany(Facility::class, 'facilityable');
}
     
       }
