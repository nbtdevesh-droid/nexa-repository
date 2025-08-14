<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\CouponMail;
class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'coupon_type',
        'product_id',
        'category_id',
        'user_id',
        'amount_type',
        'amount',
        'product_min_amount',
        'max_uses',
        'remain_uses',
        'status',
        'coupon_start_date',
        'coupon_end_date'
    ];

    function InsertData($request, $coupon = null)
    {
        // dd($request);
        if (!$coupon) {
            $coupon = new Coupon();
        }
        $coupon->code = $request->code;
        $coupon->title = $request->coupon_title;
        $coupon->coupon_type = $request->type;

        $coupon->user_id = !empty($request->user_id) ? json_encode($request->user_id) : '';

        $coupon->amount_type = $request->amount_type;
        $coupon->amount = $request->amount;
        $coupon->product_min_amount = $request->min_amount;
        $coupon->remain_uses = $request->max_uses;
        $coupon->status = $request->status ?? 1;
        $coupon->main_category = $request->parent_Category;

        // Ensure date fields are valid before saving
        $coupon->coupon_start_date = $request->start_at ? date('Y-m-d H:i:s', strtotime($request->start_at)) : null;
        $coupon->coupon_end_date = $request->expire_at ? date('Y-m-d H:i:s', strtotime($request->expire_at)) : null;        

        if ($request->type == 'category_wise') {
            $coupon->sub_category = null;
            $coupon->product_id = null;

            $coupon->main_category = $request->category_wise;
            $coupon->category_id = json_encode($request->discount_select);

            if ($request->update == 'update') {
                $coupon->main_category = $request->parent_category;
                $coupon->category_id = json_encode($request->child_Category);
            }
        }

        if ($request->type == 'product_wise') {
            $coupon->category_id = null;
            $coupon->sub_category = null;
            $coupon->main_category = null;

            // $coupon->main_category = $request->category_wise;
            // $coupon->sub_category = $request->subcategory_select;
            // $coupon->product_id = json_encode($request->discount_select);

            // if ($request->update == 'update') {
            //     $coupon->main_category = $request->parent_category;
            //     if (is_array($request->child_Category)) {
            //         $coupon->sub_category = (int)$request->child_Category[0];
            //     } else {
            //         $coupon->sub_category = (int)$request->child_Category;
            //     }
            //     $coupon->product_id = json_encode($request->product_id);
            // }
        }

        // ----------------------------------------
        if ($request->type == 'user_wise') {
            $all_user = is_array($request->user_id) ? $request->user_id : [];
            if (!empty($all_user)) {
                foreach ($all_user as $vs) {
                    // dd($coupon);
                    $send_details = [
                        'coupon_code' => $request->code,
                        'coupon_title' => $request->coupon_title,
                        'amount' => $request->amount,
                    ];

                    $user_details = DB::table('user')->where('id', $vs)->first();
                    Mail::to($user_details->email)->send(new CouponMail($send_details));
                }
            }
        }
        // ----------------------------------------

        // dd($coupon);
        $coupon->save();

        if ($coupon->id > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function subcategory()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
