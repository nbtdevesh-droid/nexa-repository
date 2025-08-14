<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerSupportController extends Controller
{
    /**************************** Customer Support ********************************/
    public function edit()
    {
        $data['customer_support'] = CustomerSupport::find(1);
        return view('admin.pages.customer_support', $data);
    }

    public function update(Request $request, $id)
    {
        $data = CustomerSupport::find($id);

        if ($request->hasFile('image1') && $request->file('image1')->isValid()) {
            if ($data->image1) {
                File::delete(public_path('/admin-assets/assets/img/customer_support/' . $data->image1));
            }
            $ext = $request->file('image1')->getClientOriginalExtension();
            $newName = $request->page. uniqid() . '.' . $ext;
            $request->file('image1')->move(public_path() . '/admin-assets/assets/img/customer_support', $newName);
            $data->image1 = $newName;
        }

        $data->description1 = $request->description1;

        if ($request->hasFile('image2') && $request->file('image2')->isValid()) {
            if ($data->image2) {
                File::delete(public_path('/admin-assets/assets/img/customer_support/' . $data->image2));
            }
            $ext = $request->file('image2')->getClientOriginalExtension();
            $newName1 = $request->page. uniqid() . '.' . $ext;
            $request->file('image2')->move(public_path() . '/admin-assets/assets/img/customer_support', $newName1);
            $data->image2 = $newName1;
        }

        $data->description2 = $request->description2;
        $data->page = $request->page;

        $data->update();

        if ($data->id > 0) {
            return back()->withSuccess('Record Update Successfully.');
        } else {
            return back()->withSuccess('Record Update Failed.');
        }
    }

    /*******************************************Privacy Policy *****************************/
    public function privacy_policy(){
        $data['privacy_policy'] = CustomerSupport::where('page', 'privacy')->first();
        return view('admin.pages.privacy', $data);
    }

    /******************************************* Terms of use *****************************/
    public function terms_of_use(){
        $data['terms'] = CustomerSupport::where('page', 'Terms & Condition')->first();
        return view('admin.pages.terms', $data);
    }

    /**************************** Home Banner ********************************/
    public function HomeBannerEdit(){
        $data['home_banners'] = DB::table('home_banners')->get();
        return view('admin.pages.homebanneredit', $data);
    }

    public function HomeBannerUpdate(Request $request){
        if($request->id){
            $banner = DB::table('home_banners')->where('id', $request->id)->first();
    
            // Check if banner is found
            if (!$banner) {
                return response()->json([
                    'status' => false,
                    'message' => 'Image not found'
                ], 404);
            }
    
            // Construct the image path
            $imagePath = public_path('admin-assets/assets/img/home_banner/' . $banner->image);
    
            // Delete the old image if it exists
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
    
            // Handle file upload
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $name = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('admin-assets/assets/img/home_banner/'), $name);
    
                // Update the database with the new image name
                DB::table('home_banners')->where('id', $request->id)->update(['image' => $name]);
            }
    
            // Return a JSON response
            return response()->json([
                'status' => true,
                'message' => 'Image updated successfully'
            ]);
        }elseif($request->image){
            $image = $request->image;

            if(!empty($image)){
                foreach($image as $img){
                    $file = $img;
                    $name = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('admin-assets/assets/img/home_banner/'), $name);

                    DB::table('home_banners')->insert(['image' => $name]);
                }

                return back()->withSuccess('Image update successfully');
            }
        }else{
            return back()->withSuccess('Data update successfully');
        }
    }

    public function HomeBannerDelete(Request $request)
    {
        $id = $request->data_id;
        $image = DB::table('home_banners')->where('id', $id)->first();
    
        if ($image) {
            $imagePath = public_path('admin-assets/assets/img/home_banner/' . $image->image);
            
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
    
            DB::table('home_banners')->where('id', $id)->delete();
    
            return response()->json(['status' => true, 'message' => 'Data deleted successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Data not found']);
        }
    }

    public function getHomeIcon(){
        $data['home_icons'] = DB::table('home_icon')->get();
        return view('admin.pages.HomeIcon', $data);
    }

    public function UpdateHomeIcon(Request $request)
    {
        $rules = [];
        $messages = [];

        for ($i = 1; $i <= 4; $i++) {
            $rules["icon_{$i}"] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
            $rules["link_{$i}"] = 'required|url';

            $messages["icon_{$i}.image"] = "Home Icon {$i} must be a valid image file.";
            $messages["icon_{$i}.mimes"] = "Home Icon {$i} must be in JPEG, PNG, or JPG format.";
            $messages["icon_{$i}.max"] = "Home Icon {$i} must not exceed 2MB.";
            $messages["link_{$i}.required"] = "Link for Home Icon {$i} is required.";
            $messages["link_{$i}.url"] = "Link for Home Icon {$i} must be a valid URL.";
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        for ($i = 1; $i <= 4; $i++) {
            $iconField = "icon_{$i}";
            $linkField = "link_{$i}";

            $homeIcon = DB::table('home_icon')->where('id', $i)->first();

            $iconPath = $homeIcon->icon ?? null;
            $name = $iconPath; // Set default value to existing icon path

            if ($request->hasFile($iconField)) {
                $file = $request->file($iconField);
                $name = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('admin-assets/assets/img/Home_icon/'), $name);

                // if ($iconPath && file_exists(public_path($iconPath))) {
                //     unlink(public_path($iconPath));
                // }
                $imagePath = public_path('admin-assets/assets/img/Home_icon/' . $iconPath);
    
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }

                // $iconPath = 'admin-assets/assets/img/Home_icon/' . $name;
            }

            DB::table('home_icon')->updateOrInsert(
                ['id' => $i],
                [
                    'icon' => $name,
                    'link' => $request->input($linkField),
                    'updated_at' => now(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Home icons updated successfully.');
    }
}
