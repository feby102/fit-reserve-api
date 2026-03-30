<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    protected $fillable=[
'vendor_id',
'name',
'description',
'price',
'duration',
'max_quantity',
'is_active'

];


public function vendor(){
    return $this->belongsTo(Vendor::class);
}


public function facilityeable(){
    return $this->morphTo();
}
 public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }
}
