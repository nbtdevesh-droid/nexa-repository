<?php 
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\BankDetails;
use App\Models\Product;
use App\Models\ProductClickCount;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountSettingApiController extends Controller
{
    public function account_change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        }

        $user = auth('sanctum')->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => 'failed', 'message' => 'The current password is incorrect.']);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return a success message
        return response()->json(['message' => 'Password updated successfully.']);
    }

    public function notification_setting(Request $request)
    {
        $user = auth('sanctum')->user();

        if($request->notification_status != ''){
            $user->notification_status = $request->notification_status;
            $user->save();
        }
        return response()->json(['status' => 'success', 'message' => 'Notification status update successfully.', 'notification_status' => $user->notification_status]);
    }
    
    public function getProductViewCount(Request $request)
    {
        $user = auth('sanctum')->user();
        $product = Product::findOrFail($request->product_id);

        $existingViewClick = ProductClickCount::where('user_id', $user->id)->where('product_id', $product->id)->first();

        if (!$existingViewClick) {
            ProductClickCount::insert([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'count' => '1'
            ]);

            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'success']);
    }

    public function app_setting()
    {
        $settings = DB::table('app_settings')->select('id', 'maintenance_setting', 'update_setting')->first();

        return response()->json(['status' => 'success', 'app_setting' => $settings]);
    }
}
