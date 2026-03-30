<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Studio extends Model
{
   
    protected $fillable = [
    'user_id',   
    'name',
        'description',
        'address',
        'price_per_hours'
    ];

    public function booking(){

    return $this->morphMany(Booking::class,'bookable');
    }


     public function packages()
{
    return $this->hasMany(StudioPackage::class);
}


public function facilities()
{
    return $this->morphMany(Facility::class, 'facilityable');
}
}
