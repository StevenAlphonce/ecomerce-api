<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_id',
        'order_amount',
        'currency',
        'payment_method',
        'payment_status',
    ];
}
