<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    
   protected $fillable = [
        'user_id',
        'bookable_id',
        'bookable_type',
        'start_time',
        'end_time',
        'hours',
        'total_price',
        'payment_method',
        'status',
        'full_name',        
        'age',              
        'parent_id_card',   
        'personal_photo',   
        'coupon_code',         
        'discount_amount' ,
        'rejection_reason'     ];
 
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

       public function user(){

    return $this->belongsTo(User::class);


    }

public function bookable()
    {
        return $this->morphTo();
    }
    
    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }



    public function package()
{
    return $this->belongsTo(StadiumPackage::class);
}

 public function services()
    {
        return $this->belongsTo(AcademyService::class);
    }



public function facility()
{
    return $this->belongsTo(Facility::class);
}


public function studio()
{
    return $this->belongsTo(Studio::class);
}


public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }
}
