<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{    use HasApiTokens, Notifiable;

 
 
    protected $fillable = [
        'name',
        'email',
        'phone',
        'city',
        'area',
        'password',
        'balance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',  
    ];


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




public function preferredLocale()
{
    return 'ar';
}

// ضيفي الميثود دي عشان تقولي لـ لارافيل يربطه بـ جارد الفيندور تلقائياً
public function authGuard()
{
    return 'vendor-api';
}

}
