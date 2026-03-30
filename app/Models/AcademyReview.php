<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyReview extends Model
{
    protected $fillable = [  'user_id', 'academy_id', 'rating','review'];

 
}
