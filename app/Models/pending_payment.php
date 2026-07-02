<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pending_payment extends Model
{
 protected $fillable = [
        'user_id',
        'type',
        'reference_id',
        'amount',
        'paymob_order_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
