<?php

namespace App\Models;

use App\Traits\Common_trait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Category extends Model
{
    use HasFactory, Common_trait;

    function InsertData($data, $category = null)
    {
        //Create slug
        $category_slug = $this->create_unique_slug($data->category_name, 'categories', 'slug');

        if (!$category) {
            $category = new Category();
        }
        if ($data->category_icon) {
            if($category->image){
                File::delete(public_path('/admin-assets/assets/img/category/' . $category->image));
            }
            $ext = $data->category_icon->getClientOriginalExtension();
            $newName = $category_slug . '.' . $ext;

            $path = public_path('/admin-assets/assets/img/category/');

            $manager = new ImageManager(new Driver());
            $image = $manager->read($data->file('category_icon'));
            $image = $image->resize(120,120);
            $image->toJpeg(80)->save($path.$newName);

            // $data->category_icon->move(public_path() . '/admin-assets/assets/img/category', $newName);
            $category->image = $newName;
        }


        $category->category_name = $data->category_name;
        if($data->parent_id != ""){
            $category->parent_id = $data->parent_id;
        }else{
            if($data->banner_image){
                if($category->banner_image){
                    File::delete(public_path('/admin-assets/assets/img/category_banner_image/' . $category->banner_image));
                }
                $ext = $data->banner_image->getClientOriginalExtension();
                $newName = $category_slug . '.' . $ext;
                $data->banner_image->move(public_path() . '/admin-assets/assets/img/category_banner_image', $newName);
                $category->banner_image = $newName;
            }
        }
        $category->slug = $category_slug;
        $category->status = $data->category_status;
        $category->category_order = $data->category_order;

        if ($category->save()) {
            return 1;
        } else {
            return 0;
        }
    }


    public function subcategory()
    {
        return $this->hasMany(Category::class, 'parent_id')->whereStatus(1)->select('id', 'category_name', 'parent_id', 'image')->orderBy('category_order', 'asc');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('childrenRecursive');
    }

}
