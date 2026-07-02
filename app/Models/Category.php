<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'seller_id','image'];


    public function products()
{

return $this->hasMany(Product::class);

}

public function seller() {
    return $this->belongsTo(User::class, 'seller_id');
}
}
