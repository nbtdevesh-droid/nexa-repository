<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Country;
use App\Models\Order;
use App\Models\Staff;
use App\Models\Product;
use App\Models\User;
use App\Rules\MatchAdminOldPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class HomeController extends Controller
{
    public function dashboard()
    {
        $isWebUser = Auth::guard('web')->check();
        $userId = $isWebUser ? null : Auth::guard('member')->user()->id;

        if ($isWebUser) {
            $data['product'] = Product::where('status', 1)->count();
            $data['customer'] = User::where('status', 1)->count();
            $data['staff'] = Staff::where('status', 1)->count();
            $data['order_count'] = Order::count();

            $orders = Order::whereNotIn('order_status', ['cancelled', 'pending'])->get();
            
            $total_sold = 0;
            $total_sales = 0;
            
            foreach ($orders as $order) {
                $product_details = json_decode($order->product_details, true) ?? [];

                foreach ($product_details as $product_detail) {
                    $total_sold += $product_detail['quantity'] ?? 0;
                }

                $total_sales += $order->net_amount;
            }

            $data['total_sales'] = $total_sales;
            $data['total_unit_sold'] = $total_sold;

            // Load orders with related user information
            $data['orders'] = Order::with('user')->orderBy('created_at', 'desc')->get();
        } else {
            $data['product'] = Product::where('status', 1)->exists() ? Product::where(['user_id' => $userId, 'status' => 1])->count() : 0;
    
            // Fetch all orders with user details
            $allOrders = Order::with('user')->orderBy('created_at', 'desc')->get();
    
            // Initialize an array to store orders relevant to the logged-in staff member
            $filteredOrders = [];
    
            // Loop through each order and match the user_id inside product_complete_details with the staff member's ID
            foreach ($allOrders as $order) {
                $productCompleteDetails = json_decode($order->product_complete_details, true) ?? [];
    
                foreach ($productCompleteDetails as $productDetail) {
                    if (isset($productDetail['user_id']) && $productDetail['user_id'] == $userId) {
                        $filteredOrders[] = $order;
                        break;
                    }
                }
            }

            $allOrderss = Order::whereNotIn('order_status', ['cancelled', 'pending'])->get();

            $total_sold = 0;
            $total_sales = 0;

            foreach ($allOrderss as $orders) {
                $productCompleteDetails = json_decode($orders->product_complete_details, true) ?? [];
    
                foreach ($productCompleteDetails as $productDetail) {
                    if (isset($productDetail['user_id']) && $productDetail['user_id'] == $userId) {
                        $total_sold += $productDetail['purchase_quantity'] ?? 0;
                        $total_sales += ($productDetail['purchase_quantity'] * $productDetail['purchase_price']) ?? 0;
                        // break;
                    }
                }
            }
    
            $data['orders'] = $filteredOrders;

            $data['staff_order_count'] = count($filteredOrders);
            $data['total_unit_sold'] = $total_sold;
            $data['total_sales'] = $total_sales;
        }
        return view('admin.dashboard', $data);
    }

    //admin profile
    public function get_admin_info()
    {
        if (Auth::guard('web')->check()) {
            $data['admin'] = Auth::guard('web')->user();
            return view('admin.profile.index', $data);
        } else {
            $data['staff'] = Auth::guard('member')->user();
            $data['countries'] = Country::orderBy('name', 'asc')->get();
            return view('admin.staff-profile.index', $data);
        }
    }

    public function update_admin_info(Request $request)
    {
        if (Auth::guard('web')->check()) {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'mobile' => 'required|numeric|unique:admin,mobile,' . Auth::guard('web')->user()->id,
            ]);

            $admin = Admin::find(Auth::guard('web')->user()->id);
            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->mobile = $request->mobile;
            $admin->save();

            return back()->withSuccess('You Have Successfully Update Your Profile.');
        } else {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:user,email,' . Auth::guard('member')->user()->id,
                'country' => 'required',
                'country_code' => 'required',
                'phone' => 'required|numeric|unique:user,phone,' . Auth::guard('member')->user()->id,
            ]);

            $admin = Staff::find(Auth::guard('member')->user()->id);
            $admin->first_name = $request->first_name;
            $admin->last_name = $request->last_name;
            $admin->email = $request->email;
            $admin->country = $request->country;

            if ($request->phone) {
                $country_code = str_starts_with($request->country_code, '+') ?  $request->country_code : '+' . $request->country_code;
                $admin->phone = $request->phone;
                $admin->country_code = $country_code;
            }
            $admin->save();
            return back()->withSuccess('You Have Successfully Update Your Profile.');
        }
    }

    public function update_admin_password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchAdminOldPassword],
            'password' => 'required|same:confirm_password',
            'confirm_password' => 'required_with:password|same:password|min:6'
        ]);

        if (Auth::guard('web')->check()) {
            $admin = Admin::find(Auth::guard('web')->user()->id);
            $admin->password = Hash::make($request->password);
            $admin->save();
            return back()->withSuccess("Password changed successfully!");
        } else {
            $admin = Staff::find(Auth::guard('member')->user()->id);
            $admin->password = Hash::make($request->password);
            $admin->plain_password = $request->password;
            $admin->save();
            return back()->withSuccess("Password changed successfully!");
        }
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image = $request->image;
        if (Auth::guard('web')->check()) {
            if (!empty($image)) {
                $ext = $image->getClientOriginalExtension();
                $newName = 'admin' . '.' . $ext;
                $admin = Admin::find(Auth::guard('web')->user()->id);
                if ($admin->pro_img != "") {
                    File::delete(public_path('/admin-assets/assets/img/profile_img/admin/' . $admin->pro_img));
                }
                $path = public_path('/admin-assets/assets/img/profile_img/admin/');
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $manager = new ImageManager(new Driver());
                $image = $manager->read($image);
                $image = $image->resize(200,200);
                $image->toJpeg(80)->save($path.$newName);

                // $request->image->move($path, $newName);
                $admin->pro_img = $newName;

                $admin->save();

                return back()->withSuccess('Image has been updated successfully');
            }
        } else {
            if (!empty($image)) {
                $ext = $image->getClientOriginalExtension();
                $newName = time() . '.' . $ext;
                // $image->move(public_path() . '/admin-assets/assets/img/profile_img/admin', $newName);
                $admin = Staff::find(Auth::guard('member')->user()->id);

                if ($admin->image != "") {
                    File::delete(public_path('/admin-assets/assets/img/profile_img/staff/' . $admin->image));
                }
                $path = public_path('/admin-assets/assets/img/profile_img/staff/');
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                $manager = new ImageManager(new Driver());
                $image = $manager->read($image);
                $image = $image->resize(200,200);
                $image->toJpeg(80)->save($path.$newName);

                // $request->image->move($path, $newName);
                $admin->image = $newName;
                $admin->save();

                return redirect('/profile')->withSuccess('Image has been updated successfully');
            }
        }
    }

    public function all_notifications()
    {
        $isWebUser = Auth::guard('web')->check();
        $userId = $isWebUser ? null : Auth::guard('member')->user()->id;

        if ($isWebUser) {
            $data['notifications'] = DB::table('other_notification_list')
                ->orderBy('id', 'desc')
                ->paginate(16);
        } else {
            $notifications = DB::table('other_notification_list')
                ->orderBy('id', 'desc')
                ->get();

            $filteredNotifications = [];
            foreach ($notifications as $notification) {
                $recived_id = json_decode($notification->other_recive_notification_id, true);

                if (is_array($recived_id) && in_array($userId, $recived_id)) {
                    $filteredNotifications[] = $notification;
                } elseif (empty($recived_id)) {
                    $filteredNotifications[] = $notification;
                }
            }

            // To make it work with pagination, use the Paginator
            $filteredNotificationsCollection = collect($filteredNotifications);
            // $data['notifications'] = new \Illuminate\Pagination\LengthAwarePaginator(
            //     $filteredNotificationsCollection->forPage(request()->get('page', 1), 16),
            //     $filteredNotificationsCollection->count(),
            //     16,
            //     request()->get('page', 1),
            //     ['path' => url()->current()]
            // );
            $data['notifications'] = new \Illuminate\Pagination\LengthAwarePaginator(
                $filteredNotificationsCollection->forPage(Paginator::resolveCurrentPage(), 16),
                $filteredNotificationsCollection->count(),
                16,
                Paginator::resolveCurrentPage(),
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        return view('admin.notification', $data);
    }

    public function delete_notifications(Request $request)
    {
        $notificationIds = $request->input('notification_ids');
    
        if (empty($notificationIds)) {
            return back()->with('error', 'No notifications selected for deletion.');
        }
    
        $deleted = DB::table('other_notification_list')->whereIn('id', $notificationIds)->delete();
    
        if ($deleted) {
            return back()->withSuccess('Selected notifications have been deleted successfully.');
        } else {
            return back()->with('error', 'Failed to delete selected notifications.');
        }
    }
    
}
