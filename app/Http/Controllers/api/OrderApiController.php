<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Country;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\WareHouse;
use App\Models\Staff;
use App\Models\Review;
use App\Models\ShippingAddress;
use App\Models\BankDetails;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;


class OrderApiController extends Controller
{
    public function country_list()
    {
        $country = Country::orderByRaw("CASE WHEN name = 'Philippines' THEN 0 ELSE 1 END")->orderBy('name', 'asc')->get();

        if ($country->isEmpty()) {
            return response()->json(['status' => 'failed', 'Country' => []]);
        }

        return response()->json(['status' => 'success', 'Country' => $country]);
    }

    public function state_list(Request $request)
    {
        $country_id = $request->country_id;
        if ($country_id) {
            $state = DB::table('states')->where('country_id', $country_id)->orderBy('name', 'asc')->get();

            if (!$state) {
                return Response::json(['status' => 'failed', 'states' => []]);
            }
            return Response::json(['status' => 'success', 'states' => $state]);
        }
    }

    public function city_list(Request $request)
    {
        $state_id = $request->state_id;

        if ($state_id) {
            $city = DB::table('cities')->where('state_id', $state_id)->orderBy('name', 'asc')->get();

            if (!$city) {
                return Response::json(['status' => 'failed', 'cities' => []]);
            }
            return Response::json(['status' => 'success', 'cities' => $city]);
        }
    }

