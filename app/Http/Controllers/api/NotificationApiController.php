<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Staff;
use App\Models\User;
use Google_Client;


class NotificationApiController extends Controller
{
    public function sendPushNotification($device_tokens, $notify_data)
    {
        $credentialsFilePath = public_path() . "/firebase/nexa-f8388-a720f21d80be.json";
        $client = new Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $apiurl = 'https://fcm.googleapis.com/v1/projects/nexa-f8388/messages:send';
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $responses = [];
        $tokensToDelete = [];

        foreach ($device_tokens as $device_token) {
            // Construct notification payload for each device token
            $notification = [
                "message" => [
                    "token" => $device_token, // Single token
                    "notification" => [
                        "title" => $notify_data['title'],
                        "body" => $notify_data['body']
                    ],
                    "android" => [
                        "priority" => "high"
                    ],
                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "alert" => [
                                    "title" => $notify_data['title'],
                                    "body" => $notify_data['body']
                                ],
                                "sound" => "default"
                            ]
                        ]
                    ]
                ]
            ];

            // Encode the payload into JSON format
            $payload = json_encode($notification);

            // Set up cURL request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiurl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            // Execute the request and get response
            $response = curl_exec($ch);
            // dd($response);

            $err = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode == 404) {
                // Decode JSON response to get error details
                $responseArray = json_decode($response, true);
                $errorStatus = $responseArray['error']['status'] ?? null;
                $errorMessage = $responseArray['error']['message'] ?? 'Unknown error';
        
                if ($errorStatus == 'NOT_FOUND') {
                    $tokensToDelete[] = $device_token;
                }
            }
            if ($err) {
                $responses[] = [
                    'device_token' => $device_token,
                    'status' => 'failed',
                    'message' => 'Curl Error: ' . $err
                ];
                // return 0;
            } else {
                $responses[] = [
                    'device_token' => $device_token,
                    'status' => 'success',
                    'response' => json_decode($response, true)
                ];
                // return 1;
            }
        }

        // Return all responses for debugging or logging
        if (!empty($tokensToDelete)) {
            DB::table('device_tokens')->whereIn('device_token', $tokensToDelete)->delete();
        }
    
        return response()->json([
            'message' => 'Notifications processed',
            'responses' => $responses
        ]);
    }

    public function AddNotification($notify_data = null, $order_id = null, $staff_member_id = null, $notification = null){
        
        if($notification == 0){
            DB::table('notification_list')->insert(
                [
                    'user_id' => $notify_data['user_id'],
                    'about' => $notify_data['title'],
                    'content' => $notify_data['body'],
                    'order_status' => 'pending',
                ]
            );
        }
        DB::table('other_notification_list')->insert(
            [
                'user_id' => $notify_data['user_id'],
                'other_recive_notification_id' => $staff_member_id,
                'other_recive_about' => 'New Order Recive : ID '. $order_id,
                'other_recive_notification_content' => 'New order recived successfully',
                'order_status' => 'pending',
            ]
        );
    }

    public function ReciveAddNotification($notify_data = null, $orders = null, $staff_member_id = null, $order_status = null){
        if (Auth::guard('web')->check()) {
            $other_id = Auth::guard('web')->user()->id;
        } elseif (Auth::guard('member')->check()) {
            $other_id = Auth::guard('member')->user()->id;
        }else{
            $other_id = null;
        }

        if(auth('sanctum')->user()->notification_status == 0){
            DB::table('notification_list')->insert(
                [
                    'user_id' => $notify_data['user_id'],
                    'other_id' => $other_id,
                    'about' => $notify_data['title'],
                    'content' => $notify_data['body'],
                    'order_status' => $order_status,
                ]
            );
        }

        DB::table('other_notification_list')->insert(
            [
                'user_id' => $notify_data['user_id'],
                'other_recive_notification_id' => $staff_member_id,
                'other_recive_about' => 'Order ' . $order_status . ' : ID '. $orders->order_id,
                'other_recive_notification_content' => 'Order ' . $order_status . ' successfully',
                'order_status' => $order_status,
            ]
        );

        $user = User::where('id', $notify_data['user_id'])->first();
        /************ Customer Mail **************/
        if($order_status == 'confirm'){
            $subject = 'Your Order Confirmation - ' . $orders->order_id;
        }elseif($order_status == 'processing'){
            $subject = 'Your Order is Being Processed - ' . $orders->order_id;
        }elseif($order_status == 'dispatch'){
            $subject = 'Your Order Has Been Dispatched - ' . $orders->order_id;
        }elseif($order_status == 'delivered'){
            $subject = 'Your Order Has Been Delivered - ' . $orders->order_id;
        }elseif($order_status == 'complete'){
            $subject = 'Your Order Has Been Completed - ' . $orders->order_id;
        }elseif($order_status == 'cancelled'){
            $subject = 'Your Order Has Been Cancelled - ' . $orders->order_id;
        }elseif($order_status == 'return'){
            $subject = 'Your Order Has Been Returned - ' . $orders->order_id;
        }

        if($order_status != 'refund'){
            Mail::send('admin.email.OrderChangeStatus', ['order' => $orders, 'user' => $user], function ($message) use ($user, $subject) {
                $message->to($user->email);
                $message->subject($subject);
            });
        }else{
            Mail::send('admin.email.refund', ['order' => $orders, 'user' => $user], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Refund Processed Successfully');
            });
        }

        /*********** Admin Mail *****************/
        if($order_status == 'confirm'){
            $subject1 = 'New Order Confirmation - ' . $orders->order_id;
        }elseif($order_status == 'processing'){
            $subject1 = 'Order Processing - ' . $orders->order_id;
        }elseif($order_status == 'dispatch'){
            $subject1 = 'Order Dispatched - ' . $orders->order_id;
        }elseif($order_status == 'delivered'){
            $subject1 = 'Order Delivered - ' . $orders->order_id;
        }elseif($order_status == 'complete'){
            $subject1 = 'Order Completed - ' . $orders->order_id;
        }elseif($order_status == 'cancelled'){
            $subject1 = 'Order Cancelled - ' . $orders->order_id;
        }elseif($order_status == 'return'){
            $subject1 = 'Order Returned - ' . $orders->order_id;
        }elseif($order_status == 'refund'){
            $subject1 = 'Refund Processed Successfully';
        }
        $fullname = $user->first_name . ' ' . $user->last_name;

        if($order_status != 'refund'){
            Mail::send('admin.email.OrderStatusSendMailAdmin', ['order' => $orders, 'user' => $fullname], function ($message) use ($subject1) {
                $message->to('info@nexamarket.app');
                $message->subject($subject1);
            });
        }else{
            Mail::send('admin.email.RefundSendMailAdmin', ['order' => $orders, 'user' => $fullname], function ($message) use ($subject1) {
                $message->to('info@nexamarket.app');
                $message->subject($subject1);
            });
        }

        if($orders->staff_member_id){
            $staff_ids = json_decode($orders->staff_member_id, true);

            foreach($staff_ids as $staff_id){
                $staff = Staff::select('email')->where('id', $staff_id)->first();
                if ($staff && $staff->email) {
                    if($order_status != 'refund'){
                        Mail::send('admin.email.OrderStatusSendMailAdmin', ['order' => $orders, 'user' => $fullname], function ($message) use ($subject1, $staff) {
                            $message->to($staff->email);
                            $message->subject($subject1);
                        });
                    }else{
                        Mail::send('admin.email.RefundSendMailAdmin', ['order' => $orders, 'user' => $fullname], function ($message) use ($subject1, $staff) {
                            $message->to($staff->email);
                            $message->subject($subject1);
                        });
                    }
                }
            }
        }
    }

    public function view_notification()
    {
        $user_id = auth('sanctum')->user()->id;
        $notification_data = DB::table('notification_list')->select('id', 'about', 'content', 'created_at')->where('user_id', $user_id)->orderBy('id', 'desc')->get();

        if ($notification_data) {
            return response()->json(['status' => 'success', 'notification' => $notification_data]);
        }

        return response()->json(['status' => 'failed', 'notification' => []]);
    }

    public function delete_notification(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;

        $notification_ids = array_map('intval', explode(',', $request->input('notification_id')));
        $deleted_count = DB::table('notification_list')
            ->whereIn('id', $notification_ids)
            ->where('user_id', $user_id)
            ->delete();

        if ($deleted_count === 0) {
            return response()->json(['status' => 'failed', 'message' => 'No notification found.']);
        }

        return response()->json(['status' => 'success', 'message' => 'Notification(s) successfully deleted.']);
    }

}