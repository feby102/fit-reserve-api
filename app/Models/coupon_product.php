<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class coupon_product extends Model
{


public function products() {
    return $this->belongsToMany(Product::class, 'coupons_products');
}}
