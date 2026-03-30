<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'vendor_id',
        'code',
        'type',
        'value',
        'min_order_value',
        'max_usage',
        'user_usage_limit',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];



public function isValid()
    {
        if (!$this->is_active) return false;
        
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        
        if ($this->max_usage !== null && $this->used_count >= $this->max_usage) return false;

        return true;
    }



public function calculateDiscount($total)
    {
        if ($this->type === 'percent') {
            return ($total * ($this->value / 100));
        }
        
        return min($this->value, $total);  
    }



   public function services()
{
    return $this->belongsToMany(
        AcademyService::class,
        'coupons_services',      
        'coupon_id',
        'academy_service_id'     
    );
}

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }
}
