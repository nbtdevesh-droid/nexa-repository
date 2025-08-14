<?php

namespace App\Models;

use App\Traits\Common_trait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory, Common_trait;

    function InsertData($data, $brand = null){
        $brand_slug = $this->create_unique_slug($data->brand_name, 'brands', 'slug');

        if(!$brand){
            $brand = new Brand();
        }
        $brand->brand_name = $data->brand_name;
        $brand->slug = $brand_slug;
        $brand->status = $data->brand_status;

        if($brand->save()){
            return 1;
        }else{
            return 0;
        }
    }
}
