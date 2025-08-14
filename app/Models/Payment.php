<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        // 'user_id',
        // 'order_id',
        'transaction_id',
        'amount',
        'currency',
        'payment_method',
        'payment_status',
        'payment_details',
    ];
}