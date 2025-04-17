<?php

namespace App\Models;

use App\Traits\Common_trait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetails extends Model
{
    use HasFactory;

    protected $table = 'customer_bank_details';
}