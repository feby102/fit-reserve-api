<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $table = 'LedgerEntry'; 
    protected $fillable = [
        'account_type',
        'account_id',
        'type',
        'amount',
        'description',
        'reference_id',
        'reference_type'
    ];

    public function account()
    {
        return $this->morphTo();
    }
}
