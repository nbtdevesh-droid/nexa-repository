<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductClickCount extends Model
{
    protected $table = 'product_click_count';
    protected $fillable = ['user_id', 'product_id', 'count'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
}
?>