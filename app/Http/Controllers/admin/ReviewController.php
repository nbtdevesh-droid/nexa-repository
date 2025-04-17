<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{ 
    public function allReview()
    {
        $data['all_reviews'] = Review::orderBy('id', 'desc')->paginate(10);

        return view('admin/Review/all_review', $data);
    }

    public function addReview()
    {
        $data['customers'] = User::whereNotNull('first_name')->where('status', 1)->orderBy('id', 'desc')->get();
        $data['products'] = Product::where('status', 1)->orderBy('id', 'desc')->get();
        return view('admin/Review/add_review', $data);
    }

    public function ReviewStore(Request $request)
    {
        // Retrieve customer details
        $customer = User::select('first_name', 'last_name')->where('id', $request->customer_id)->first();

        // Ensure last_name is not null, otherwise use an empty string
        $fullName = $customer ? trim($customer->first_name . ' ' . ($customer->last_name ?? '')) : 'Unknown';

        // Store review
        $review = new Review();
        $review->user_id = $request->customer_id;
        $review->product_id = $request->product_id;
        $review->user_name = $fullName;
        $review->rating = $request->rating_value;
        $review->description = $request->review;

        if ($review->save()) {
            return response()->json(['status' => true, 'message' => 'Review Added Successfully.']);
        } else {
            return response()->json(['status' => false, 'message' => 'Review Addition Failed.']);
        }
    }
    

    public function ReviewDelete(Request $request, $id)
    {
        $review = Review::find($id);
        
        if(!$review){
            return back()->with('error', 'Review Not Found.');
        }

        $review->delete();

        return back()->withSuccess('Review Delete Successfully.');
    }
}