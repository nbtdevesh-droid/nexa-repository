<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\api\NotificationApiController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public $order;

    public function __construct(Order $order)
    {
        $this->all_orders = $order;
    }

    public function index()
    {
        $isWebUser = Auth::guard('web')->check();
        $userId = $isWebUser ? null : Auth::guard('member')->user()->id;

        if ($isWebUser) {
            // Web user: Get all orders with pagination
            $orders = $this->all_orders->with('user')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            // Staff user: Filter and paginate manually
            $allOrders = $this->all_orders->with('user')->orderBy('created_at', 'desc')->get();

            $filteredOrders = $allOrders->filter(function ($order) use ($userId) {
                $productCompleteDetails = json_decode($order->product_complete_details, true) ?? [];
                return collect($productCompleteDetails)->contains('user_id', $userId);
            });

            $currentPage = Paginator::resolveCurrentPage();
            $perPage = 10;
            $currentItems = $filteredOrders->forPage($currentPage, $perPage);

            $orders = new LengthAwarePaginator(
                $currentItems,
                $filteredOrders->count(),
                $perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return view('admin.order.order_list', compact('orders'));
    }

    public function edit(string $id)
    {
        $data['orders'] = $this->all_orders->with('user')->find($id);
        return view('admin.order.order_view', $data);
    }

    function checkRegisterTrackingNumber($trackingNumber, $carrier_code)
    {
        $url = "https://api.17track.net/track/v2.2/register";
        // $token = '38C035713F2329B9858191F603D2BA55';
        $token = env('TRACK_SECURITY_KEY');

        $data = json_encode([
            [
                "number" => $trackingNumber,
                "carrier" => $carrier_code
            ]
        ]);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "17token: $token",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = 'Curl error: ' . curl_error($ch);
            curl_close($ch);
            return ['status' => 'error', 'message' => $error, 'carrier' => null];
        }

        curl_close($ch);

        $response = json_decode($response);

        if (!empty($response->data->rejected)) {
            return [
                'status' => 'error',
                'message' => $response->data->rejected[0]->error->message ?? 'Unknown error',
                'carrier' => null
            ];
        }

        if (!empty($response->data->accepted)) {
            return [
                'status' => 'success',
                'message' => 'Tracking number registered successfully',
                'carrier' => $response->data->accepted[0]->carrier ?? null
            ];
        }

        return ['status' => 'error', 'message' => 'Something went wrong', 'carrier' => null];
    }
    
    public function orderTrackingUpdate(Request $request, string $id)
    {
        if (empty(trim($request->shiping_date))) {
            return back()->with('info', 'Please fill order shiiping date before add tacking number.');
        }

        if ($request->order_status == 'pending') {
            return back()->with('info', 'Please change order status before add tacking number.');
        }

        if (empty(trim($request->tacking_number))) {
            return back()->with('info', 'Please fill all fields');
        }
        if (empty(trim($request->carrier_code))) {
            return back()->with('info', 'Please fill all fields');
        }
        $order = Order::where('id', $id)->first();
        if (!$order) {
            return back()->with('info', 'Order not found');
        }
        $register = $this->checkRegisterTrackingNumber($request->tacking_number, $request->carrier_code);
        if($register['status'] == 'error'){
            return back()->with('info', $register['message']);
        } 
        $order->tracking_number = $request->tacking_number; 
        // $order->tracking_carrier_code = $register['carrier']; 
        $order->tracking_carrier_code = $request->carrier_code; 
        $order->save();
        return back()->with('success', $register['message']);
      
    }

    public function update(Request $request, string $id)
    {
        $orders = $this->all_orders->find($id);

        if ($orders->order_status == $request->order_status) {
            return back()->with('info', 'Order status is already ' . $request->order_status . '. Please change order status.');
        }
        $data = $this->all_orders->update_status($request, $orders);

        if ($data != 1) {
            return back()->with('error', 'Status updated failed');
        }

        $device_token = DB::table('device_tokens')->where('user_id', $orders->user_id)->pluck('device_token')->toArray();
        $notification = User::where('id', $orders->user_id)->pluck('notification_status')->first();
        $notify_data = [
            'title' => 'Order ' . $request->order_status . ' - ' . $orders->order_id,
            'user_id' => $orders->user_id,
            'body' => 'your order ' . $request->order_status . ' successfully',
        ];

        $controller = new NotificationApiController();

        if ($notification == 0) {
            $data = $controller->sendPushNotification($device_token, $notify_data);
        }

        $controller->ReciveAddNotification($notify_data, $orders, $orders->staff_member_id, $request->order_status);

        return back()->withSuccess('Status updated Successfully');
    }

    public function generate_pdf($id)
    {
        $order_info = Order::where('id', $id)->first();
        $pdf = Pdf::loadView('admin.order.download_order_info', compact('order_info'));
        return $pdf->download('OrderInvoice.pdf');
    }

    public function showInvoice($orderId)
    {
        $order_info = Order::find($orderId);
        return view('admin.order.download_order_info', compact('order_info'));
    }

    public function refund(Request $request, string $orderId)
    {
        $order = Order::select('user_id', 'order_id', 'net_amount', 'order_status', 'transaction_id')->where('id', $orderId)->with('GetTransactionDetail')->first();
    
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    
        $user = User::find($order->user_id);
        $fullname = $user ? $user->first_name . ' ' . $user->last_name : 'Unknown User';
    
        $secretKey = env('PAYSTACK_SECRET');

        $auth =  Http::withHeaders([
            'Authorization' => 'Bearer ' . $secretKey,
            'Content-Type'  => 'application/json',
        ]);
    
        $transactionResponse = $auth->get("https://api.paystack.co/transaction/{$order->transaction_id}");
        $transactionData = $transactionResponse->json();

        if (!isset($transactionData['status']) || !$transactionData['status']) {
            return back()->with('error', 'Failed to fetch transaction details.');
        }

        // Extract `reference`
        $transactionReference = $transactionData['data']['reference'] ?? null;

        if (!$transactionReference) {
            return back()->with('error', 'Reference not found in transaction.');
        }
       
        $amount = $transactionData['data']['amount'] ?? 0;
        $refundResponse = $auth->post('https://api.paystack.co/refund', [
            'transaction' => $transactionReference,
            'amount'      => $amount, // No need to multiply
            'currency'    => 'NGN'
        ]);
    
        $refundData = $refundResponse->json();
    
        Log::info('Paystack Refund Response: ' . json_encode($refundData));

        if (!$refundData['status']) {
            return back()->with('error', 'Refund failed: ' . $refundData['message']);
        }
    
        Order::where('transaction_id', $order->transaction_id)->update(['order_status' => 'refund', 'shiping_date' => now()->format('Y-m-d')]);

        DB::table('transactions')->where('transaction_id', $order->transaction_id)->update(['payment_status' => 'refunded', 'refunded_details' => json_encode($refundData)]);
    
        Mail::send('admin.email.refund', ['order' => $order, 'user' => $fullname], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Your Refund Has Been Processed Successfully');
        });
    
        Mail::send('admin.email.AdminPaymentRefundMail', ['order' => $order, 'user' => $fullname], function ($message) use ($user) {
            $message->to('info@nexamarket.app');
            $message->subject('Refund Processed Successfully');
        });
        return back()->withSuccess('Refund successfully processed and email sent to customer.');
    }

    // public function refund(Request $request, string $orderId)
    // {
    //     $order = Order::select('user_id', 'order_id', 'order_status', 'transaction_id')->where('id', $orderId)->first();

    //     if (!$order) {
    //         return response()->json(['message' => 'Order not found'], 404);
    //     }
    //     $user = user::where('id', $order->user_id)->first();
    //     $fullname = $user->first_name . ' ' . $user->last_name;

    //     // $stripe = new StripeClient(env('STRIPE_SECRET'));

    //     // // try {
    //     // $details = $stripe->paymentIntents->retrieve($order->transaction_id, []);
    //     // $charge_id = $details->latest_charge;
    //     // $refund = $stripe->refunds->create(['charge' => $charge_id]);

    //     // if ($refund->status == 'succeeded') {
    //     //     Order::where('transaction_id', $order->transaction_id)->update(['order_status' => 'refund', 'shiping_date' => now()->format('Y-m-d')]);

    //     //     DB::table('transactions')
    //     //         ->where('transaction_id', $order->transaction_id)
    //     //         ->update(['payment_status' => 'refund', 'refunded_details' => json_encode($refund)]);

    //     //     Mail::send('admin.email.refund', ['order' => $order, 'user' => $fullname], function ($message) use ($user) {
    //     //         $message->to($user->email);
    //     //         $message->subject('Your Refund Has Been Processed Successfully');
    //     //     });

    //     //     Mail::send('admin.email.AdminPaymentRefundMail', ['order' => $order, 'user' => $fullname], function ($message) use ($user) {
    //     //         $message->to('info@nexamarket.app');
    //     //         $message->subject('Refund Processed Successfully');
    //     //     });

    //     //     return back()->withSuccess('Refund Successfully processed and email sent to customer.');
    //     // } else {
    //     //     return back()->with('error', 'Refund Failed.');
    //     // }
    //     // } catch (\Exception $e) {
    //     //     return back()->with('error', 'An error occurred: ' . $e->getMessage());
    //     // }
    // }

    public function export_order()
    {
        $orders = Order::with('user')->get();
        $headers = [
            'Order Number', 'Name', 'Address', 'Country', 'Province', 'City', 'Company',
            'Zip Code', 'E-mail', 'Phone Number', 'Product Amount', 'Shipping Charge', 'Total', 'Order Status',
        ];
    
        $response = new StreamedResponse(function () use ($orders, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
    
            foreach ($orders as $order) {
                $customer_data = $order->user;
    
                $customer_name = $customer_data ? ($customer_data->first_name . ' ' . $customer_data->last_name) : '';
                $customer_email = $customer_data->email ?? '';
                $customer_phone = $customer_data->phone ?? '';
    
                $data = $order->shiping_address_id ? json_decode($order->shiping_address_id, true) : [];
    
                $row = [
                    $order->order_id,
                    $customer_name,
                    $data['address'] ?? null,
                    $data['country'] ?? null,
                    $data['state'] ?? null,
                    $data['city'] ?? null,
                    null,
                    $data['zip_code'] ?? null,
                    $customer_email,
                    $customer_phone,
                    '₦ ' . (($order->net_amount ?? 0) - ($order->shipping_charges ?? 0)),
                    is_null($order->shipping_charges) ? null : '₦ ' . $order->shipping_charges,
                    '₦ ' . ($order->net_amount ?? 0),
                    $order->order_status,
                ];
    
                fputcsv($handle, $row);
            }
    
            fclose($handle);
        });
    
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="orders.csv"');
    
        return $response;
    }
}


