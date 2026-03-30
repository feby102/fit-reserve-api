<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerRating extends Model
{
 protected $fillable = [
        'challenge_id',
        'evaluator_id',
        'rated_player_id',
        'rating',
        'comment'
    ];
    
    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

     public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

     public function ratedPlayer()
    {
        return $this->belongsTo(User::class, 'rated_player_id');
    }
    }
