<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    
protected $fillable = [

'type',

'total_profit',

'total_bookings',
'report_date'
,'vendor_id'
];


    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
