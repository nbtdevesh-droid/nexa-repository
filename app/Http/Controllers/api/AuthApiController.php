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

        $user = User::withTrashed()->where('email', $request->credentials)->first();

        if (!$user) {
            return response()->json(['status' => 'failed', 'message' => 'These credentials do not match our records.'], 401);
        }
    
        if ($user->trashed()) {
            if ($user->delete_by === 'admin') {
                return response()->json(['status' => 'failed', 'message' => 'Your account has been deactivated by an administrator. Please contact support for assistance.'], 403);
            } else {
                return response()->json(['status' => 'failed', 'message' => 'Your account is deactivated. Please register to reactivate your account.'], 403);
            }
        }
    
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
        $user = User::withTrashed()->where('phone', $request->credentials)->where('country_code', $request->country_code)->first();

        if ($user) {
            if ($user->trashed()) {
                if ($user->delete_by === 'admin') {
                    return response()->json(['status' => 'failed', 'message' => 'Your account has been deactivated by an administrator. Please contact support for assistance.'], 403);
                } else {
                    return response()->json(['status' => 'failed', 'message' => 'Your account is deactivated. Please register to reactivate your account.'], 403);
                }
            }
    
            return response()->json(['status' => 'failed', 'message' => 'The credentials have already been taken.']);
        }
    
        return response()->json(['status' => 'success', 'message' => 'Customer does not exist']);
    }

    public function customerRegisterOrLogin(Request $request)
    {
        $user = User::withTrashed()->where('phone', $request->credentials)->where('country_code', $request->country_code)->first();

        // If user exists and is soft-deleted → Reactivate
        if ($user && $user->trashed()) {
            if ($user->delete_by === 'admin') {
                return response()->json(['status' => 'failed', 'message' => 'Your account has been deactivated by an administrator. Please contact support.'], 403);
            }
            $user->restore();
            $user->status = 1;
            $user->current_steps = 'step_2';
            $user->delete_by = null;
            $user->save();

            $tokenData = $user->createToken($request->credentials);
            $token = $tokenData->plainTextToken;
            $tokenId = $tokenData->accessToken->id;

            DB::table('device_tokens')->updateOrInsert(
                ['user_id' => $user->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
            );

            $profile = $this->formatUserProfile($user);

            return response()->json(['status' => 'success', 'message' => 'Account reactivated successfully!', 'token' => $token, 'token_id' => $user->id, 'userdata' => $profile]);
        }

         // If user exists and is active
        if ($user) {
            if ($user->status != '1') {
                return response()->json(['status' => 'failed', 'message' => 'Customer is blocked', 'token' => null, 'token_id' => null, 'userdata' => null]);
            }

            $tokenData = $user->createToken($request->credentials);
            $token = $tokenData->plainTextToken;
            $tokenId = $tokenData->accessToken->id;

            DB::table('device_tokens')->updateOrInsert(
                ['user_id' => $user->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
            );

            $profile = $this->formatUserProfile($user);

            return response()->json(['status' => 'success', 'message' => 'Login Successfully', 'token' => $token, 'token_id' => $user->id, 'userdata' => $profile]);
        }

        // If no user found → Create new account
        $newUser = new User();
        $newUser->phone = $request->credentials;
        $newUser->country_code = $request->country_code;
        $newUser->status = 1;
        $newUser->current_steps = 'step_1';
        $newUser->save();

        $tokenData = $newUser->createToken($request->credentials);
        $token = $tokenData->plainTextToken;
        $tokenId = $tokenData->accessToken->id;

        DB::table('device_tokens')->updateOrInsert(
            ['user_id' => $newUser->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
        );

        $profile = $this->formatUserProfile($newUser);

        return response()->json(['status' => 'success', 'message' => 'Sign up Successfully', 'token' => $token, 'token_id' => $newUser->id, 'userdata' => $profile]);
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
    
    // public function socialite_login(Request $request) {
    //     // $validator = Validator::make($request->all(), [
    //     //     'socailite_type' => 'required',
    //     //     'socailite_id' => 'required',
    //     //     'first_name' => 'required',
    //     //     'email' => 'required|email',
    //     //     'fcm_token' => 'required',
    //     // ]);
    
    //     // if ($validator->fails()) {
    //     //     return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
    //     // }
    
    //     // if ($request->socailite_type === 'apple') {
    //     //     if($request->email){
    //     //         $data = User::Where('apple_id', '=', $request->socailite_id)->orWhere('email', $request->email)->first();
    //     //     }else{
    //     //         $data = User::Where('apple_id', '=', $request->socailite_id)->first();
    //     //     }
    //     // } else {
    //     //     $data = User::where('email', $request->email)->first();
    //     // }
    //     $query = User::withTrashed();  // Include soft-deleted users in the query
    //     if ($request->socailite_type === 'apple') {
    //         $request->email ? $query->where('email', $request->email)->orWhere('apple_id', $request->socailite_id) : $query->where('apple_id', $request->socailite_id);
    //     } else {
    //         $query->where('email', $request->email);
    //     }
    
    //     $data = $query->first();

    //     if ($data) {
    //         if ($data->status == 1) {
    //             switch ($request->socailite_type) {
    //                 case 'apple':
    //                     if ($data->apple_id === $request->socailite_id) {

    //                         // DB::table('device_tokens')->updateOrInsert(['user_id' => $data->id, 'device_token' => $request->fcm_token]);
    //                         $tokenData = $data->createToken($data->email);
    //                         $token = $tokenData->plainTextToken;
    //                         $tokenId = $tokenData->accessToken->id;
                
    //                         DB::table('device_tokens')->updateOrInsert(
    //                             ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
    //                         );

    //                         return response()->json(['status' => 'success', 'message' => 'Sign In With apple Successfully', 'token' => $token, 'userdata' => $data]);
    //                     } else {
    //                         $data->apple_id = $request->socailite_id;
    //                     }
    //                     break;
    //                 case 'google':
    //                     if ($data->google_id === $request->socailite_id) {
    //                         $tokenData = $data->createToken($request->email);
    //                         $token = $tokenData->plainTextToken;
    //                         $tokenId = $tokenData->accessToken->id;
                
    //                         DB::table('device_tokens')->updateOrInsert(
    //                             ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
    //                         );
    //                         return response()->json(['status' => 'success', 'message' => 'Sign In with Google Successfully', 'token' => $token, 'userdata' => $data]);
    //                     } else {
    //                         $data->google_id = $request->socailite_id;
    //                     }
    //                     break;
    
    //                 case 'facebook':
    //                     if ($data->facebook_id === $request->socailite_id) {
    //                         $tokenData = $data->createToken($request->email);
    //                         $token = $tokenData->plainTextToken;
    //                         $tokenId = $tokenData->accessToken->id;
                
    //                         DB::table('device_tokens')->updateOrInsert(
    //                             ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
    //                         );
    //                         return response()->json(['status' => 'success', 'message' => 'Sign In with Facebook Successfully', 'token' => $token, 'userdata' => $data]);
    //                     } else {
    //                         $data->facebook_id = $request->socailite_id;
    //                     }
    //                     break;
    //             }
    //             if ($data->save()) {
    //                 $tokenData = $data->createToken($request->email);
    //                 $token = $tokenData->plainTextToken;
    //                 $tokenId = $tokenData->accessToken->id;
        
    //                 DB::table('device_tokens')->updateOrInsert(
    //                     ['user_id' => $data->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
    //                 );

    //                 return response()->json(['status' => 'success', 'message' => 'Socialite ID updated and Sign up successful.', 'token' => $token, 'userdata' => $data]);
    //             } else {
    //                 return response()->json(['status' => 'failed', 'message' => 'Failed to update socialite ID.', 'token' => null, 'userdata' => null]);
    //             }
    //         } else {
    //             return response()->json(['status' => 'failed', 'message' => 'User account is inactive', 'token' => null, 'userdata' => null]);
    //         }
    //     } else {
    //         $user = User::where('email', $request->email)->first();

    //         if($user)
    //         {

    //         }else{
    //             $fullName = $request->first_name;
    //             // Split the name by spaces
    //             $nameParts = explode(' ', $fullName);
    
    //             // The first part will be the first name
    //             $firstName = array_shift($nameParts); // First word as first name
    
    //             // The remaining parts will be the last name
    //             $lastName = implode(' ', $nameParts); // Join remaining words as last name
    
    //             $adduser = new User;
    //             switch ($request->socailite_type) {
    //                 case 'google':
    //                     $adduser->google_id = $request->socailite_id;
    //                     break;
    //                 case 'facebook':
    //                     $adduser->facebook_id = $request->socailite_id;
    //                     break;
    //                 case 'apple':
    //                     $adduser->apple_id = $request->socailite_id;
    //                     break;
    //             }
    //             $adduser->first_name = $firstName;
    //             $adduser->last_name = $lastName; 
    //             $adduser->email = $request->email;
    //             $adduser->password = Hash::make(Str::before($request->email, '@'));
    //             $adduser->status = "1";
    //             $adduser->current_steps = 'step_2';
    //             $adduser->save();
        
    //             // DB::table('device_tokens')->updateOrInsert(
    //             //     ['user_id' => $adduser->id, 'device_token' => $request->fcm_token]
    //             // );
    
    //             $tokenData = $adduser->createToken($request->email);
    //             $token = $tokenData->plainTextToken;
    //             $tokenId = $tokenData->accessToken->id;
    
    //             DB::table('device_tokens')->updateOrInsert(
    //                 ['user_id' => $adduser->id, 'device_token' => $request->fcm_token, 'token_id' => $tokenId]
    //             );
        
    //             return response()->json(['status' => 'success', 'message' => 'Sign up with Socialite Successfully', 'token' => $token, 'userdata' => $adduser]);
    //         }
    //     }
    // }

    public function socialite_login(Request $request)
    {
        $socialiteType = $request->socailite_type;
        $socialiteId   = $request->socailite_id;
    
        // Check for user based on socialite ID or email
        $query = User::withTrashed();  // Include soft-deleted users in the query
        if ($socialiteType === 'apple') {
            // $query->where('email', $request->email);
            $request->email ? $query->where('email', $request->email)->orWhere('apple_id', $socialiteId) : $query->where('apple_id', $socialiteId);
        } else {
            $query->where('email', $request->email);
        }
    
        $user = $query->first();
        // dd($user);
        if ($user != null) {
            // Handle soft-deleted users
            if ($user->trashed()) {
                if ($user->delete_by === 'admin') {
                    return response()->json(['status' => 'failed', 'message' => 'Your account has been deactivated by an administrator. Please contact support for assistance.', 'token' => null, 'userdata' => null ]);
                } else {
                    // Reactivate the soft-deleted user
                    $user->restore();
                    $user->status = 1;
                    $user->delete_by = null;
                    $user->save();
                }
            }
    
            if ($user->status != 1) {
                return response()->json(['status' => 'failed', 'message' => 'User account is inactive', 'token' => null, 'userdata' => null ]);
            }
    
            $socialiteColumn = $socialiteType . '_id';
    
            if ($user->$socialiteColumn === $socialiteId) {
                return $this->generateTokenResponse($user, "Sign in with {$socialiteType} successful", $request->fcm_token);
            }
    
            // Update socialite ID if it wasn't matched
            $user->$socialiteColumn = $socialiteId;
            $user->save();
    
            return $this->generateTokenResponse($user, 'Socialite ID updated and signed in successfully.', $request->fcm_token);
        }
    
        // No user found, create new
        $nameParts = explode(' ', $request->first_name);
        $firstName = array_shift($nameParts);
        $lastName = implode(' ', $nameParts);
    
        $newUser = new User();
        $newUser->first_name = $firstName;
        $newUser->last_name = $lastName;
        $newUser->email = $request->email;
        $newUser->status = 1;
        $newUser->password = Hash::make(Str::before($request->email, '@'));
        $newUser->current_steps = 'step_2';
        $newUser->{$socialiteType . '_id'} = $socialiteId;
        $newUser->save();
    
        // Generate the token using a valid string (e.g., user's email or a custom string)
        return $this->generateTokenResponse($newUser, 'Signed up with Socialite successfully', $request->fcm_token);
    }
    

    protected function generateTokenResponse($user, $message, $fcmToken)
    {
        // Generate token
        // if($socialiteType == 'apple'){
        //     // Use a fallback string if email is null
        //     $tokenName = $user->email ?? ('user_' . $user->id);

            // Generate token
            // $tokenData = $user->createToken($tokenName);
        // }else{

            $tokenData = $user->createToken($user->email); // Use the email as the name
        // }
        $token = $tokenData->plainTextToken;
        $tokenId = $tokenData->accessToken->id;
    
        // Store device token (if needed)
        DB::table('device_tokens')->updateOrInsert(
            ['user_id' => $user->id, 'device_token' => $fcmToken, 'token_id' => $tokenId]
        );
    
        $user->image = !empty($user->image) ? asset('admin-assets/assets/img/profile_img/user/' . $user->image) : asset('admin-assets/assets/img/profile_img/user/common.png');
        return response()->json(['status' => 'success', 'message' => $message, 'token' => $token, 'userdata' => $user]);
    }
    

    private function formatUserProfile($user)
    {
        $profile = User::select('id', 'first_name','last_name', 'email', 'country', 'country_code', 'phone', 'image', 'current_steps')->where('id', $user->id)->first();

        $profile->image = !empty($profile->image) ? asset('admin-assets/assets/img/profile_img/user/' . $profile->image) : asset('admin-assets/assets/img/profile_img/user/common.png');

        return $profile;
    }
    
}