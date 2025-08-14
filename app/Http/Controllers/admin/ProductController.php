<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Rules\SalePriceLessThanRegularPrice;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use App\Traits\Common_trait;

class ProductController extends Controller
{ 
    use Common_trait;
    public $product;

    public function __construct(Product $product, Category $category, Brand $brand)
    {
        $this->product = $product;
        $this->category = $category;
        $this->brand = $brand;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $isWebUser = Auth::guard('web')->check();
        $userId = $isWebUser ? null : Auth::guard('member')->user()->id;

        if ($request->ajax()) {
            $products = $this->product->query();

            if ($request->has('keyword') && $request->keyword != "") {
                $keyword = $request->keyword;

                if (preg_match('/^#\d{7}$/', $keyword)) {
                    // Remove the '#' and leading zeros to get the actual ID
                    $id = ltrim($keyword, '#0');
                    $products = $products->where('id', $id);
                } else {
                    $products = $products->where(function ($query) use ($keyword) {
                        $query->where('sku', 'like', '%' . $keyword . '%')
                              ->orWhere('product_name', 'like', '%' . $keyword . '%');
                    });
                }
            }

            if ($request->has('category_id') && $request->category_id != "") {
                $category_id = $request->category_id;
                $products = $products->where('parent_category', $category_id)->orWhere('child_category', $category_id)->withCount('productClickCounts as total_click_count');
            }

            if (!$isWebUser) {
                $products = $products->where('user_id', $userId);
            }

            $products = $products->orderBy('id', 'desc')->withCount('productClickCounts as total_click_count')->paginate(10);
            // if ($request->ajax()) {
                return response()->json([
                    'data' => $products->items(),
                    'links' => (string) $products->links()->render(),
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                ]);
            // }
        }

        $categories = $this->category->whereNull('parent_id')->orderBy('category_name')->whereStatus(1)->get();

        if ($isWebUser) {
            $products = $this->product->orderBy('id', 'desc')->withCount('productClickCounts as total_click_count')->paginate(10);
        } else {
            $products = $this->product->where('user_id', $userId)->orderBy('id', 'desc')->withCount('productClickCounts as total_click_count')->paginate(10);
        }

        return view('admin.product.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['categories'] = $this->category->whereNull('parent_id')->orderBy('category_name')->whereStatus(1)->get();
        $data['brands'] = $this->brand->orderBy('brand_name')->whereStatus(1)->get();
        return view('admin.product.add_product', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->product->insertId($request);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Product Added Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Product Added Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['product'] = $this->product->find($id);
        $data['categories'] = $this->category->whereNull('parent_id')->orderBy('category_name')->whereStatus(1)->get();
        $data['brands'] = $this->brand->orderBy('brand_name')->whereStatus(1)->get();
        return view('admin.product.edit_product', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = $this->product->find($id);
        $data = $this->product->insertId($request, $product);

        if ($data != 1) {
            return response()->json(['success' => false, 'message' => 'Product Updated Failed']);
        }
        return response()->json(['success' => true, 'message' => 'Product Updated Successfully']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->product->DeleteData($id);

        if ($data == 1) {
            return back()->withSuccess('Product Delete Successfully');
        } else {
            return back()->with('Product Delete Failed');
        }
    }

    /********************************** get child category ********************************/
    public function get_child_category(Request $request){
        $parent_id = $request['parent_id'];
        $sub_category = $this->category->where('parent_id', $parent_id)->whereStatus(1)->get();
        
        if (!$sub_category) {
            return response()->json(['status' => 'failed', 'data' => '']);
        }
        return response()->json(['status' => 'success', 'data' => $sub_category]);
    }

    /************************* summernote image upload in folder **************************/
    public function summernoteimgupload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = rand() . '.' . $file->getClientOriginalExtension();
            $path = 'admin-assets/summernote/' . $filename; // Store path
    
            $file->move(public_path('admin-assets/summernote'), $filename);
    
            return response()->json(asset($path));
        }
    
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /***************************** product bulk delete *********************************/
    public function bulkDelete(Request $request){
        $productIds = $request->input('product_ids');

        if (empty($productIds)) {
            return response()->json(['success' => false, 'message' => 'No products selected for deletion.']);
        }

        // Delete products by IDs
        Product::whereIn('id', $productIds)->delete();

        return response()->json(['success' => true, 'message' => 'Selected products have been deleted successfully.']);
    }

    /******************************** product csv import ********************************/
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]); 
        set_time_limit(300); 
        $file = $request->file('file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray(); 
        $headers = [
            'product_name', 'description', 'parent_category', 'child_category',
            'brand_id', 'quantity', 'min_order', 'regular_price', 'sale_price',
            'sku', 'feature_image', 'status', 'flash_deal'
        ];

        $errors = [];
        $successCount = 0;

        foreach ($rows as $index => $row) {
            if ($index === 0) continue;

            $data = array_combine($headers, $row);
            
            // Validate the data
            $validator = Validator::make($data, [
                'product_name'   => 'required|string|max:255',
                'description'=>    'required', 
                'parent_category'=> 'required|exists:categories,id',
                'child_category' => 'nullable|exists:categories,id',
                'sku'            => 'required',
                'quantity'       => 'required|integer|min:1',
                'regular_price'  => 'required|numeric|min:0',
                'min_order'      => 'required|integer|min:1',
                'feature_image'  => 'required',
                'status' => 'nullable|in:0,1',
                'flash_deal' => 'nullable|in:0,1',
            ]);

            if ($validator->fails()) {
                $errors[$index + 1] = $validator->errors()->all();  
                continue;
            }

            // Check if parent and child categories have status = 1
            $parentCategory = $this->category->where('id', $data['parent_category'])->where('status', 1)->first();
            if (!$parentCategory) {
                $errors[$index + 1][] = "Parent category ID {$data['parent_category']} is inactive.";
                continue;
            }

            if (!empty($data['child_category'])) {
                $childCategory = $this->category->where('id', $data['child_category'])->where('status', 1)->first();
                if (!$childCategory) {
                    $errors[$index + 1][] = "Child category ID {$data['child_category']} is inactive.";
                    continue;
                }
            }

            $data['slug'] = $this->create_unique_slug($data['product_name'],'products', 'slug'); 
            $data['status'] = $data['status'] === null ? 0 : 1;
            $data['flash_deal'] = $data['flash_deal'] === null ? 0 : 1;
            Product::create($data);
            $successCount++;
        }



        if (!empty($errors)) {
            return redirect()->back()
                ->withErrors($errors)  
                ->with('error', 'Some rows failed validation. See the errors below.');
        }

        return redirect()->back()->with('success', "$successCount products imported successfully!");
    }
}
