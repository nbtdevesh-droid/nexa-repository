<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class User extends Model
{
    use HasFactory, HasApiTokens, Notifiable;
    protected $table = 'user';

    function InsertId($data, $user = null){
        $isNewUser = true;

        if(!$user){
            $user = new User();
            $isNewUser = true;
        }

        $user->first_name = $data->first_name;
        $user->last_name = $data->last_name;
        $user->email = $data->email;
        $user->country = $data->country;
        if ($data->phone) {
            $country_code = str_starts_with($data->country_code, '+') ?  $data->country_code : '+' . $data->country_code;
            $user->phone = $data->phone;
            $user->country_code = $country_code;
        }

        if($data->password){
            $user->password = Hash::make($data->password);
        }

        if ($data->hasFile('image') && $data->file('image')->isValid()) {
            $ext = $data->file('image')->getClientOriginalExtension();
            $newName = time() . '.' . $ext;

            $path = public_path('/admin-assets/assets/img/profile_img/user/');

            $manager = new ImageManager(new Driver());
            $image = $manager->read($data->file('image'));
            $image = $image->resize(200,200);
            $image->toJpeg(80)->save($path.$newName);

            // $data->file('image')->move(public_path() . '/admin-assets/assets/img/profile_img/user', $newName);
            if($user->image){
                File::delete(public_path('/admin-assets/assets/img/profile_img/user/' . $user->image));
            }
            $user->image = $newName;
        }
        $user->status = $data->status;
        $user->current_steps = 'step_2';
        if ($user->save()) {
            if ($isNewUser == true) {
                Mail::send('admin.email.UserRegister', ['data' => $user], function ($message) use ($data) {
                    $message->to($data->email);
                    $message->subject('Welcome to NEXA');
                });
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function review_helpful()
    {
        return $this->hasMany(ReviewHelpful::class);
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function shipping_address()
    {
        return $this->hasMany(ShippingAddress::class);
    }
}
