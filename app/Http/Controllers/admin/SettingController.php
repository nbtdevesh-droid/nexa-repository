<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Google_Client;

class SettingController extends Controller
{
    public function flash_deal()
    {
        $deals = DB::table('product_flash_deals')->orderBy('id', 'desc')->paginate(10);
        // Artisan::call('optimize:clear');
        return view('admin.setting.flash_deal', compact('deals'));
    }
   
    public function add_flash_deal(Request $request)
    {
        return view('admin.setting.add_flash_deal');
    }

    public function store_flash_deal(Request $request)
    {
        $validatedData = $request->validate([
            'start_at' => 'required|date',
            'expire_at' => [
                'required',
                'date',
                'after_or_equal:start_at',
                function ($attribute, $value, $fail) use ($request) {
                    $startDateTime = $request->start_at;
                    $endDateTime = $value;

                    $exists = DB::table('product_flash_deals')
                        ->where(function ($query) use ($startDateTime, $endDateTime) {
                            $query->whereBetween('start_flash_deal', [$startDateTime, $endDateTime])
                                ->orWhereBetween('end_flash_deal', [$startDateTime, $endDateTime])
                                ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                                    $query->where('start_flash_deal', '<=', $startDateTime)
                                        ->where('end_flash_deal', '>=', $endDateTime);
                                });
                        })
                        ->exists();

                    if ($exists) {
                        $fail('The date and time overlaps with an existing record.');
                    }
                }
            ],
            'quantity' => 'required|integer|min:1',
        ], [
            'start_at.required' => 'The start date and time field is required.',
            'start_at.date' => 'The start date and time must be a valid date.',
            'expire_at.required' => 'The expire date and time field is required.',
            'expire_at.date' => 'The expire date and time must be a valid date.',
            'expire_at.after_or_equal' => 'The expire date and time must be after or equal to the start date and time.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be at least 1.',
        ]);



        $data = DB::table('product_flash_deals')->insert([
            'start_flash_deal' => $request->start_at ? date('Y-m-d H:i:s', strtotime($request->start_at)) : null,
            'end_flash_deal' => $request->expire_at ? date('Y-m-d H:i:s', strtotime($request->expire_at)) : null,
            'quantity' => $request->quantity

        ]);


        if ($data === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No changes were made to the flash deal. Please check the input values.'
            ]);
        }
        return redirect()->route('setting.flash_deal')->with('success', 'Flash Deal added successfully!');
    }

    public function edit_flash_deal($id)
    {
        $flashDeal = DB::table('product_flash_deals')->where('id', $id)->first();
        return view('admin.setting.edit_flash_deal', compact('flashDeal'));
    }

    public function setting_update_flash_deal(Request $request, $id)
    {
        // Get the current flash deal record
        $flashDeal = DB::table('product_flash_deals')->where('id', $id)->first();

        // Add validation, including the custom overlap check
        $validatedData = $request->validate([
            'start_at' => 'required|date',
            'expire_at' => [
                'required',
                'date',
                'after_or_equal:start_at',
                function ($attribute, $value, $fail) use ($request, $flashDeal) {
                    $startDateTime = $request->start_at;
                    $endDateTime = $value;

                    // Check for overlap, excluding the current record being updated
                    $exists = DB::table('product_flash_deals')
                        ->where('id', '!=', $flashDeal->id) // Exclude the current record
                        ->where(function ($query) use ($startDateTime, $endDateTime) {
                            $query->whereBetween('start_flash_deal', [$startDateTime, $endDateTime])
                                ->orWhereBetween('end_flash_deal', [$startDateTime, $endDateTime])
                                ->orWhere(function ($query) use ($startDateTime, $endDateTime) {
                                    $query->where('start_flash_deal', '<=', $startDateTime)
                                        ->where('end_flash_deal', '>=', $endDateTime);
                                });
                        })
                        ->exists();

                    if ($exists) {
                        $fail('The date and time overlaps with an existing record.');
                    }
                }
            ],
            'quantity' => 'required|integer|min:1',
        ], [
            'start_at.required' => 'The start date and time field is required.',
            'start_at.date' => 'The start date and time must be a valid date.',
            'expire_at.required' => 'The expire date and time field is required.',
            'expire_at.date' => 'The expire date and time must be a valid date.',
            'expire_at.after_or_equal' => 'The expire date and time must be after or equal to the start date and time.',
            'quantity.required' => 'The quantity field is required.',
            'quantity.integer' => 'The quantity must be a valid number.',
            'quantity.min' => 'The quantity must be at least 1.',
        ]);

        // Update the flash deal record
        DB::table('product_flash_deals')->where('id', $id)->update([
            'start_flash_deal' => $request->start_at ? date('Y-m-d H:i:s', strtotime($request->start_at)) : null,
            'end_flash_deal' => $request->expire_at ? date('Y-m-d H:i:s', strtotime($request->expire_at)) : null,
            'quantity' => $request->quantity
        ]);

        return redirect()->route('setting.flash_deal')->with('success', 'Flash Deal updated successfully!');
    }

    public function destroy_flash_deal($id)
    {
        DB::table('product_flash_deals')->where('id', $id)->delete();
        return redirect()->route('setting.flash_deal')->with('success', 'Flash Deal deleted successfully!');
    }

    public function shipping_charges()
    {
        $deals = DB::table('product_flash_deals')->orderBy('id', 'desc')->paginate(10);
        // Artisan::call('optimize:clear');
        $shipping_data = DB::table('shipping_charges')->where('id', '1')->first();

        return view('admin.setting.add_shipping_charge', compact('shipping_data'));
    }
    
    public function setting_update_shipping_charges(Request $request)
    {
        // Add validation, including the custom overlap check
        $validatedData = $request->validate([
            'shipping_amount' => 'required|integer|',
            'after_charges' => 'required|integer|',
        ], [
            'shipping_amount.required' => 'The shipping amount field is required.',
            'after_charges.date' => 'The shipping amount after charges field is required.',
        ]);

        // Update the flash deal record
        DB::table('shipping_charges')->where('id', '1')->update([
            'shipping_amount' => $request->shipping_amount,
            'after_charges' => $request->after_charges,
        ]);

        return redirect()->route('setting.shipping_charges')->with('success', 'Shipping Charges updated successfully!');
    }

    /***************************************** app notification setting ********************************/
    public function app_setting()
    {
        $settings = DB::table('app_settings')->first();
        return view('admin.setting.app_notification_setting', compact('settings'));
    }

    public function app_setting_update(Request $request)
    {
        $app_setting = DB::table('app_settings')->where('id', 1)->first();

        // Send Firebase Notifications
        if ($request->maintenance_setting == 1 && $app_setting->maintenance_setting == 0) {
            $this->sendFirebaseNotification('Maintenance Mode', 'We are currently experiencing some technical difficulties. Please check back shortly.');
        } elseif ($request->maintenance_setting == 0 && $app_setting->maintenance_setting == 1) {
            $this->sendFirebaseNotification('Maintenance Mode Off', 'We are back online. All systems are now operational.');
        }

        if ($request->update_setting == 1 && $app_setting->update_setting == 0) {
            $this->sendFirebaseNotification('New Update Available!', "We've released a new update with exciting features and improvements. Update now for the best experience!");
        } elseif ($request->update_setting == 0 && $app_setting->update_setting == 1) {
            $this->sendFirebaseNotification('Great News!', 'You are now using the latest stable version.');
        }

        // Update settings in the database
        DB::table('app_settings')->updateOrInsert(
            ['id' => 1],
            [
                'maintenance_setting' => $request->maintenance_setting,
                'update_setting' => $request->update_setting,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Notification settings updated successfully.');
    }

    public function sendFirebaseNotification($title, $message)
    {
        $credentialsFilePath = public_path() . "/firebase/nexa-f8388-a720f21d80be.json";

        // Initialize the Google Client
        $client = new Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Set Firebase API endpoint
        $apiurl = 'https://fcm.googleapis.com/v1/projects/nexa-f8388/messages:send';

        // Get the access token
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $access_token = $token['access_token'];

        // Set headers
        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $datas = [
            'route' => 'app-setting',
        ];

        // Construct the payload for topic messaging
        $notification = [
            "message" => [
                "topic" => "NEXA", // Topic name
                "notification" => [
                    "title" => $title,
                    "body" => $message
                ],
                "data" => array_map('strval', $datas),
            ],
        ];

        // Encode payload to JSON
        $payload = json_encode($notification);

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiurl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Execute and handle response
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // if ($err) {
        //     return response()->json([
        //         'status' => 'failed',
        //         'message' => 'Curl Error: ' . $err
        //     ]);
        // }

        // return response()->json([
        //     'status' => 'success',
        //     'http_code' => $httpCode,
        //     'response' => json_decode($response, true)
        // ]);

        if ($httpCode === 200) {
            return redirect()->back()->with('flash-success', 'Notification sent successfully!');
        }else{
            dd($response);
        }
    }
}
