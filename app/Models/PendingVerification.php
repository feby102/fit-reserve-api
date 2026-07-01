<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'documents',
        'payment_method',
        'phone_number',
        'price',
        'paymob_order_id'
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}