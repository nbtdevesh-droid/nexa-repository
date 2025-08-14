<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Facades\Route;
use App\Models\CustomerSupport;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache', function () {
    Artisan::call('config:clear'); 
    return "Cache cleared successfully!";
})->name('clear.cache');

Route::group(['middleware' => 'guest'], function () {
    Route::view('/', 'auth/login')->name('login');
    Route::view('forgot-password', 'auth/forgot-password')->name('forgotpassword');
    Route::post('recover-password', [AdminController::class, 'recover_password_func'])->name('recover-password');
    Route::post('reset-password', [AdminController::class, 'reset_password_func'])->name('reset-password');
    Route::post('admin/login', [AdminController::class, 'admin_login_func'])->name('admin.login');
});


Route::get('/reset-password/{token}', function (string $token) {
    return view('auth/reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::group(['middleware' => 'auth'], function () {
    Route::group(['controller' => HomeController::class], function () {
        Route::get('dashboard', 'dashboard')->name('admin.dashboard');

        /*********************************** Update Profile ************************************/
        Route::get('profile', 'get_admin_info')->name('admin.profile');
        Route::post('profile/update', 'update_admin_info')->name('admin.profile.update');
        Route::post('profile/password/update', 'update_admin_password')->name('admin.profile.password.update');
        Route::post('upload-images', 'storeImage')->name('admin.uploadimage');

        /************************************* Notification **************************/
        Route::get('show-notification', 'all_notifications')->name('admin.notifications');
        Route::post('delete-notification', 'delete_notifications')->name('delete.notification');
    });

    Route::group(['middleware' => 'can:access-admin-dashboard'], function () {
        /******************************* User *****************************/
        Route::resource('user', UserController::class);
        Route::post('unique-phone-number-user', [UserController::class, 'uniquePhoneNumberUser']);
        Route::post('unique-email-user', [UserController::class, 'uniqueEmailUser']);
        Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('user.restore');

        /************************* Staff ************************/
        Route::resource('staff', StaffController::class);
        Route::post('unique-phone-number', [StaffController::class, 'uniquePhoneNumber']);
        Route::post('unique-email', [StaffController::class, 'uniqueEmail']);
        Route::get('export-staff', [StaffController::class, 'export_staff'])->name('staff.export_staff');

        /********************************* Coupon ************************/
        Route::resource('coupon', CouponController::class);
        Route::post('get-discount-wise', [CouponController::class, 'getDiscountWise']);
        Route::post('get-category-wise', [CouponController::class, 'getCategoryWise']);
        Route::post('get-subcategory-wise', [CouponController::class, 'getSubcategoryWise']);
        Route::post('/get-subcategories', [CouponController::class, 'getSubcategories'])->name('get.subcategories');
        Route::post('get-products', [CouponController::class, 'getProducts'])->name('get.products');

        /******************************* All pages ***********************/
        Route::get('customer-support', [CustomerSupportController::class, 'edit'])->name('customer.support');
        Route::get('page-privacy-policy', [CustomerSupportController::class, 'privacy_policy'])->name('privacy.policy');
        Route::get('terms-of-use', [CustomerSupportController::class, 'terms_of_use'])->name('terms.use');
        Route::post('customer-support/update/{id}', [CustomerSupportController::class, 'update'])->name('customer.page.update');
        Route::get('Pages/HomeIcon', [CustomerSupportController::class, 'getHomeIcon'])->name('pages.home.icon.update');
        Route::post('update-home-icon', [CustomerSupportController::class, 'UpdateHomeIcon'])->name('home.icon.update');

        /******************************* Home Banner ***********************/
        Route::get('Home-Banner', [CustomerSupportController::class, 'HomeBannerEdit'])->name('home.banner');
        Route::post('Home-Banner-update', [CustomerSupportController::class, 'HomeBannerUpdate'])->name('home.banner.update');
        Route::get('Home-Banner-delete', [CustomerSupportController::class, 'HomeBannerDelete'])->name('banner.image.delete');

        /***************************** WareHouse ********************************/
        // Route::resource('warehouse', WareHouseController::class);
        // Route::get('/get-states', [WareHouseController::class, 'getStates'])->name('getStates');
        // Route::get('/get-cities', [WareHouseController::class, 'getCities'])->name('getCities');

        Route::get('/flash-deal', [SettingController::class, 'flash_deal'])->name('setting.flash_deal');
        Route::get('/add-flash-deal', [SettingController::class, 'add_flash_deal'])->name('setting.add-flash_deal');
        Route::post('/store-flash-deal', [SettingController::class, 'store_flash_deal'])->name('setting.store-flash_deal');
        Route::get('/edit-flash-deal/{id}', [SettingController::class, 'edit_flash_deal'])->name('setting.deal.edit');
        Route::post('/setting-update-flash-deal/{id}', [SettingController::class, 'setting_update_flash_deal'])->name('setting.update-flash_deal');
        Route::post('/destroy-flash-deal/{id}', [SettingController::class, 'destroy_flash_deal'])->name('setting.deal.destroy');
        
        Route::get('/shipping-charges', [SettingController::class, 'shipping_charges'])->name('setting.shipping_charges');
        Route::post('/update-shipping-charge', [SettingController::class, 'setting_update_shipping_charges'])->name('setting.update_shipping_charge');

        /****************************** Payment Refund *********************/
        Route::post('payment-refund/{id}', [OrderController::class, 'refund'])->name('order.payment.refund');

        /************************************* Review *********************************/
        Route::get('all-review', [ReviewController::class, 'allReview'])->name('all.reviews');
        Route::get('add-new-review', [ReviewController::class, 'addReview'])->name('add.reviews');
        Route::post('new-review-store', [ReviewController::class, 'ReviewStore'])->name('review.store');
        Route::post('delete-review/{id}', [ReviewController::class, 'ReviewDelete'])->name('review.destroy');

        /*********************************** app notification setting ******************************************/
        Route::get('app-notification-setting', [SettingController::class, 'app_setting'])->name('app.notification.setting');
        Route::post('app-notification-setting-update', [SettingController::class, 'app_setting_update'])->name('app.notification.setting.update');
    });

    /******************************* User View for staff member dashboard ***********************/
    Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');
    Route::get('export-user', [UserController::class, 'export_user'])->name('order.export_user');

    /******************************** Category ************************/
    Route::resource('category', CategoryController::class);
    Route::post('unique-category-name', [CategoryController::class, 'unique_category_name']);
    Route::post('unique-category-order', [CategoryController::class, 'unique_category_order']);

    /************************************** Brand *********************/
    Route::resource('brand', BrandController::class);
    Route::post('unique-brand-name', [BrandController::class, 'unique_brand_name']);

    /************************** Product ***********************************/
    Route::resource('products', ProductController::class);
    Route::post('get-child-categories', [ProductController::class, 'get_child_category']);
    Route::post('delete-image', [ProductController::class, 'delete_image']);
    // Route::post('import-csv', [ProductController::class, 'import_csv'])->name('product.import.csv');
    Route::post('product-bulkDelete', [ProductController::class, 'bulkDelete'])->name('product.bulkDelete');
    Route::post('/import-product', [ProductController::class, 'import'])->name('import.product');
    Route::post('upload-summernote-image', [ProductController::class, 'summernoteimgupload'])->name('upload.summernote.image');

    /******************************temp-images*******************************/
    Route::post('/upload-temp-image', [TempImageController::class, 'create'])->name('temp-images.create');
    Route::post('/product-image/update', [TempImageController::class, 'update'])->name('product-images.update');
    Route::delete('/product-image', [TempImageController::class, 'destroy'])->name('product-images.destroy');

    /****************************** Orders *************************************/
    Route::resource('order', OrderController::class);
    Route::get('download-info/{id}', [OrderController::class, 'generate_pdf'])->name('order.download.info');
    Route::get('order/invoice/{orderId}', [OrderController::class, 'showInvoice']);
    Route::post('/order/tracking/{orderId}', [OrderController::class, 'orderTrackingUpdate'])->name('order.tracking');

    Route::get('export-order', [OrderController::class, 'export_order'])->name('order.export_order');
    /********************************* Logout *******************************/
    Route::get('logout', [AdminController::class, 'logout'])->name('admin.logout');
});


Route::get("/customer-support-page", function () {
    $pagecontent = CustomerSupport::where('page', 'customer_support')->first();
    return view('customer_support', compact('pagecontent'));
});

Route::get("/privacy-policy", function () {
    $pagecontent = CustomerSupport::where('page', 'privacy')->first();
    return view('privacy_policy', compact('pagecontent'));
});

Route::get("/terms-condition", function () {
    $pagecontent = CustomerSupport::where('page', 'Terms & Condition')->first();
    return view('terms_and_condiation', compact('pagecontent'));
});

Route::view('delete-account', 'delete_account');
Route::post('customer-account-delete', [AdminController::class, 'AccountDelete'])->name('customer.account.delete');