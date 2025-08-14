<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TempImageController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;

        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $newName = uniqid() . '.' . $ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path() . '/admin-assets/assets/img/temp/', $newName);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/admin-assets/assets/img/temp/' . $newName),
                'image' => $tempImage->name,
                'message' => 'Image uploded successfully.'
            ]);
        }
    }

    public function update(Request $request)
    {
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }

        // Retrieve existing images, or initialize to an empty array if none exist
        $old_images = json_decode($product->gallery_image, true) ?? [];
        $new_images = [];

        // Process new images
        if ($request->hasFile('image')) {
            $name = uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('/admin-assets/assets/img/product/gallery_img/'), $name);
            $new_images[] = $name;
        }

        // Merge new images with old images
        $all_images = array_merge($old_images, $new_images);
        $product->gallery_image = json_encode($all_images, JSON_FORCE_OBJECT);

        // Save the product with updated gallery images
        $product->save();
        $new_image_key = array_key_last($all_images);
        // Return success response
        return response()->json([
            'status' => 'success',
            'image_id' => $new_image_key, // Return the last image's filename
            'ImagePath' => asset('/admin-assets/assets/img/product/gallery_img/' . end($new_images)),
            'message' => 'Images updated successfully'
        ]);
    }

    // public function destroy(Request $request)
    // {
    //     dd($request->all());
    //     $product = Product::find($request->product_id);
    //     if (!$product) {
    //         return back()->with('error', 'Product not found');
    //     }

    //     $filenames = json_decode($product->gallery_image, true);

    //     if (isset($filenames[$request->key])) {
    //         $imageName = $filenames[$request->key];
    //         $imagePath = public_path('admin-assets/assets/img/product/gallery_img/' . $imageName);

    //         unset($filenames[$request->key]);
    //         $product->gallery_image = json_encode(array_values($filenames), JSON_FORCE_OBJECT);

    //         if (File::exists($imagePath)) {
    //             File::delete($imagePath);
    //         }
    //     } else {
    //         return response()->json(['error' => 'Image key not found in gallery'], 404);
    //     }

    //     $product->update();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Product image deleted successfully'
        // ]);
    // }

    public function destroy(Request $request)
    {
        $product = Product::find($request->product_id);
        
        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        // Get the filenames array from the gallery
        $filenames = json_decode($product->gallery_image, true);

        // Check if the requested image key exists
        if (isset($filenames[$request->key])) {
            $imageName = $filenames[$request->key];
            $imagePath = public_path('admin-assets/assets/img/product/gallery_img/' . $imageName);

            // Remove the image from the array
            unset($filenames[$request->key]);
            
            // Reindex the array to ensure the keys are sequential
            $filenames = array_values($filenames);

            // Update the product gallery
            $product->gallery_image = json_encode($filenames, JSON_FORCE_OBJECT);
            $product->save();  // Save the updated gallery to the database

            // Delete the file from the server if it exists
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            // Return success response with updated gallery
            return response()->json([
                'status' => true,
                'message' => 'Product image deleted successfully',
                'updated_gallery' => $filenames
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Image key not found in gallery'], 404);
        }
}

}
