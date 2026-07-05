<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=[

'user_id',
    'total_price',
    'status',
    'payment_method',
    'payment_status',
    'paymob_order_id'

];



 public function items()
{
    return $this->hasMany(OrderItem::class);
}
}
