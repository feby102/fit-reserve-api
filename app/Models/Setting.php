<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

 
    class Setting extends Model
{

protected $fillable = [
    'commission_rate',
    'cancellation_policy',
    'is_store_enabled',
    'is_challenges_enabled',
    'is_videos_enabled',
    'terms',
    'privacy_policy',
    'about_us',
    'banner',
];
}

