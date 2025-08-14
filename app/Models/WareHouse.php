<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WareHouse extends Model
{
    use HasFactory;
    protected $table = 'warehouse';

    public function insertId($request, $house = null)
    {
        $country = Country::find($request->country);
        $state = DB::table('states')->where('id', $request->state)->first();
        if (!$house) {
            $house = new WareHouse();
        }
        $house->warehouse_name = $request->warehouse_name;
        $house->contact_name = $request->contact_name;
        $house->contact_email = $request->contact_email;

        $country_code = str_starts_with($request->country_code, '+') ?  $request->country_code : '+' . $request->country_code;

        $house->country_code = $country_code;
        $house->contact_number = $request->contact_number;
        $house->street_address = $request->street_address;
        $house->country = $country->name;
        $house->state = $state->name;
        $house->city = $request->city;
        $house->zip_code = $request->postal_code;
        $house->status = $request->status;

        return $house->save() ? 1 : 0;
    }

}   