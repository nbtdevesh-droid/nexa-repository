<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $brands = $this->brand->latest('id');

            if ($request->has('keyword') && $request->keyword != "") {
                $keyword = $request->keyword;
                $brands = $brands->where('brand_name', 'like', '%' . $keyword . '%');
            }
            $brands = $brands->paginate(10);

            // return response()->json($categories);
            return response()->json([
                'data' => $brands->items(),
                'current_page' => $brands->currentPage(),
                'per_page' => $brands->perPage(),
                'total' => $brands->total(),
                'last_page' => $brands->lastPage(),
                'links' => (string) $brands->links()->render()  // Render pagination links as HTML
            ]);
        }

        $brands = $this->brand->orderBy('id', 'desc')->paginate(10); // Adjust the pagination limit as needed
        return view('admin.brand.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $formdata = $request->all();
        $data = $this->brand->InsertData($request);

        if($data != 1){
            return back()->with('error','Brand Added Failed');
        }
        return back()->withSuccess('Brand Added Successfully');
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
        //
    }

    public function unique_brand_name(Request $request){
        $formdata = $request->all();
        if($request['existance_type'] == 'brand_name'){
            $brand = $this->brand->where('brand_name', '=', $request['brand_name'])->count();
            if($brand == 0){
                 echo "true";
            } else {
                echo "false";
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $formdata = $request->all();
        // dd($formdata);
        $brand = $this->brand->find($id);

        // Check if brand has associated products before making it inactive
        if ($brand->id && $request->category_status == 0) { // If updating and status is being set to inactive
            $productExists = Product::where('brand_id', $brand->id)->exists(); 

            if ($productExists) {
                return back()->with('error', 'This brand cannot be inactivated because it has associated products.');
            }
        }
        
        $data = $this->brand->InsertData($request, $brand);

        if($data != 1){
            return back()->with('error','Brand Updated Failed');
        }
        return back()->withSuccess('Brand Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = $this->brand->find($id);
        if(!$data){
            return back()->with('error', 'Brand Deleted Failed');
        }

        // Check if brand has associated products before making it inactive
        if ($data->id) {
            $productExists = Product::where('brand_id', $data->id)->exists(); 

            if ($productExists) {
                return back()->with('error', 'This brand cannot be inactivated because it has associated products.');
            }
        }

        $data->delete();
        return back()->withSuccess('Brand Deleted Successfully');
    }
}
