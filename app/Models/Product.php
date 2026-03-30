<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
protected $fillable = [
    'name' ,
         'description'  ,
          'price', 
          'discount' ,
         'image'  ,
         'category_id'  ,
       'vendor_id',
           'store_id'];


 public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }


public function vendor()
{

return $this->belongsTo(Vendor::class);

}



public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}

public function reviews()
{
return $this->morphMany(Review::class,'reviewable');
}
}
