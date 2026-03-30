<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StadiumSchedule extends Model
{
    
    protected $fillable = [

        'stadium_id',
        'day',
        'start_time',
        'end_time'

    ];


    public function stadium()
    {
        return $this->belongsTo(Stadium::class);
    }

}
 