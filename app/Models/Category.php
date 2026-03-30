<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'vendor_id','image'];


    public function products()
{

return $this->hasMany(Product::class);

}


public function vendor()
{
    return $this->belongsTo(Vendor::class);
}
}
