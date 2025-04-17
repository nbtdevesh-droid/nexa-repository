<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthApiController extends Controller
{
    public function user_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'credentials' => 'required|email',
            'password' => 'required',
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        }

        $input = $request->all();

        if (filter_var($request->credentials, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->credentials)->first();

            if ($user) {
                if ($user->status == '1') {
                    if (!Hash::check($request->password, $user->password)) {
                        return Response::json(array('status' => 'failed', 'message' => 'Incorrect email or password. Please check your details and try again.', 'token' => null, 'token_id' => null, 'user_data' => null));
                    } else {
                        $profile = User::select('id', 'first_name','last_name', 'email', 'country', 'country_code', 'phone', 'image', 'current_steps')->where('id', $user->id)->first();

                        $profile->image = !empty($profile->image) ? asset('admin-assets/assets/img/profile_img/user/' . $profile->image) : asset('admin-assets/assets/img/profile_img/user/common.png');

                        $tokenData = $user->createToken($request->credentials);
                        $token = $tokenData->plainTextToken;
                        $tokenId = $tokenData->accessToken->id;

                        DB::table('device_tokens')->updateOrInsert(
                            ['user_id' => $user->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
                        );
                                        
                        return Response::json(array('status' => 'success', 'message' => 'Login Successfully', 'token' => $token, 'token_id' => $user->id, 'user_data' => $profile));
                    }
                }else{
                    return Response::json(array('status' => 'failed', 'message' => 'Customer is blocked', 'token' => null, 'token_id' => null, 'user_data' => null));
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'Incorrect email or password. Please check your details and try again.', 'token' => null, 'token_id' => null, 'user_data' => null]);
            }
        }
    }

    public function customerExistOrNot(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required_with:phone',
            'credentials' =>
            [
                'required',
                'numeric',
                Rule::unique('user', 'phone')->where(function ($query) use ($request) {
                    return $query->where('country_code', $request->country_code); // Ensure the phone is unique with respect to country_code
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        }

        // Check if the user exists with the provided phone number and country code
        $user = User::where('phone', $request->credentials)->where('country_code', $request->country_code)->first();

        if (!$user) {
            return response()->json(['status' => 'success', 'message' => 'Customer does not exist']);
        }
        return response()->json(['status' => 'failed', 'message' => 'The credentials have already been taken.']);
    }

    public function customerRegisterOrLogin(Request $request)
    {
        $userCount = User::Where('phone', $request->credentials)->where('country_code', $request->country_code)->first();

        if ($userCount) {
            if ($userCount->status == '1') {
                $profile = User::select('id', 'first_name','last_name', 'email', 'country', 'country_code', 'phone', 'image', 'current_steps')->where('id', $userCount->id)->first();
                $profile->image = !empty($profile->image) ? asset('admin-assets/assets/img/profile_img/user/' . $profile->image) : asset('admin-assets/assets/img/profile_img/user/common.png');

                // DB::table('device_tokens')->updateOrInsert(
                //     ['user_id' => $userCount->id, 'device_token' => $request->fcm_token]
                // );
                // $token = $userCount->createToken($request->credentials)->plainTextToken;

                $tokenData = $userCount->createToken($request->credentials);
                $token = $tokenData->plainTextToken;
                $tokenId = $tokenData->accessToken->id;

                DB::table('device_tokens')->updateOrInsert(
                    ['user_id' => $userCount->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
                );

                if (is_numeric($request->credentials)) {
                    return Response::json(array('status' => 'success', 'message' => 'Login Successfully', 'token' => $token, 'token_id' => $userCount->id, 'userdata' => $profile));
                }
            }else{
                return Response::json(array('status' => 'success', 'message' => 'Customer is blocked', 'token' => null, 'token_id' => null, 'userdata' => null));
            }
        } else {
            $User = new User();
            $User->phone = $request->credentials;
            $User->country_code = $request->country_code;
            $User->status = 1;
            $User->current_steps = 'step_1';
            $User->save();

            DB::table('device_tokens')->updateOrInsert(
                ['user_id' => $User->id, 'device_token' => $request->fcm_token]
            );

            $token = $User->createToken($request->credentials)->plainTextToken;

            if (is_numeric($request->credentials)) {
                return Response::json(array('status' => 'success', 'message' => 'Sign up Successfully', 'token' => $token, 'token_id' => $User->id, 'userdata' => $User));
            }
        }
    }

    public function recover_password(Request $request){
        $validator = Validator::make($request->all(), [
            'credentials' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'  => 'failed', 'message' => $validator->errors()->first()]);
        } else {
            $user = User::where('email', $request->credentials)->first();

            $randomNumber = random_int(100000, 999999);
            $endTime = now()->addMinutes(10);

            if(!$user){
                return Response::json(array('status' => 'failed', 'message' => 'Invalid credentials', 'otp' => ''));
            }
            User::where('email', $request->credentials)->update([
                'email' => $request->credentials,
                'otp' => $randomNumber,
                'otp_created_at' => $endTime,
                'otp_verified' => 0,
            ]);

            $mailData = [
                'email' => $request->credentials,
                'otp' => $randomNumber,
            ];

            try {
                Mail::send('admin.email.OtpVerified', ['data' => $mailData], function ($message) use ($mailData) {
                    $message->to($mailData['email']);
                    $message->subject('Reset Password');
                });
                return response()->json(['status' => 'success', 'message' => 'OTP sent to your email', 'otp' => $randomNumber]);
            } catch (Exception $e) {
                return response()->json(['status' => 'failed', 'message' => 'Error sending OTP email: ' . $e->getMessage()]);
            }
        }
    }

    public function reset_password_otp_verification(Request $request){
        $userdata = User::where('email', $request->credentials)->first();

        if (!$userdata) {
            return Response::json(['status' => 'failed', 'message' => 'Incorrect Credentials.']);
        }
        if ($userdata['otp'] == $request->otp && $userdata['otp_verified'] == 0) {
            if ($userdata['otp_created_at'] < now()) {
                return Response::json(['status' => 'failed', 'message' => 'OTP Expired']);
            } else {
                User::where('email', $request->credentials)->update(['otp' => null, 'otp_created_at' => null, 'otp_verified' => '1']);
                return Response::json(['status' => 'success', 'message' => 'OTP Verified']);
            }
        }else{
            return Response::json(['status' => 'failed', 'message' => 'OTP does not match']);
        }
    }

    public function reset_password(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'password' => ['required', 'min:6'],
            ]
        );

        if ($validateUser->fails()) {
            return response()->json([ 'status' => false, 'message' => 'validation error', 'errors' => $validateUser->errors()]);
        } else {
            $user = User::where('email', $request->credentials)->first();

            if ($user) {
                if ($user->status == 1 && $user->otp_verified == 1) {
                    $user = User::where('email', $request->credentials)->update(['password' => Hash::make($request->password)]);
                    return Response::json(array('status' => 'success', 'message' => 'Your password has been successfully reset. Please log in with your new password.'));
                } else { 
                    return Response::json(array('status' => 'failed', 'message' => 'Otp not verified'));
                }
            }
        }
    }

    public function email_verify(Request $request) {
        $validator = validator($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('user')->ignore(auth('sanctum')->user()->id),
            ],
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email address is already taken.',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        }
    
        $verificationCode = rand(100000, 999999);
        $user = User::find(auth('sanctum')->user()->id);
        // dd($user);
        $endTime = now()->addMinutes(5);
    
        if (!$user) {
            return response()->json(['status' => 'failed', 'message' => 'Invalid credentials', 'otp' => '']);
        }
    
        User::where('id', auth('sanctum')->user()->id)->update([
            'otp' => $verificationCode,
            'otp_created_at' => $endTime,
            'email_otp_verified' => 0,
        ]);
    
        $mailData = [
            'subject' => 'Email Verify OTP.',
            'email' => $request->email,
            'verificationCode' => $verificationCode,
        ];
    
        try {
            Mail::send('admin.email.EmailVerify', ['data' => $mailData], function ($message) use ($mailData) {
                $message->to($mailData['email']);
                $message->subject($mailData['subject']);
            });
            return response()->json(['status' => 'success', 'message' => 'OTP sent to your email', 'otp' => $verificationCode]);
        } catch (Exception $e) {
            \Log::error('Error sending OTP email: '.$e->getMessage()); // Log the error for further analysis
            return response()->json(['status' => 'failed', 'message' => 'Error sending OTP email.']);
        }
    }
    
    public function email_otp_verify(Request $request){
        $validator = validator($request->all(), [
            'email' => ['required', 'email'],
            'otp' => ['required'],
        ], [
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'otp.required' => 'The OTP is required.',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        }
        
        $userdata = User::where('id', auth('sanctum')->user()->id)->first();

        if (!$userdata) {
            return Response::json(['status' => 'failed', 'message' => 'Incorrect Credentials.']);
        }
        if ($userdata['otp'] == $request->otp && $userdata['email_otp_verified'] == 0) {
            if ($userdata['otp_created_at'] < now()) {
                return Response::json(['status' => 'failed', 'message' => 'OTP Expired']);
            } else {
                User::where('id', auth('sanctum')->user()->id)->update(['otp' => null, 'otp_created_at' => null, 'email_otp_verified' => '1']);
                return Response::json(['status' => 'success', 'message' => 'OTP Verified']);
            }
        }else{
            return Response::json(['status' => 'failed', 'message' => 'OTP does not match']);
        }
    }

    public function logout_auth(Request $request){
        // auth('sanctum')->user()->tokens()->delete();
        $user = auth('sanctum')->user();
        if (!$user) {
            return Response::json(['status' => 'failed', 'message' => 'User is not authenticated.']);
        }
    
        DB::table('device_tokens')->where([
            'user_id' => $user->id,
            'device_token' => $request->fcm_token
        ])->delete();
    
        $tokenString = $request->bearerToken();
        
        if ($tokenString) {
            $token = PersonalAccessToken::findToken($tokenString);
            if ($token) {
                $token->delete();
                return Response::json(['status' => 'success', 'message' => 'You have successfully logged out of NEXA. We hope to see you back soon!']);
            }
        }
    
        return Response::json(['status' => 'failed', 'message' => 'Logout failed.']);
    }
    
    public function socialite_login(Request $request) {
        // $validator = Validator::make($request->all(), [
        //     'socailite_type' => 'required',
        //     'socailite_id' => 'required',
        //     'first_name' => 'required',
        //     'email' => 'required|email',
        //     'fcm_token' => 'required',
        // ]);
    
        // if ($validator->fails()) {
        //     return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        // }
    
        if ($request->socailite_type === 'apple') {
            $data = User::where('email', '=', $request->email)->orWhere('apple_id', '=', $request->socailite_id)->first();
        } else {
            $data = User::where('email', $request->email)->first();
        }

        if ($data) {
            if ($data->status == 1) {
                switch ($request->socailite_type) {
                    case 'apple':
                        if ($data->apple_id === $request->socailite_id) {
                            // DB::table('device_tokens')->updateOrInsert(['user_id' => $data->id, 'device_token' => $request->fcm_token]);
                            $tokenData = $data->createToken($data->email);
                            $token = $tokenData->plainTextToken;
                            $tokenId = $tokenData->accessToken->id;
                
                            DB::table('device_tokens')->updateOrInsert(
                                ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
                            );

                            return response()->json(['status' => 'success', 'message' => 'Sign In With apple Successfully', 'token' => $token, 'userdata' => $data]);
                        } else {
                            $data->apple_id = $request->socailite_id;
                        }
                        break;
                    case 'google':
                        if ($data->google_id === $request->socailite_id) {
                            $tokenData = $data->createToken($request->email);
                            $token = $tokenData->plainTextToken;
                            $tokenId = $tokenData->accessToken->id;
                
                            DB::table('device_tokens')->updateOrInsert(
                                ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
                            );
                            return response()->json(['status' => 'success', 'message' => 'Sign In with Google Successfully', 'token' => $token, 'userdata' => $data]);
                        } else {
                            $data->google_id = $request->socailite_id;
                        }
                        break;
    
                    case 'facebook':
                        if ($data->facebook_id === $request->socailite_id) {
                            $tokenData = $data->createToken($request->email);
                            $token = $tokenData->plainTextToken;
                            $tokenId = $tokenData->accessToken->id;
                
                            DB::table('device_tokens')->updateOrInsert(
                                ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
                            );
                            return response()->json(['status' => 'success', 'message' => 'Sign In with Facebook Successfully', 'token' => $token, 'userdata' => $data]);
                        } else {
                            $data->facebook_id = $request->socailite_id;
                        }
                        break;
                }
                if ($data->save()) {
                    // DB::table('device_tokens')->updateOrInsert(['user_id' => $data->id, 'device_token' => $request->fcm_token]);
                    $tokenData = $data->createToken($request->email);
                    $token = $tokenData->plainTextToken;
                    $tokenId = $tokenData->accessToken->id;
        
                    DB::table('device_tokens')->updateOrInsert(
                        ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
                    );

                    return response()->json(['status' => 'success', 'message' => 'Socialite ID updated and Sign up successful.', 'token' => $token, 'userdata' => $data]);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'Failed to update socialite ID.', 'token' => null, 'userdata' => null]);
                }
            } else {
                return response()->json(['status' => 'failed', 'message' => 'User account is inactive', 'token' => null, 'userdata' => null]);
            }
        } else {
            $fullName = $request->first_name;
            // Split the name by spaces
            $nameParts = explode(' ', $fullName);

            // The first part will be the first name
            $firstName = array_shift($nameParts); // First word as first name

            // The remaining parts will be the last name
            $lastName = implode(' ', $nameParts); // Join remaining words as last name

            $adduser = new User;
            switch ($request->socailite_type) {
                case 'google':
                    $adduser->google_id = $request->socailite_id;
                    break;
                case 'facebook':
                    $adduser->facebook_id = $request->socailite_id;
                    break;
                case 'apple':
                    $adduser->apple_id = $request->socailite_id;
                    break;
            }
            $adduser->first_name = $firstName;
            $adduser->last_name = $lastName; 
            $adduser->email = $request->email;
            $adduser->password = Hash::make(Str::before($request->email, '@'));
            $adduser->status = "1";
            $adduser->current_steps = 'step_2';
            $adduser->save();
    
            // DB::table('device_tokens')->updateOrInsert(
            //     ['user_id' => $adduser->id, 'device_token' => $request->fcm_token]
            // );

            $tokenData = $adduser->createToken($request->email);
            $token = $tokenData->plainTextToken;
            $tokenId = $tokenData->accessToken->id;

            DB::table('device_tokens')->updateOrInsert(
                ['user_id' => $adduser->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
            );
    
            return response()->json(['status' => 'success', 'message' => 'Sign up with Socialite Successfully', 'token' => $token, 'userdata' => $adduser]);
        }
    }
    
}