<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
 protected $fillable = ['balance','name','user_id'];   


 public function user(){
    return $this->belongsTo(User::class);
}

public function products()
{

return $this->hasMany(Product::class);

}

public function academies(){
    return $this->hasMany(Academy::class);
}

public function gyms(){
    return $this->hasMany(Gym::class);
}

public function store()
{
    return $this->hasOne(Store::class);
}
}
