<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyPlan extends Model
{
    protected $fillable = [ 'academy_id','price' ,'name','type','max_students'];


    public function vendor()
{
    return $this->belongsTo(Vendor::class);
}

public function academy()
{
return $this->belongsTo(Academy::class);
}


 public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    public function plan()
{
    return $this->belongsTo(AcademyPlan::class, 'academy_plan_id');
}
}