    public function add_update_shipping_address(Request $request)
    {
        $user = auth('sanctum')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'country' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'country_code' => 'required',
            'phone' => 'required',
            'primary_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'  => 'failed', 'message' => $validator->errors()->first()]);
        } else {
            if ($request->primary_address == 1) {
                ShippingAddress::where('user_id', $user->id)->update(['primary_address' => 0]);
            }
            if ($request->address_id != "") {
                $address = ShippingAddress::find($request->address_id);

                if (!$address) {
                    return Response::json(['status' => 'failed', 'message' => 'Address id does not exist']);
                }

                $address->user_id = $user->id;
                $address->name = $request->name;
                $address->address = $request->address;
                $address->city = $request->city;
                $address->country = $request->country;
                $address->state = $request->state;
                $address->zip_code = $request->zip_code;
                $address->country_code = $request->country_code;
                $address->phone = $request->phone;
                $address->primary_address = $request->primary_address;
                $address->update();

                return Response::json(['status' => 'success', 'message' => 'Shipping address update successfully']);
            } else {
                $address = new ShippingAddress();
                $address->user_id = $user->id;
                $address->name = $request->name;
                $address->address = $request->address;
                $address->city = $request->city;
                $address->country = $request->country;
                $address->state = $request->state;
                $address->zip_code = $request->zip_code;
                $address->country_code = $request->country_code;
                $address->phone = $request->phone;
                $address->primary_address = $request->primary_address;
                $address->save();

                return Response::json(['status' => 'success', 'message' => 'Shipping address add successfully']);
            }
        }
    }

    public function all_shipping_address()
    {
        $user_id = auth('sanctum')->user()->id;
        $addresss = ShippingAddress::where('user_id', $user_id)->get();
        if (!$addresss) {
            return Response::json(['status' => 'failed', 'ShippingAddress' => []]);
        }

        foreach ($addresss as $address) {

            if ($address->city) {
                $city = DB::table('cities')->where('name', $address->city)->first();
                if ($city) {
                    $address->city_id = $city->id;
                }
            }

            if ($address->country) {
                $country = Country::where('name', $address->country)->first();
                if ($country) {
                    $address->country_id = $country->id;
                }
            }

            if ($address->state) {
                $state = DB::table('states')->where('name', $address->state)->first();
                if ($state) {
                    $address->state_id = $state->id;
                }
            }
        }

        return Response::json(['status' => 'success', 'ShippingAddress' => $addresss]);
    }

    public function delete_shipping_address(Request $request)
    {
        $user = auth('sanctum')->user()->id;
        $address = ShippingAddress::where('user_id', $user)->where('id', $request->address_id)->first();
        if (!$address) {
            return response()->json(['status' => 'failed', 'message' => 'Address id does not exist']);
        }

        $address->delete();
        return response()->json(['status' => 'success', 'message' => 'Address delete successfully']);
    }

    public function edit_shipping_address(Request $request)
    {
        $user = auth('sanctum')->user()->id;
        $address = ShippingAddress::where('user_id', $user)->where('id', $request->address_id)->first();
        if (!$address) {
            return response()->json(['status' => 'failed', 'shipping_address' => []]);
        }

        return response()->json(['status' => 'success', 'shipping_address' => $address]);
    }

    public function get_coupon()
    {
        $user_id = auth('sanctum')->user()->id;
        $cart_data = Cart::where('user_id', $user_id)->get();
        // Ensure there is cart data
        if ($cart_data->isEmpty()) {
            return response()->json([
                'status' => false,
                'available_coupon' => [],
            ]);
        }

        // Get unique product and category IDs from cart
        $product_ids = $cart_data->pluck('product_id')->unique()->toArray();
        $category_ids = $cart_data->pluck('sub_Cat_id')->unique()->toArray();

        // Retrieve active coupons by type
        $category_coupons = Coupon::where(['status' => '1', 'coupon_type' => 'category_wise'])->get();
        $product_coupons = Coupon::where(['status' => '1', 'coupon_type' => 'product_wise'])->get();
        $user_coupons = Coupon::where(['status' => '1', 'coupon_type' => 'user_wise'])->get();

        // Filter category-wise coupons based on matching category IDs
        $category_coupon_ids = $category_coupons->map(function ($coupon) use ($category_ids) {
            $coupon_categories = json_decode($coupon->category_id) ?? []; // Handle NULL or empty
            
            // Handle NULL or empty
            if (!empty($coupon_categories) && array_intersect($category_ids, $coupon_categories)) {
                return $coupon->id;
            }
        })->filter()->toArray();

        // Filter product-wise coupons based on matching product IDs
        $product_coupon_ids = $product_coupons->map(function ($coupon) use ($product_ids) {
            // $coupon_products = json_decode($coupon->product_id) ?? []; // Handle NULL or empty
            $coupon_products = Product::where('status', 1)->get()->pluck('id')->toArray();
            if (!empty($coupon_products) && array_intersect($product_ids, $coupon_products)) {
                return $coupon->id;
            }
        })->filter()->toArray();

        // Filter user-wise coupons if the user is allowed for it
        $user_coupon_ids = $user_coupons->map(function ($coupon) use ($user_id) {
            $coupon_users = json_decode($coupon->user_id) ?? []; // Handle NULL or empty
            if (in_array($user_id, $coupon_users)) {
                return $coupon->id;
            }
        })->filter()->toArray();

        // Combine all coupon IDs (category + product + user)
        $total_coupon_ids = array_merge($category_coupon_ids, $product_coupon_ids, $user_coupon_ids);
        // Check if there are any coupons
        if (!empty($total_coupon_ids)) {
            // Retrieve available coupons based on user and date conditions
            $available_coupons = Coupon::whereIn('id', $total_coupon_ids)
                ->where(function ($query) use ($user_id) {
                    $query->whereJsonDoesntContain('apply_user_id', $user_id)
                        ->orWhereNull('apply_user_id');
                })
                ->where('coupon_start_date', '<=', Carbon::now())
                ->where(function ($query) {
                    $query->where('coupon_end_date', '>', Carbon::now())
                        ->orWhere(function ($subquery) {
                            $subquery->whereDate('coupon_end_date', '=', Carbon::today())
                                ->whereTime('coupon_end_date', '<=', Carbon::today()->endOfDay());
                        });
                })
                ->get();

            // Add expire field for each available coupon
            $available_coupons->each(function ($coupon) {
                // $coupon->expire = max(0, Carbon::now()->diffInDays(Carbon::parse($coupon->coupon_end_date)->endOfDay()));
                $couponEnd = Carbon::parse($coupon->coupon_end_date)->endOfDay();
                $now = Carbon::now();
            
                if ($now->lessThan($couponEnd)) {
                    $difference = $now->diff($couponEnd);
                    $days = $difference->d;
                    $hours = $difference->h;
                    $minutes = $difference->i;
            
                    if ($days > 0) {
                        $coupon->expire = "{$days} days remaining";
                    } else {
                        $coupon->expire = "{$hours} hours, {$minutes} minutes remaining";
                    }
                } else {
                    $coupon->expire = "Expired";
                }
            
                $coupon->loading = false;
            });

            return response()->json([
                'status' => true,
                'available_coupon' => $available_coupons,
            ]);
        }

        // Return empty response if no coupons are found
        return response()->json([
            'status' => false,
            'available_coupon' => [],
        ]);
    }

    public function apply_coupon(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $cart_ids = explode(',', $request->cart_ids);
        $get_carts = Cart::whereIn('id', $cart_ids)->get();
        $get_coupon = Coupon::where(['code' => $request->coupon_code, 'status' => '1'])->first();

        // dd($get_coupon);
        if (!$get_coupon || json_decode($get_coupon->apply_user_id, true) != null && in_array($user_id, json_decode($get_coupon->apply_user_id, true))) {
            $message = 'Coupon not found.';
        } elseif (Carbon::parse($get_coupon->coupon_end_date)->endOfDay() <= Carbon::now()) {
            $message = 'This coupon has expired.';
        } elseif ($get_coupon->remain_uses <= 0) {
            $message = 'Sorry, this coupon has no remaining uses.';
        } elseif ($get_coupon->amount_type == 'percentage') {
            // $get_percentage_value = [];
            $total_discount = 0;

            if ($get_coupon->coupon_type == 'category_wise') {
                $total = [];
                foreach ($get_carts as $get_pro) {
                    if (in_array($get_pro->sub_Cat_id, json_decode($get_coupon->category_id, true))) {
                        $price = $get_pro->price;
                        $total[] = $price * $get_pro->quantity;
                    }
                }
                $total_discount = array_sum($total) * $get_coupon->amount / 100;
            } elseif ($get_coupon->coupon_type == 'product_wise') {
                $total = [];
                foreach ($get_carts as $get_pro) {
                    if (in_array($get_pro->product_id, json_decode($get_coupon->product_id, true))) {
                        $price = $get_pro->price;
                        $total[] = $price * $get_pro->quantity;
                    }
                }
                $total_discount = array_sum($total) * $get_coupon->amount / 100;
            } elseif ($get_coupon->coupon_type == 'user_wise') {
                foreach ($get_carts as $get_pro) {
                    if (in_array($user_id, json_decode($get_coupon->user_id, true))) {
                        $total_discount = $request->total_amount * $get_coupon->amount / 100;
                    }
                }
            }

            if ($request->total_amount < $get_coupon->product_min_amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order value is less than the minimum coupon value.',
                    'discount_price' => null,
                    'total_price' => null,
                    'coupon_code' => null,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Coupon successfully applied!',
                'discount_price' => $total_discount,
                'total_price' => $request->total_amount - $total_discount,
                'coupon_code' => $request->coupon_code,
            ]);
        } elseif ($get_coupon->amount_type == 'flat') {
            if ($request->total_amount < $get_coupon->product_min_amount || $request->total_amount < $get_coupon->amount) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order value is less than the minimum coupon value.',
                    'discount_price' => null,
                    'total_price' => null,
                    'coupon_code' => null,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Coupon successfully applied!',
                'discount_price' => $get_coupon->amount,
                'total_price' => $request->total_amount - $get_coupon->amount,
                'coupon_code' => $request->coupon_code,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => isset($message) ? $message : 'Something went wrong. Please try again.',
            'discount_price' => [],
            'total_price' => $request->total_amount,
            'coupon_code' => null,
        ]);
    }

    // public function warehouse_list()
    // {
    //     $all = WareHouse::select('id', 'warehouse_name', 'street_address', 'country', 'state', 'city', 'zip_code')->where('status', 1)->get();

    //     if ($all) {
    //         return response()->json(['status' => 'success', 'warehouse' => $all]);
    //     }

    //     return response()->json(['status' => 'failed', 'warehouse' => null]);
    // }

    function remaning($deal)
    {
        $auth = auth('sanctum')->user()->id;
        $count = 0;
        $orders = Order::where(['user_id' => $auth, 'flash_deal_id' => $deal->id])->get()->toArray();

        if ($orders) {
            foreach ($orders as $order) {
                $count += $order['flash_deal_count'];
            }
            return ($deal->quantity) - ($count);
        }

        return $deal->quantity;
    }

    public function get_order_list(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        if ($request->order_status == 'all') {
            $get_orders = Order::select('id', 'order_status', 'net_amount', 'order_id', 'product_details', 'product_complete_details', 'created_at')->where('user_id', $user_id)->orderByDesc('id')->get();

            foreach ($get_orders as $order) {
                $order->products_to_review = [];
            }
        } elseif ($request->order_status == 'to_review') {
            $get_orders = Order::select('id', 'product_details', 'product_complete_details', 'order_status', 'net_amount', 'order_id', 'created_at')->where('order_status', 'delivered')->where('user_id', $user_id)->orderByDesc('id')->get();

            $review_ids = Review::where('user_id', $user_id)->pluck('product_id')->toArray();

            foreach ($get_orders as $order) {
                $product_details = json_decode($order->product_complete_details, true) ?? [];
              
                $products_to_review = [];
                foreach ($product_details as $product_detail) {
                    if (!in_array($product_detail['id'], $review_ids)) {
                        $products_to_review[] = [
                            'product_id' => $product_detail['id'],
                            'product_name' => $product_detail['product_name'],
                            'feature_image' => asset('admin-assets/assets/img/product/feature_img/' . $product_detail['feature_image']),
                            'purchase_price' => $product_detail['purchase_price'],
                            'purchase_quantity' => $product_detail['purchase_quantity'],
                            'total_price' => $product_detail['purchase_total_price'],
                        ];
                    }
                }

                if (!empty($products_to_review)) {
                    $order->products_to_review = $products_to_review;
                } else {
                    $get_orders = $get_orders->filter(function ($item) use ($order) {
                        return $item->id != $order->id;
                    });
                }
            }
        } else {
            $orderStatusMap = [
                'to_confirm' => 'pending',
                'to_ship' => 'processing',
                'to_receive' => 'dispatch',
                'completed' => 'delivered',
                // 'cancelled' => 'cancelled',
                // 'refund' => 'refund',
            ];
            
            $orderStatus = $orderStatusMap[$request->order_status] ?? null;
            
            $get_orders = Order::select('id', 'product_complete_details', 'order_status', 'net_amount', 'order_id', 'created_at')->where('user_id', $user_id)
                ->when($orderStatus, function ($query, $orderStatus) {
                    return $query->where('order_status', $orderStatus);
                })->orderByDesc('id')->get();
        }

        if ($get_orders->isNotEmpty()) {
            $get_orders->map(function ($order) {
                $productDetails = json_decode($order->product_complete_details, true) ?? [];
                $order['net_amount'] = $order->net_amount;
                $order['Quantity'] = count($productDetails);
                $order['order_date'] = $order->created_at->format('d-m-Y');
            });

            // Use values() to reset array keys
            return response()->json(['status' => 'success', 'message' => 'Your orders', 'orders' => $get_orders->values()->makeHidden(['product_details', 'product_complete_details', 'created_at'])]);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'No orders found', 'orders' => []]);
        }
    }

    public function cancel_order(Request $request)
    {
        $user = auth('sanctum')->user();
        $get_orders = Order::where(['id' => $request->order_id, 'user_id' => $user->id])->whereIn('order_status', ['pending', 'confirm', 'processing'])->first();

        if (!$get_orders) {
            return response()->json(['status' => 'failed', 'message' => 'You cannot cancel an order after it has been dispatched or completed.']);
        }

        // Handle coupon update logic
        if ($get_orders->coupon_id != null) {
            $coupon_data = json_decode($get_orders->coupon_id, true);
            if ($coupon_data && isset($coupon_data['id'])) {
                $get_coupon = Coupon::where('id', $coupon_data['id'])->first();
                if ($get_coupon) {
                    $coupon_user_id = json_decode($get_coupon->apply_user_id) ?? [];
                    $coupon_user_ids = array_values(array_diff($coupon_user_id, [$user->id]));
                    $get_coupon->apply_user_id = json_encode($coupon_user_ids);
                    $get_coupon->remain_uses += 1;
                    $get_coupon->save();
                }
            }
        }

        // Update product quantities
        foreach (json_decode($get_orders->product_details, true) as $order_pro_details) {
            $product = Product::where('id', $order_pro_details['product_id'])->first();
            $quantity = $product['quantity'] + $order_pro_details['quantity'];
            $product->update(['quantity' => $quantity]);
        }

        // Cancel the order
        $get_orders->update(['order_status' => 'cancelled', 'shiping_date' => now()->format('Y-m-d')]);
        $fullname = $user->first_name . ' ' . $user->last_name;

        // $bank_Details = BankDetails::where('user_id', $user->id)->first();

        // Send order cancellation email admin
        Mail::send('admin.email.order-cancellation-success', ['order' => $get_orders, 'user' => $fullname], function ($message) use ($get_orders) {
            $message->to('info@nexamarket.app');
            $message->subject('Order Cancellation Request');
        });

        // Send order cancellation email customer
        Mail::send('admin.email.OrderCancelCustomerMail', ['order' => $get_orders, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Order Cancellation Successful');
        });

        return response()->json(['status' => 'success', 'message' => 'Your order was successfully cancelled.']);
    }

    public function re_order(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;

        $get_orders = Order::where(['id' => $request->order_id, 'user_id' => $user_id])->whereIn('order_status', ['delivered', 'complete'])->first();

        if ($get_orders) {
            foreach (json_decode($get_orders->product_complete_details, true) as $details) {
                $product = Product::find($details['id']);

                if ($product->quantity >= $product->min_order) {
                    $cart_data = Cart::where(['product_id' => $details['id'], 'user_id' => $user_id])->first();

                    if ($cart_data) {
                        $qty = $cart_data->quantity + $details['purchase_quantity'];
                        $cart_data->quantity = $qty;
                        $cart_data->save();
                    } else {
                        $cart = new Cart();
                        $cart->user_id = $user_id;
                        $cart->product_id = $product->id;
                        $cart->category_id = $product->parent_category;
                        $cart->sub_Cat_id = $product->child_category;
                        $cart->quantity = $product->min_order;
                        $cart->save();
                    }
                }
            }

            return response()->json(['status' => 'success', 'message' => 'All products saved in the cart']);
        }

        return response()->json(['status' => 'failed', 'message' => 'Order not found or cannot be reordered']);
    }

    public function single_order_details(Request $request)
    {
        $user = auth('sanctum')->user();
        $order_info = Order::where('id', $request->order_id)->where('user_id', $user->id)->first();

        if (!$order_info) {
            return response()->json(['status' => 'failed', 'order_information' => null, 'order_item_information' => null, 'shipping_address' => null, 'WareHouse_address' => null, 'WareHouse_contect_details' => null, 'pdf_url' => null]);
        }

        // Call the method in your main function
        $order_item_information = $this->processOrderItems($order_info);

        $user_address = json_decode($order_info->shiping_address_id, true);

        $order_information = [
            'id' => $order_info->id,
            'order_id' => $order_info->order_id,
            'quantity' => count($order_item_information),
            'order_status' => $order_info->order_status,
            'order_date' => $order_info->created_at->format('F d,y'),
            'sub_total' => $order_info->sub_total,
            'coupon_discount' => $order_info->coupon_discount,
            'total_amount' => $order_info->net_amount,
            'shipping_charges' =>  $order_info->shipping_charges,
            // 'shipping_to' => $user_address['name'],
        ];

        $shipping_address = [
            'service_type' => $order_info->delivery_option,
            'shiping_date' => $order_info->shiping_date ? ($order_info->shiping_date)->format('F d,Y') : 'Not available',
            'address' => $user_address['address'],
            'country' => $user_address['country'],
            'zip_code' => $user_address['zip_code']
        ];

        // if ($order_info->delivery_option == 'Pickup' && $order_info->warehouse_id != null) {
        //     $warehouse_id = json_decode($order_info->warehouse_id, true);
        //     $WareHouse_address = [
        //         'warehouse_name' => $warehouse_id['warehouse_name'],
        //         'address' => $warehouse_id['street_address'],
        //         'country' => $warehouse_id['country'],
        //         'zip_code' => $warehouse_id['zip_code']
        //     ];
        //     $WareHouse_contect_details = [
        //         'contact_name' => $warehouse_id['contact_name'],
        //         'contact_email' => $warehouse_id['contact_email'],
        //         'Phone_no' => $warehouse_id['country_code'] . ' ' . $warehouse_id['contact_number'],
        //     ];
        // } else {
        //     $WareHouse_address = [];
        //     $WareHouse_contect_details = [];
        // }

        if ($order_info->transaction_id) {

            $payment = DB::table('transactions')->select('amount', 'currency', 'payment_method', 'created_at')->where('transaction_id', $order_info->transaction_id)->first();
            if ($payment) {
                $payment_details = [
                    'payment_method' => $payment->payment_method,
                    'payment_date' => $payment->created_at,
                    'payment_amount' => $order_info->net_amount,
                ];
            } else {
                $payment_details = null;
            }
        } else {
            $payment_details = [
                'payment_method' => $order_info->payment_mode,
                'payment_amount' => $order_info->net_amount,
            ];
        }

        
        $pdf = Pdf::loadView('admin.order.download_order_info', compact('order_info'));
        // dd($pdf);
        
        $fileName = 'OrderInvoice_' . $request->order_id . '.pdf';
        $filePath = public_path('admin-assets/assets/invoice/' . $fileName);

        $pdf->save($filePath);

        $fileUrl = url('admin-assets/assets/invoice/' . $fileName);

        return response()->json(['status' => 'success', 'order_information' => $order_information, 'order_item_information' => $order_item_information, 'shipping_address' => $shipping_address, 'payment_details' => $payment_details, 'pdf_url' => $fileUrl]);
    }

    // Refactor the order item processing logic into a method
    private function processOrderItems($order_info)
    {
        $order_item_information = json_decode($order_info->product_complete_details, true);
    
        return array_map(function ($information) use ($order_info) {
            return [
                'product_name' => $information['product_name'] ?? null,
                'price' => $information['purchase_price'],
                'sale_price' => $information['sale_price'] ?? null,
                'regular_price' => $information['regular_price'] ?? null,
                'total_price' => $information['purchase_total_price'] ?? null,
                'total_quantity' => $information['purchase_quantity'] ?? null,
                'feature_image' => asset('admin-assets/assets/img/product/feature_img/' . ($information['feature_image'] ?? '')),
            ];
        }, $order_item_information ?? []);
    }
        
    public function product_order_save(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $cart_ids = explode(',', $request->cart_id);
        $lastOrderId = Order::orderBy('id', 'DESC')->value('id') ?? 0;
        $user = User::find($user_id);

        // Create a new Order
        $order = new Order();
        $order->user_id = $user_id;
        $order->sub_total = $request->sub_total_amount;
        $order->coupon_discount = $request->item_discount_amount ?? 0;
        $order->net_amount = $request->total_amount;
        $order->shipping_charges = $request->shipping_charges;
        $order->payment_mode = $request->payment_mode;
        $order->delivery_option = $request->delivery_option;
        $order->transaction_id = $request->transaction_id;

        // if ($request->warehouse_id) {
        //     $warehouse_address = WareHouse::where('id', $request->warehouse_id)->first();
        //     if (!$warehouse_address) {
        //         return response()->json(['status' => false, 'message' => 'Invalid warehouse address.']);
        //     }
        //     $order->warehouse_id = json_encode($warehouse_address);
        // }

        $shipping_address = ShippingAddress::where(['id' => $request->address_id, 'user_id' => $user_id])->first();

        if (!$shipping_address) {
            return response()->json(['status' => false, 'message' => 'Invalid shipping address.']);
        }

        // Or if you need the full address as JSON, do this:
        $order->shiping_address_id = json_encode($shipping_address);

        // Generate Order ID
        $order->order_id = '#' . str_pad($lastOrderId + 1, 7, "0", STR_PAD_LEFT);

        // Apply coupon if provided
        if ($request->coupon_id) {
            $get_coupon = Coupon::where('code', $request->coupon_id)->first();

            if ($get_coupon) {
                // Decode apply_user_id from the coupon
                $applied_users = json_decode($get_coupon->apply_user_id, true) ?? [];

                // Check if the logged-in user's ID is not in the applied_users array
                if (!in_array($user_id, $applied_users)) {
                    // Add the user ID to the applied_users array and update remain_uses
                    array_push($applied_users, $user_id);
                    $get_coupon->apply_user_id = json_encode($applied_users);
                    $get_coupon->remain_uses = strval($get_coupon->remain_uses - 1);
                    $get_coupon->save();

                    // Assign the coupon to the order
                    $order->coupon_id = json_encode($get_coupon);
                } else {
                    return response()->json(['status' => false, 'message' => 'You have already applied this coupon.']);
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Invalid or expired coupon.']);
            }
        }

        // Retrieve cart data
        $cart_data = Cart::select('product_id', 'category_id', 'quantity')->whereIn('id', $cart_ids)->get();
        if ($cart_data->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No items found in the cart.']);
        }

        if (count($cart_data) > 0) {
            $numberOfProducts = $cart_data->count();
            $for_store_product = [];
            $product_staff_member_id = [];
            $flash_deal_count = 0;
            foreach ($cart_data as $cart_pro_details) {
                $product = Product::where('id', $cart_pro_details->product_id)->first();
                $product_staff_member_id[] = $product->user_id;
                if ($product->flash_deal == 1) {
                    $flash_deal_count++;
                }
                if ($product->quantity >= $cart_pro_details->quantity) {
                    $product->update(['quantity' => strval($product->quantity - $cart_pro_details->quantity)]);
                }

                $product['purchase_quantity'] = $cart_pro_details->quantity;
                $product['purchase_price'] = $product->sale_price;
                $product['purchase_total_price'] = $product->sale_price * $cart_pro_details->quantity;
                $product['coupon_discount'] = $cart_pro_details->coupon;
                array_push($for_store_product, $product);
                // array_push($staff_member, $product_staff_member_id);
            }
            $unique_staff_member_ids = array_unique($product_staff_member_id);
            $order->staff_member_id = json_encode($unique_staff_member_ids, JSON_FORCE_OBJECT);
            $order->product_details = json_encode($cart_data, JSON_FORCE_OBJECT);
            $order->product_complete_details = json_encode($for_store_product, JSON_FORCE_OBJECT);
        }
        if ($flash_deal_count > 0) {
            $deal = DB::table('product_flash_deals')->where('start_flash_deal', '<=', now())->where('end_flash_deal', '>=', now())->first();
            if ($deal) {
                $remaning_deal = $this->remaning($deal);
                if ($remaning_deal < $flash_deal_count || $remaning_deal == 0) {
                    return response()->json(['status' => false, 'message' => 'No flash deal items.']);
                }
                $order->flash_deal_id = $deal->id;
                $order->flash_deal_count = $flash_deal_count;
                $order->save();
            } else {
                $order->save();
            }
        } else {
            $order->save();
        }
        $deletedCount = Cart::whereIn('id', $cart_ids)->where('user_id', $user_id)->delete();

        $notify_data = ['title' => 'Order Placed - ' . $order->order_id, 'user_id' => $user_id, 'body' => 'your order successfully placed'];
        $controller = new NotificationApiController();
        if ($user->notification_status == 0) {
            $device_token = DB::table('device_tokens')->where('user_id', $user_id)->pluck('device_token')->toArray();
            $controller->sendPushNotification($device_token, $notify_data);
        }

        $controller->AddNotification($notify_data, $order->order_id, $order->staff_member_id, $user->notification_status);

        // dd('ok');
        /************ Customer Mail **************/
        Mail::send('admin.email.OrderConfirm', ['order' => $order, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Order Placed Successful');
        });

        /*********** Admin Mail *****************/
        Mail::send('admin.email.NewOrderRecive', ['order' => $order, 'user' => $user], function ($message) use ($user) {
            $message->to('info@nexamarket.app');
            $message->subject('New Order Received');
        });

        if ($order->staff_member_id) {
            $staff_ids = json_decode($order->staff_member_id, true);

            foreach ($staff_ids as $staff_id) {
                $staff = Staff::select('email')->where('id', $staff_id)->first();
                if ($staff && $staff->email) {
                    Mail::send('admin.email.NewOrderRecive', ['order' => $order, 'user' => $user], function ($message) use ($user, $staff) {
                        $message->to($staff->email);
                        $message->subject('New Order Received');
                    });
                }
            }
        }

        // Return success response
        return response()->json(['status' => true, 'message' => 'Order placed successfully!', 'order_id' => $order->order_id, 'total_amount' => $order->net_amount]);
    }

    public function checkCartProduct(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $cart_ids = explode(',', $request->cart_id);
        // Retrieve cart data
        $cart_data = Cart::select('product_id', 'category_id', 'quantity')->whereIn('id', $cart_ids)->get();
        if ($cart_data->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No items found in the cart.']);
        }

        if (count($cart_data) > 0) {
            $flash_deal_count = 0;
            foreach ($cart_data as $cart_pro_details) {
                $product = Product::where('id', $cart_pro_details->product_id)->first();
                if ($product->flash_deal == 1) {
                    $flash_deal_count++;
                }
            }
        }

        if ($flash_deal_count > 0) {
            $deal = DB::table('product_flash_deals')->where('start_flash_deal', '<=', now())->where('end_flash_deal', '>=', now())->first();

            if ($deal) {
                $remaning_deal = $this->remaning($deal);
                if ($remaning_deal < $flash_deal_count || $remaning_deal == 0) {
                    if ($remaning_deal == 0) {
                        return response()->json(['status' => false, 'message' => 'Your purchase limit for the flash deal has been reached.']);
                    }
                    return response()->json(['status' => false, 'message' => 'You can purchase only ' . $remaning_deal . ' items in flash deal, but you have added ' . $flash_deal_count . ' items']);
                }
                return response()->json(['status' => true, 'message' => 'success']);
            } else {
                return response()->json(['status' => true, 'message' => 'success']);
            }
        } else {
            return response()->json(['status' => true, 'message' => 'success']);
        }
    }

    public function getTrackingNumber(Request $req) {
        $auth = auth('sanctum')->user()->id;
        $order = Order::where(['id'=>$req->order_id,'user_id' => $auth])->first();
        if(!$order){
            return response()->json(['status' => false, 'message' => 'Order not found'],404);
        }
        if(empty($order->tracking_number) || empty($order->tracking_carrier_code)){
            $data = ['tracking-number'=>null,'carrier-code'=>null];
            return response()->json(['status' => false, 'message' => 'Tracking number not added from admin', 'tracking-details' => $data],200);
        }
        $data = ['tracking-number'=>$order->tracking_number,'carrier-code'=>$order->tracking_carrier_code];
        return response()->json(['status' => true, 'message' => 'success', 'tracking-details' => $data],200);
    }
}
