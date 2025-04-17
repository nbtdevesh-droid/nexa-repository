<?php

namespace App\Models;

use App\Traits\Common_trait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory, Common_trait;
    protected $appends = ['formatted_id'];

    protected $fillable = [
        'product_name', 'slug', 'description', 'parent_category', 'child_category',
             'brand_id', 'quantity', 'min_order', 'regular_price', 'sale_price',
             'sku', 'feature_image', 'status', 'flash_deal'
     ];

    function insertId($data, $product = null)
    {
        $isNewProduct = false;

        if (!$product) {
            $product = new Product();
            $isNewProduct = true;
        }

        $product->product_name = $data->product_name;
        $product->slug = $this->create_unique_slug($data->product_name, 'products', 'slug');
        $product->description = $data->description;
        $product->parent_category = $data->parent_category;
        $product->child_category = $data->child_category;
        $product->brand_id = $data->brand;
        $product->sku = $data->sku;
        $product->quantity = $data->stock_quantity;
        $product->regular_price = $data->regular_price;
        $product->sale_price = $data->sale_price;
        $product->min_order = $data->min_order;
        $product->status = $data->status;
        $product->flash_deal = $data->flash_deal ? $data->flash_deal : 0;

        // $product->start_flash_deal = $data->start_at ? date('Y-m-d H:i:s', strtotime($data->start_at)) : null;
        // $product->end_flash_deal = $data->expire_at ? date('Y-m-d H:i:s', strtotime($data->expire_at)) : null;

        if (!empty($data->feature_image)) {
            $tempImageInfo = TempImage::find($data->feature_image);
            $extArray = explode('.', $tempImageInfo->name);
            $ext = last($extArray);

            $imageName = time() . '.' . $ext;
            $sourcePath = public_path() . '/admin-assets/assets/img/temp/' . $tempImageInfo->name;
            $dPath = public_path() . '/admin-assets/assets/img/product/feature_img/' . $imageName; //destination path
            File::copy($sourcePath, $dPath);

            if ($product->feature_image) {
                File::delete(public_path('/admin-assets/assets/img/product/feature_img/' . $product->feature_image));
            }
            $product->feature_image = $imageName;
        }

        // Handle gallery images
        if ($isNewProduct && !empty($data->image_array)) {
            $galleryImages = [];
            foreach ($data->image_array as $temp_image_id) {
                $tempImageInfo = TempImage::find($temp_image_id);
                $extArray = explode('.', $tempImageInfo->name);
                $ext = end($extArray);

                $imageName = uniqid() . '.' . $ext;
                $sourcePath = public_path('admin-assets/assets/img/temp/' . $tempImageInfo->name);
                $destinationPath = public_path('admin-assets/assets/img/product/gallery_img/' . $imageName);

                if (File::exists($sourcePath)) {
                    File::copy($sourcePath, $destinationPath);
                    $galleryImages[] = $imageName;
                }
            }
            $product->gallery_image = json_encode($galleryImages, JSON_FORCE_OBJECT);
        }

        if (Auth::guard('member')->check()) {
            $product->user_id = Auth::guard('member')->user()->id;
        }
        $product->save();

        if ($product->id > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function DeleteData($id)
    {
        $product = Product::find($id);
        if (empty($product)) {
            return back()->with('error', 'Product not found');
        }

        if (!empty($product->gallery_image)) {
            $productImages = json_decode($product->gallery_image, true);
            foreach ($productImages as $image) {
                File::delete(public_path('/admin-assets/assets/img/product/gallery_img/' . $image));
            }
        }
        // File::delete(public_path('/admin-assets/assets/img/product/feature_img/' . $product->feature_image));


        if ($product) {
            $product->delete();
            return 1;
        } else {
            return 0;
        }
    }

    public function subcategory()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function brands()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function getFormattedIdAttribute()
    {
        return '#' . str_pad($this->attributes['id'], 7, '0', STR_PAD_LEFT);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relationship to ReviewHelpful (through reviews)
    public function helpfulVotes()
    {
        return $this->hasManyThrough(ReviewHelpful::class, Review::class);
    }

    public function productClickCounts()
    {
        return $this->hasMany(ProductClickCount::class, 'product_id');
    }
}
