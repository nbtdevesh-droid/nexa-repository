<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\admin\StripeWebhookController;
use App\Http\Controllers\admin\PaystackWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['controller' => AuthApiController::class], function () {
    Route::post('login', 'user_login');
    Route::post('Customer-ExistOrNot', 'customerExistOrNot');
    Route::post('Customer-RegisterOrLogin', 'customerRegisterOrLogin');
    Route::post('forgot-password', 'recover_password');
    Route::post('forgot-password-otp-verified', 'reset_password_otp_verification');
    Route::post('reset-password', 'reset_password'); 
    Route::post('socailite-login', 'socialite_login');
});

Route::middleware('throttle:api')->group(function () {
    Route::group(['controller' => HomeApiController::class], function () {
        Route::post('home', 'HomePage');
        Route::post('category-wise-product', 'category_wise_product');
        Route::get('all_category', 'GetAllCategory');
        Route::post('category-wise-subcategoryOrProduct', 'CategorySubcategoryOrProduct');
        Route::post('sub-category-wise-product', 'sub_category_wise_product');
        Route::post('all-products', 'AllProducts');
        Route::post('single-product', 'SingleProduct');
        Route::get('all-brands', 'AllBrands');
        Route::post('keyword-suggestion', 'getSuggestions');
        Route::post('flash-deals', 'flash_deal');
    });
});

Route::middleware('apiAuth:sanctum')->group(function () {
    Route::group(['controller' => HomeApiController::class], function () {
        Route::get('get-profile', 'getProfileDetail');
        Route::post('update-profile', 'UpdateProfileDetail');
        Route::post('add-to-wishlist', 'AddToWishlist');
        Route::get('all-wishlist-products', 'AllWishlistProducts');
        Route::post('product-review', 'product_review');
        Route::post('product-review-update', 'product_review_update');
        Route::post('product-review-helpful', 'product_review_helpful');
        Route::post('product-all-reviews', 'product_all_reviews');
        Route::get('new-arival', 'new_arival');
        Route::get('all-vouchers', 'vouchers');
    });

    Route::post('email-verify', [AuthApiController::class, 'email_verify']);
    Route::post('email-otp-verify', [AuthApiController::class, 'email_otp_verify']);

    Route::group(['controller' => CartApiController::class], function(){
        Route::post('add-to-cart', 'add_to_cart');
        Route::post('get-all-cart-data', 'get_all_cart_data');
        Route::post('delete-cart-item', 'delete_cart_item');
        Route::post('cart-qty-increase', 'cart_qty_increase');
        Route::post('cart-qty-decrease', 'cart_qty_decrease');
    });

    Route::group(['controller' => OrderApiController::class], function(){
        Route::get('country-list', 'country_list');
        Route::post('state-list', 'state_list');
        Route::post('city-list', 'city_list');
        Route::post('add-update-shipping-address', 'add_update_shipping_address');
        Route::get('all-shipping-address', 'all_shipping_address');
        Route::post('delete-shipping-address', 'delete_shipping_address');
        Route::post('edit-shipping-address', 'edit_shipping_address');
        Route::get('coupon-get', 'get_coupon');
        Route::post('apply-coupon', 'apply_coupon');
        Route::get('get-warehouse-list', 'warehouse_list');
        Route::post('product-order', 'product_order_save');
        Route::post('get-order-list', 'get_order_list');
        // Route::get('my-order', 'my_order');
        Route::post('cancel-order', 'cancel_order');
        Route::post('re-order', 're_order');
        Route::post('single-order-details', 'single_order_details');
        Route::post('check-cart-product', 'checkCartProduct');
        Route::post('get-track-info', 'getTrackingNumber');

        // Route::post('single-order-details-pdf', 'single_order_details_pdf');
    });

    Route::post('logout', [AuthApiController::class, 'logout_auth']);

    Route::group(['controller' => NotificationApiController::class], function(){
        Route::get('notification-list', 'view_notification');
        Route::post('notification-delete', 'delete_notification');
    });

    Route::group(['controller' => AccountSettingApiController::class], function(){
        Route::post('change-password', 'account_change_password');
        Route::post('notification-setting', 'notification_setting');
        Route::post('get-product-view-count', 'getProductViewCount');
        Route::delete('customer-delete', 'customer_delete');

        // Route::post('add-customer-bank-details', 'add_bank_details');
        // Route::get('get-customer-bank-details', 'get_bank_details');
    });
});

// Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
Route::post('/paystack/webhook', [PaystackWebhookController::class, 'handleWebhook']);

/*************************** APP Notification setting *****************************/
Route::get('app-setting', [AccountSettingApiController::class, 'app_setting']);