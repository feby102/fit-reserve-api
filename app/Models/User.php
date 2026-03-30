<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable

{use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'birth_date',
        'city' ,
        'role', 
        'activity', 
        'is_active', 
        'is_verified', 
        'wallet_balance', 
        'profile_image',
        'gender'
    ];
 



public function vendor(){
    return $this->hasOne(Vendor::class);
}



    public function wallet()
{
    return $this->hasOne(Wallet::class);
}


protected static function booted()
{
    static::created(function($user){
        Wallet::create(['user_id'=>$user->id]);
    });
}

 


public function bookings()
{
    return $this->hasMany(Booking::class, 'user_id'); 
}



public function stadiums()
{
    return $this->hasMany(Stadium::class);
}


public function academySubscriptions()
{
return $this->hasMany(AcademySubscription::class);
}

public function privateCoachBookings()
{
    return $this->hasMany(CoachBooking::class);
}

public function review()
{
return $this->belongsTo(Review::class);
}




public function  challenges()
{
    return $this->hasMany(Challenge::class, 'user_id'); 
}


public function ratingsGiven()
{
    return $this->hasMany(PlayerRating::class, 'evaluator_id');
}

 public function ratingsReceived()
{
    return $this->hasMany(PlayerRating::class, 'rated_player_id');
}

 public function getAverageRatingAttribute()
{
    return $this->ratingsReceived()->avg('rating') ?: 0;
}




public function notifications()
{

return $this->hasMany(Notification::class);

}


  public function conversations()
    {
        return $this->belongsToMany(user::class,'conversation_user');
    }


public function messages()
{

return $this->hasMany(Message::class);

}



public function loyaltyPoints()
{
    return $this->hasMany(LoyaltyPoint::class);
}




public function getTotalPointsAttribute()
{
    return $this->loyaltyPoints()->sum('points');
}





    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
