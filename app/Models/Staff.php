<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'staff';

    function InsertId($data, $staff = null){
        $isNewStaff = false;

        if(!$staff){
            $staff = new Staff();
            $isNewStaff = true;
        }

        $staff->first_name = $data->first_name;
        $staff->last_name = $data->last_name;
        $staff->email = $data->email;
        $staff->country = $data->country;
        if ($data->phone) {
            $country_code = str_starts_with($data->country_code, '+') ?  $data->country_code : '+' . $data->country_code;
            $staff->phone = $data->phone;
            $staff->country_code = $country_code;
        }

        if($data->password){
            $staff->password = Hash::make($data->password);
        }

        if ($data->hasFile('image') && $data->file('image')->isValid()) {
            $ext = $data->file('image')->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            $path = public_path('/admin-assets/assets/img/profile_img/staff/');

            $manager = new ImageManager(new Driver());
            $image = $manager->read($data->file('image'));
            $image = $image->resize(200,200);
            $image->toJpeg(80)->save($path.$newName);

            // $data->file('image')->move(public_path() . '/admin-assets/assets/img/profile_img/staff', $newName);
            if($staff->image){
                File::delete(public_path('/admin-assets/assets/img/profile_img/staff/' . $staff->image));
            }
            $staff->image = $newName;
        }
        $staff->status = $data->status;
        if ($staff->save()) {
            if ($isNewStaff == true) {
                Mail::send('admin.email.StaffRegister', ['data' => $staff], function ($message) use ($data) {
                    $message->to($data->email);
                    $message->subject('Welcome to NEXA');
                });
            }
            return 1;
        } else {
            return 0;
        }
    }
}
