<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingCharges;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CartApiController extends Controller
{
    public function add_to_cart(Request $request)
    {
        $user = auth('sanctum')->user();
        $product = Product::find($request->product_id);
        if(!$product){
            return Response::json(['status' => 'failed', 'message' => 'Product id does not exit', 'cart_id' => '']);
        }

        if($product->quantity >= $product->min_order){
            $cart_data = Cart::where(['product_id' => $request->product_id, 'user_id' => $user->id])->first();
    
            if ($cart_data) {
                $qty = $cart_data->quantity + $request->total_quantity;
                $cart_data->quantity = $qty;
                // $cart_data->total_price = $cart_data->price * $qty;
                $cart_data->save();
                return Response::json(['status' => 'success', 'message' => $request->total_quantity . ' item add in cart', 'cart_id' => $cart_data->id]);
            } else {
                $cart = new Cart();
                $cart->user_id = $user->id;
                $cart->product_id = $product->id;
                $cart->category_id = $product->parent_category;
                $cart->sub_Cat_id = $product->child_category;
                $cart->quantity = $request->total_quantity;
                // $cart->price = $request->price;
                // $cart->total_price = $request->price * $request->total_quantity;
                $cart->save();

                return Response::json(['status' => 'success', 'message' => $request->total_quantity . ' item add in cart', 'cart_id' => $cart->id]);
            }
        }else{
            return Response::json(['status' => 'failed', 'message' => 'Product out of stock', 'cart_id' => '']);
        }
    }
    
    public function get_all_cart_data(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;

        // Fetch cart data and associated product details
        $view_cart = Cart::where('user_id', $user_id)->with(['cartProduct:id,product_name,feature_image,quantity,min_order,sale_price,regular_price'])->get()->makeHidden(['cartProduct', 'created_at', 'updated_at']);
        // dd($view_cart);
        if ($view_cart->isEmpty()) {
            return response()->json([
                'status' => 'failed',
                'view_cart_total' => 0,
                'sub_total_price' => 0,
                'total_price' => 0,
                'discount' => 0,
                'shipping_charge' => 0,
                'total_pay' => 0,
                'message' => 'No data found',
                'loading' => false,
                'view_cart' => [],
                'delivery_address' => null
            ]);
        }

        // Initialize subtotal
        $sub_total_price = 0;
        $total_price = 0;

        // Modify cart items and calculate subtotal
        $view_cart->map(function ($cart_item) use (&$sub_total_price, &$total_price) {
            $product = $cart_item->cartProduct;

            // Set default values for cart item
            $cart_item['available'] = false;
            $cart_item['available_quantity'] = '0';

            // Check if product is available and set data accordingly
            if ($product && $product->quantity > 0) {
                $cart_item['available'] = true;
                $cart_item['available_quantity'] = (string) $product->quantity;
                $cart_item['min_order'] = (string) $product->min_order;
                $cart_item['sale_price'] = $product->sale_price;
                $cart_item['regular_price'] = $product->regular_price;
                $cart_item['total_regular_price'] = $product->regular_price * $cart_item->quantity;
                if($cart_item['regular_price'] && $cart_item['sale_price']){
                    $cart_item['discount'] = $cart_item['regular_price'] - $cart_item['sale_price'];
                }else{
                    $cart_item['discount'] = 0;
                }
                
                // Add to the subtotal
                $sub_total_price += $product->regular_price * $cart_item->quantity;
                $total_price += $product->sale_price * $cart_item->quantity;
            }
            $cart_item['price'] = $product->sale_price;
            $cart_item['total_price'] = $product->sale_price;

            $cart_item['feature_img'] = $this->getImageUrl('product/feature_img', $product->feature_image);
            $cart_item['product_name'] = $product->product_name ?? 'Unknown Product';
            $cart_item['loadinglike'] = false;
            $cart_item['select'] = false;

            return $cart_item;
        });

        // Calculate the total price (sum of sale prices for each cart item)
        // $total_price = $view_cart->sum('total_price');

        $delivery_address = ShippingAddress::where('user_id', $user_id)->get();

        $shipping_charges = DB::table('shipping_charges')->select('id', 'shipping_amount', 'after_charges as shipping_charges')->where('id', '1')->first();
 
        return response()->json([
            'status' => 'success',
            'view_cart_total' => $view_cart->count(),
            'sub_total_price' => $sub_total_price,
            'total_price' => $total_price,
            'discount' => $sub_total_price - $total_price,
            'shipping_charge' => $shipping_charges->shipping_amount < $total_price ? 0 : (int) $shipping_charges->shipping_charges,
            'total_pay' => $shipping_charges->shipping_amount < $total_price ? $total_price : $total_price + $shipping_charges->shipping_charges,
            'message' => 'Successfully Get Products',
            'loading' => false,
            'view_cart' => $view_cart,
            'delivery_address' => $delivery_address,
            'shipping_charges' => $shipping_charges
        ]);
    }

    public function cart_qty_increase(Request $request)
    {
        $user = auth('sanctum')->user();
        $cart = Cart::where(['id' => $request->cart_id, 'user_id' => $user->id])->first();

        if (!$cart) {
            return response()->json(['status' => 'failed', 'message' => 'cart id does not exit']);
        }

        $product = Product::find($cart->product_id);
        if ($product->quantity >= 1) {
            $qty = $cart->quantity + 1;
            $cart->quantity = $qty;
            // $cart->total_price = $cart->price * $qty;
            $cart->save();

            return response()->json(['status' => 'success', 'message' => 'Cart Qty Updated']);
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Product out of stock']);
        }
    }

    public function cart_qty_decrease(Request $request)
    {
        $user = auth('sanctum')->user();
        $cart = Cart::where(['id' => $request->cart_id, 'user_id' => $user->id])->first();
        $product = Product::find($cart->product_id);

        if (!$cart) {
            return response()->json(['status' => 'failed', 'message' => 'cart id does not exit']);
        }

        if ($cart->quantity == $product->min_order) {
            return response()->json(['status' => 'failed', 'message' => 'Qty can not less then ' . $product->min_order]);
        } else {
            $qty = $cart->quantity - 1;
            $cart->quantity = $qty;
            // $cart->total_price = $cart->price * $qty;
            $cart->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Cart Qty Updated']);
    }

    public function delete_cart_item(Request $request)
    {
        $user = auth('sanctum')->user();
        $cart_ids = explode(',', $request->input('cart_id'));
        $cart_ids = array_map('trim', $cart_ids);
        $cart_ids = array_map('intval', $cart_ids);

        $carts = Cart::whereIn('id', $cart_ids)->where('user_id', $user->id)->get();

        if ($carts->isEmpty()) {
            return response()->json(['status' => 'failed', 'message' => 'No cart items found.']);
        }

        $deletedCount = Cart::whereIn('id', $cart_ids)->where('user_id', $user->id)->delete();

        if ($deletedCount === 0) {
            return response()->json(['status' => 'failed', 'message' => 'No cart items were deleted.']);
        }

        return response()->json(['status' => 'success', 'message' => 'Cart items deleted successfully']);
    }

    private function getImageUrl($type, $filename)
    {
        return asset("admin-assets/assets/img/{$type}/{$filename}");
    }

}