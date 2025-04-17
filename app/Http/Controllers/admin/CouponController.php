<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public $coupon;

    public function __construct(Coupon $coupon, Product $product)
    {
        $this->coupon = $coupon;
        $this->product = $product;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['coupons'] = $this->coupon->orderBy('created_at', 'desc')->get();
        return view('admin.coupon.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['products'] = $this->product->orderBy('product_name', 'asc')->get();
        $data['categories'] = Category::where('parent_id', null)->orderBy('category_name', 'asc')->get();
        $data['users'] = User::orderBy('first_name', 'asc')->where('first_name', '!=', '')->get();
        return view('admin.coupon.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   

        $data = $this->coupon->InsertData($request);
        
        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Coupon Added Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Coupon Added Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coupon = $this->coupon->find($id);
        if ($coupon->status == '1') {
            $status = '0';
        } else {
            $status = '1';
        }

        $coupon->status = $status;
        $coupon->save();

        if ($coupon->status != 1) {
            return back()->with('success', 'Coupon Inactive Successfully.');
        }
        return back()->with('success', 'Coupon Active Successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['products'] = $this->product->orderBy('product_name', 'asc')->get();
        $data['categories'] = Category::where('parent_id', null)->orderBy('category_name', 'asc')->get();
        $data['users'] = User::orderBy('first_name', 'asc')->get();
        $data['coupon'] = $this->coupon->find($id);
        return view('admin.coupon.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $coupon = $this->coupon->find($id);
        $data = $this->coupon->InsertData($request, $coupon);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Coupon Updated Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Coupon Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->coupon->find($id);

        if (!$data) {
            return redirect()->route('coupon.index')->with('error', 'Coupon Delete Failed');
        }
        $data->delete();
        return redirect()->route('coupon.index')->withSuccess('Coupon Delete Successfully');
    }

    public function getDiscountWise(Request $request)
    {
        $discount_wise = $request->discount_wise;

        if ($discount_wise == 'category_wise' || $discount_wise == 'product_wise') {
            $categories = Category::where('parent_id', null)->orderBy('category_name', 'asc')->get();
            return response()->json(['data' => $categories]);
        }
    }

    public function getCategoryWise(Request $request)
    {
        // dd($request->all());
        $category_id = $request->category_wise;
        $discount_wise = $request->discount_wise;

        if ($discount_wise == 'category_wise' || $discount_wise == 'product_wise') {
            $categories = Category::where('parent_id', $category_id)->orderBy('category_name', 'asc')->get();
            return response()->json(['discount_wise' => $discount_wise, 'categories' => $categories]);
        }
    }

    public function getSubcategoryWise(Request $request)
    {
        $subcategory_id = $request->subcategory_wise;
        $discount_wise = $request->discount_wise;

        $products = Product::where('child_category', $subcategory_id)->get();
        return response()->json(['product_data' => $products->pluck('product_name', 'id')]);
    }

    // -------------------------  for edit -------------
    public function getSubcategories(Request $request)
    {
        $subcategories = Category::where('parent_id', $request->category_id)->get();
        return response()->json($subcategories);
    }
    public function getProducts(Request $request)
    {
        $products = Product::where('child_category', $request->subcategory_id)->get();
        return response()->json($products);
    }
    // -------------------------  for edit -------------//
}
