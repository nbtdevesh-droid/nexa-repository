<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    public function cartProduct()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function cartCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
