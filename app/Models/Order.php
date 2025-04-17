<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['order_status','tracking_number','tracking_carrier_code'];
    protected $casts = [
        'shiping_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function GetTransactionDetail()
    {
        return $this->hasOne(Payment::class, 'transaction_id', 'transaction_id');
    }

    // Accessor to decode the shipping address
    public function getShippingAddressAttribute()
    {
        return json_decode($this->attributes['shiping_address_id'], true) ?? [];
    }

    // Accessor to get the full formatted address
    public function getFormattedAddressAttribute()
    {
        $address = $this->shipping_address;

        return $address['address'] . ', ' . $address['city'] . ', ' . $address['state'] . ', ' . $address['country'] . ' - ' . $address['zip_code'];
    }

    // Accessor to get full customer phone with country code
    public function getCustomerPhoneAttribute()
    {
        $address = $this->shipping_address;

        return $address['country_code'] . ' ' . $address['phone'];
    }

    function update_status($data, $orders = null){
        $orders->order_status = $data->order_status;
        $orders->shiping_date = $data->shipping_date ? date('Y-m-d', strtotime($data->shipping_date)) : null;;
        return $orders->update() ? 1 : 0;
    }
}
