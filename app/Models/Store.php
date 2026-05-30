<?php

namespace App\Models;


use Attribute;
use Illuminate\Database\Eloquent\Model;
use Storage;
class Store extends Model
{
    protected $fillable = ['vendor_id','name','description','logo','is_active',
        'image','is_verified'
];



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


    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

public function reviews()
{
return $this->morphMany(Review::class,'reviewable');
}

    }