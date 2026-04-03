<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyService extends Model
{
      protected $fillable = [  'academy_id' ,'name' ,'price', 'duration','max_number' ,'is_active' ];


public function user()
{
return $this->belongsTo(User::class);
}



public function plan()
{
return $this->belongsTo(AcademyPlan::class,'academy_plan_id');
}


public function bookings()
{
    return $this->morphMany(Booking::class, 'bookable');
}

public function academy()
{
    return $this->belongsTo(Academy::class);
}


public function vendor()
{
    return $this->belongsTo(Vendor::class);
}
public function reviews()
{
return $this->morphMany(Review::class,'reviewable');
}
}
