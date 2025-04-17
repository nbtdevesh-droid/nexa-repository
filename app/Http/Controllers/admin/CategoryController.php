<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = $this->category->latest('id');
            // $categories = $this->category;

            if ($request->has('keyword') && $request->keyword != "") {
                $keyword = $request->keyword;
                // $categories = $categories->where('category_name', 'like', '%' . $keyword . '%');
                $categories = $categories->where(function($query) use ($keyword) {
                    $query->where('category_name', 'like', '%' . $keyword . '%')->orWhereHas('parent', function($query) use ($keyword) {
                              $query->where('category_name', 'like', '%' . $keyword . '%');
                          });
                });
            }

            // Load parent category relationships to be used in AJAX response
            $categories = $categories->with('parent')->paginate(10);

            // return response()->json($categories);
            return response()->json([
                'data' => $categories->items(),
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'last_page' => $categories->lastPage(),
                'links' => (string) $categories->links()->render()  // Render pagination links as HTML
            ]);
        }
        $categoriess =  $this->category->where('parent_id', null)->orderBy('category_name', 'asc')->get();
        $categories = $this->category->orderBy('id', 'desc')->paginate(10);
        // $categories = $this->category->paginate(10);
        return view('admin.category.index', compact('categories', 'categoriess'));
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
        $data = $this->category->InsertData($request);

        if($data != 1){
            return back()->with('error', 'Category Added Failed');
        }
        return back()->withSuccess('Category Added Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function unique_category_name(Request $request)
    {
        if ($request->existance_type === 'category_name') {
            $query = Category::where('category_name', $request->category_name);
    
            if (!empty($request->category_id)) {
                $query->where('id', '!=', $request->category_id);
            }
    
            $categoryExists = $query->exists();
    
            return response()->json(['valid' => !$categoryExists]);
        }
    
        return response()->json(['valid' => false]);
    }

    public function unique_category_order(Request $request)
    {
        $parentId = $request->input('parent_id');
        $categoryOrder = $request->input('category_order');
        $formId = $request->input('form_id');

        $query = Category::where('category_order', $categoryOrder);
        if (empty($parentId)) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentId);
        }
        if (!empty($formId)) {
            $query->where('id', '<>', $formId);
        }
        $isUnique = !$query->exists();
        return response()->json(['is_unique' => $isUnique]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $formdata = $request->all();
        $category = $this->category->find($id);

        // Check if category has associated products before making it inactive
        if ($category->id && $request->category_status == 0) { // If updating and status is being set to inactive
            $productExists = Product::where('parent_category', $category->id)
                ->orWhere('child_category', $category->id)
                ->exists(); 

            if ($productExists) {
                return back()->with('error', 'This category or subcategory cannot be inactivated because it has associated products.');
            }
        }

        $data = $this->category->InsertData($request, $category);

        if($data != 1){
            return back()->with('error', 'Category Update Failed');
        }
        return back()->withSuccess('Category Update Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = $this->category->find($id);

        if (!$category) {
            return back()->with('error', 'Category Delete Failed - Category not found.');
        }

        // Check if category has associated products before deleting
        $productExists = Product::where('parent_category', $id)->orWhere('child_category', $id)->exists();

        if ($productExists) {
            return back()->with('error', 'This category or subcategory cannot be deleted because it has associated products.');
        }

        // Delete category image if it exists
        if($category->image){
            File::delete(public_path('/admin-assets/assets/img/category/' . $category->image));
        }

        if($category->banner_image != null){
            File::delete(public_path('/admin-assets/assets/img/category_banner_image/' . $category->banner_image));
        }

        // Delete the category
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }

}
