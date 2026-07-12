<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Request;
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
        'image','is_verified','video_id'

    ];

    //owner


protected $appends = ['image_url'];

public function getImageUrlAttribute()
{
    return $this->image
        ? asset('storage/' . $this->image)
        : null;
}

public function scopeStadium($query, $area)
{
    return $query->where('address', 'like', "%{$area}%");
}

public function videos()
{
    return $this->hasMany(Video::class);
}



//    public function vendor(){
//     return $this->belongsTo(Vendor::class);
// }




public function vendor(){

return $this->belongsTo(User::class, 'vendor_id');
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
