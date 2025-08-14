<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\Review;
use App\Models\ReviewHelpful;
use App\Models\Order;
use App\Models\Coupon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class HomeApiController extends Controller
{
    public function getProfileDetail()
    {
        $profile = User::select('id', 'first_name', 'last_name', 'email', 'country', 'country_code', 'phone', 'image')->whereId(auth('sanctum')->user()->id)->first();
        $profile->image = !empty($profile->image) ? $this->getImageUrl('profile_img/user', $profile->image) : $this->getImageUrl('profile_img/user', 'common.png');

        return response()->json([
            'status' => 'success',
            'profile' => $profile
        ]);
    }

    public function UpdateProfileDetail(Request $request)
    {
        $user = auth('sanctum')->user()->id;

        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'country' => 'required',
        ];

        if ($request->country_code || $request->phone) {
            $rules['country_code'] = 'required_with:phone';
            $rules['phone'] =
                [
                    'required',
                    'numeric',
                    Rule::unique('user', 'phone')->where(function ($query) use ($request) {
                        return $query->where('country_code', $request->country_code);
                    })->ignore($user),
                ];
        }

        if ($request->email) {
            $rules['email'] = 'required|email|unique:user,email,' . $user;
        }

        if ($request->password) {
            $rules['password'] = 'required|min:6';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first(), 'current_step' => '']);
        } else {
            $profile = User::whereId($user)->first();

            if ($profile) {
                $profile->first_name = $request->first_name;
                $profile->last_name = $request->last_name;
                if ($request->email) {
                    $profile->email = $request->email;
                }
                $profile->country = $request->country;
                if ($request->country_code) {
                    $profile->country_code = $request->country_code;
                }
                if ($request->phone) {
                    $profile->phone = $request->phone;
                }

                if ($request->password != null ) {
                    $profile->password = Hash::make($request->password);
                }

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;

                    $path = public_path('/admin-assets/assets/img/profile_img/user/');

                    $manager = new ImageManager(new Driver());
                    $image = $manager->read($request->file('image'));
                    $image = $image->resize(200, 200);
                    $image->toJpeg(80)->save($path . $filename);
                    // $file->move(public_path('/admin-assets/assets/img/profile_img/user/'), $filename);

                    if ($profile->image) {
                        File::delete(public_path('/admin-assets/assets/img/profile_img/user/' . $profile->image));
                    }
                    $profile->image = $filename;
                }

                $mailData = [
                    'full_name' => $request->first_name . ' ' . $request->last_name,
                    'email' => $request->email
                ];

                if ($profile->current_steps == 'step_1') {
                    if ($request->email) {
                        Mail::send('admin.email.welcome_mail', ['data' => $mailData], function ($message) use ($mailData) {
                            $message->to($mailData['email']);
                            $message->subject('Welcome to NEXA');
                        });
                    }
                }
                $profile->current_steps = 'step_2';

                $profile->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Profile update successful',
                    'current_step' => 'step_2'
                ]);
            }
        }
    }

    public function HomePage(Request $request)
    {
        // return response()->json(['message' => 'Resource not found'], 404);

        // dd('ok');

        $user = auth('sanctum')->user(); 
        $search_keyword = $request->input('search_keyword');

        if($user){
            $wishlistProductIds = $user->wishlist ? $user->wishlist->pluck('product_id')->toArray() : [];
        }

        if ($search_keyword) {
            $user = auth('sanctum')->user();
            $products = Product::select('id', 'product_name', 'feature_image', 'quantity as available_quantity', 'regular_price', 'sale_price')->whereStatus(1)->where('product_name', 'like', '%' . $search_keyword . '%')->orderBy('id', 'desc');

            // Check if the search keyword matches a category name
            $category = Category::where('category_name', 'like', '%' . $search_keyword . '%')->with('subcategory')->first();
            if ($category) {
                $products->orWhere('parent_category', $category->id)->orWhere('child_category', $category->id);
            }

            $products = $products->orderBy('id', 'desc')->paginate(16);

            if ($products->isEmpty()) {
                return Response::json(['status' => 'failed', 'data' => []]);
            }

            if($user){
                $this->processProducts($products, $wishlistProductIds);
            }

            return Response::json(['status' => 'success', 'data' => $products]);
        } else {
            //home banner
            $banners = DB::table('home_banners')->orderBy('created_at', 'desc')->get(['id', 'image']);

            if ($banners->isEmpty()) {
                $banners = [];
            }

            $banners->each(fn($banner) => $banner->image = $this->getImageUrl('home_banner', $banner->image));

            //Home Icon
            $home_icon = DB::table('home_icon')->orderBy('created_at', 'desc')->get(['id', 'icon', 'link']);

            if ($home_icon->isEmpty()) {
                $home_icon = [];
            }

            $home_icon->each(fn($home_icon) => $home_icon->icon = $this->getImageUrl('Home_icon', $home_icon->icon));

            //all categories
            $categories = Category::whereStatus(1)->whereNull('parent_id')->orderBy('category_order', 'asc')->get(['id', 'category_name', 'image']);
            if ($categories->isEmpty()) {
                $categories = [];
            }
            $categories->each(fn($category) => $category->image = $this->getImageUrl('category', $category->image));

            // Fetch flash deals
            $deal = DB::table('product_flash_deals')->where('start_flash_deal', '<=', now())->where('end_flash_deal', '>=', now())->orderBy('start_flash_deal', 'desc')->get();
            // dd($deal);
            $flash_deal_time = [
                'start_time' => $deal[0]->start_flash_deal ?? '',
                'end_time' => $deal[0]->end_flash_deal ?? ''
            ];

            $flash_deals = Product::where('flash_deal', 1)->whereStatus(1)->orderBy('id', 'desc')->take(4)->get(['id', 'product_name', 'quantity as available_quantity', 'feature_image', 'regular_price', 'sale_price', 'flash_deal'])->makeHidden('formatted_id');

            if ($flash_deals->isEmpty()) {
                $flash_deals = [];
            }

            foreach ($flash_deals as $flash_deal) {
                $regular_price = (float)($flash_deal->regular_price ?? 0);
                $sale_price = (float)($flash_deal->sale_price ?? 0);

                $flash_deal->discount_percent = ($regular_price > 0 && $sale_price > 0)
                    ? round((($regular_price - $sale_price) / $regular_price) * 100)
                    : 0;

                $flash_deal->feature_image = $this->getImageUrl('product/feature_img', $flash_deal->feature_image);
                $flash_deal->sold_out = $this->flashDealsoldOut($flash_deal->id);
            }
            //all products
            $products = Product::whereStatus(1)->orderBy('id', 'desc')->take(50)->get(['id', 'product_name', 'quantity as available_quantity', 'feature_image', 'regular_price', 'sale_price'])->makeHidden('formatted_id');

            if ($products->isEmpty()) {
                $products = [];
            }

            if($user){
                $wishlistProductIds = $user->wishlist ? $user->wishlist->pluck('product_id')->toArray() : [];
                $this->processProducts($products, $wishlistProductIds);
            }else{
                $wishlistProductIds = '';
                $this->processProducts($products, $wishlistProductIds);
            }

            return Response::json(['status' => 'success', 'home_banner' => $banners, 'home_icon' => $home_icon, 'categories' => $categories, 'flash_deal_time' => $flash_deal_time, 'flash_deals' => $flash_deals, 'products' => $products]);
        }
    }

    public function flash_deal(Request $request)
    {
        $perPage = 10;
        $page = $request->input('page', 1);

        // Fetch flash deals
        $deal = DB::table('product_flash_deals')->where('start_flash_deal', '<=', now())->where('end_flash_deal', '>=', now())->orderBy('start_flash_deal', 'desc')->get();

        if($deal->isNotEmpty()){
            // Get the paginated flash deals
            $flash_deals = Product::where('flash_deal', 1)
                ->whereStatus(1)
                ->orderBy('id', 'desc')
                ->paginate($perPage, ['id', 'product_name', 'quantity as available_quantity', 'feature_image', 'regular_price', 'sale_price', 'flash_deal'], 'page', $page);
    
            $flash_deals->getCollection()->transform(function ($flash_deal) {
                $regular_price = (float)($flash_deal->regular_price ?? 0);
                $sale_price = (float)($flash_deal->sale_price ?? 0);
    
                $flash_deal->discount_percent = ($regular_price > 0 && $sale_price > 0)
                    ? round((($regular_price - $sale_price) / $regular_price) * 100)
                    : 0;
    
                $flash_deal->feature_image = $this->getImageUrl('product/feature_img', $flash_deal->feature_image);
                $flash_deal->sold_out = $this->soldOut($flash_deal->id);
    
                return $flash_deal;
            });
    
            return response()->json(['status' => 'success', 'flash_deals' => $flash_deals]);
        }else{
            return response()->json(['status' => 'success', 'flash_deals' => (object)[]]);
        }
    }

    public function category_wise_product(Request $request)
    {
        $user = auth('sanctum')->user();
        $search_keyword = $request->input('search_keyword');
        $cat_id = $request->input('category_id');

        if($user){
            $wishlistProductIds = $user->wishlist ? $user->wishlist->pluck('product_id')->toArray() : [];
        }

        // Fetch all categories with their subcategories
        $categoriesQuery = Category::select('id', 'category_name', 'image', 'banner_image')->whereNull('parent_id')->whereStatus(1)->with('subcategory')->orderBy('category_order', 'asc');

        $categories = $categoriesQuery->get();

        if ($categories->isEmpty()) {
            return Response::json(['status' => 'failed', 'categories' => []]);
        }

        foreach ($categories as $category) {
            $category->image = $this->getImageUrl('category', $category->image);
            $category->banner_image = $category->banner_image ? $this->getImageUrl('category_banner_image', $category->banner_image) : null;
            $category->search = false;

            // Fetch products for this category
            $mainCategoryProductsQuery = Product::select(['id', 'product_name', 'quantity as available_quantity', 'feature_image', 'regular_price', 'sale_price'])->where('parent_category', $category->id)->whereStatus(1)->orderBy('id', 'desc');

            // Apply search filter if provided
            if (!empty($cat_id) && $search_keyword) {
                $mainCategoryProductsQuery->where('product_name', 'LIKE', '%' . $search_keyword . '%');
            }

            $mainCategoryProducts = $mainCategoryProductsQuery->get();

            foreach ($mainCategoryProducts as $product) {
                $product->feature_image = $this->getImageUrl('product/feature_img', $product->feature_image);
                if($user){
                    $product->in_wishlist = in_array($product->id, $wishlistProductIds);
                }

                $regular_price = (float)($product->regular_price ?? 0);
                $sale_price = (float)($product->sale_price ?? 0);

                if ($regular_price > 0 && $sale_price > 0) {
                    $discountPercentage = (($regular_price - $sale_price) / $regular_price) * 100;
                    $product->discount_percent = round($discountPercentage);
                } else {
                    $product->discount_percent = 0;
                }

                $product->loading = false;
                $product->sold_out = $this->soldOut($product->id);

                $averageRating = $product->reviews()->avg('rating');
                $product->average_rating = $averageRating ?: 0;
                $product->makeHidden(['formatted_id']);
            }

            // If no category_id is provided, paginate each category's products
            if (!$cat_id || $category->id == $cat_id) {
                $perPage = 10; // Set the number of items per page
                $page = $request->input('page', 1); // Current page or default to 1
                $offset = ($page - 1) * $perPage;
                $paginatedProducts = $mainCategoryProducts->slice($offset, $perPage)->values();

                // Create pagination metadata
                $pagination = new LengthAwarePaginator(
                    $paginatedProducts,
                    $mainCategoryProducts->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                // Attach paginated products to the category
                $category->products = $pagination;
            }
            if (!empty($cat_id) && $category->id != $cat_id) {
                $category->products = null;
            }

            foreach ($category->subcategory as $subcategory) {
                $subcategory->image = $this->getImageUrl('category', $subcategory->image);
            }
        }

        return Response::json(['status' => 'success', 'categories' => $categories]);
    }

    public function GetAllCategory()
    {
        $user = auth('sanctum')->user();

        // Fetch all categories with their subcategories
        $categoriesQuery = Category::select('id', 'category_name', 'image', 'banner_image')->whereNull('parent_id')->whereStatus(1)->orderBy('category_order', 'asc');

        $categories = $categoriesQuery->get();

        if ($categories->isEmpty()) {
            return Response::json(['status' => 'failed', 'categories' => []]);
        }

        foreach ($categories as $category) {
            $category->image = $this->getImageUrl('category', $category->image);
            $category->banner_image = $category->banner_image ? $this->getImageUrl('category_banner_image', $category->banner_image) : null;
        }

        return Response::json(['status' => 'success', 'categories' => $categories]);
    }

    public function CategorySubcategoryOrProduct(Request $request)
    {
        $user = auth('sanctum')->user();
        $search_keyword = $request->input('search_keyword');
        $cat_id = $request->input('category_id');

        if($user){
            $wishlistProductIds = $user->wishlist ? $user->wishlist->pluck('product_id')->toArray() : [];
        }
    
        // Fetch category along with subcategories
        $category = Category::select('id', 'category_name')->whereNull('parent_id')->whereStatus(1)->with('subcategory')->orderBy('category_order', 'asc')->where('id', $cat_id)->first();
    
        if (!$category) {
            return Response::json(['status' => 'failed', 'message' => 'Category not found']);
        }
    
        $category->search = false;
    
        // Fetch products for this category
        $mainCategoryProductsQuery = Product::select(['id', 'product_name', 'quantity as available_quantity', 'feature_image', 'regular_price', 'sale_price'])->where('parent_category', $category->id)->whereStatus(1)
            ->orderBy('id', 'desc');
    
        // Apply search filter if provided
        if ($search_keyword) {
            $mainCategoryProductsQuery->where('product_name', 'LIKE', '%' . $search_keyword . '%');
        }
    
        $paginatedProducts = $mainCategoryProductsQuery->paginate(10);

        // dd($paginatedProducts);
    
        foreach ($paginatedProducts as $product) {
            $product->feature_image = $this->getImageUrl('product/feature_img', $product->feature_image);

            if($user){
                $product->in_wishlist = in_array($product->id, $wishlistProductIds);
            }
            
            $regular_price = (float)($product->regular_price ?? 0);
            $sale_price = (float)($product->sale_price ?? 0);
    
            $product->discount_percent = ($regular_price > 0 && $sale_price > 0)
                ? round((($regular_price - $sale_price) / $regular_price) * 100)
                : 0;
    
            $product->loading = false;
            $product->sold_out = $this->soldOut($product->id);
            $product->average_rating = $product->reviews()->avg('rating') ?: 0;
            $product->makeHidden(['formatted_id']);
        }
    
        // Assign paginated products to category
        $category->products = $paginatedProducts;
    
        foreach ($category->subcategory as $subcategory) {
            $subcategory->image = $this->getImageUrl('category', $subcategory->image);
        }
    
        return Response::json(['status' => 'success', 'categories' => $category]);
    }    
    
    public function sub_category_wise_product(Request $request)
    {
        $user = auth('sanctum')->user();
        $search_keyword = $request->input('search_keyword');
        $cat_id = $request->input('category_id');
        $sub_cat_id = $request->input('sub_cat_id');

        if($user){
            $wishlistProductIds = $user->wishlist ? $user->wishlist->pluck('product_id')->toArray() : [];
        }

        if ($sub_cat_id) {
            $productsQuery = Product::select('id', 'product_name', 'quantity as available_quantity', 'feature_image', 'regular_price', 'sale_price')->whereStatus(1)->where('parent_category', $cat_id)->where('child_category', $sub_cat_id);

            // Apply search filter if provided
            if ($search_keyword) {
                $productsQuery->where('product_name', 'LIKE', '%' . $search_keyword . '%');
            }

            $products = $productsQuery->orderBy('id', 'desc')->get();

            // if ($products->isEmpty()) {
            //     return response()->json(['status' => 'failed', 'products' => null]);
            // }
            if($user){
                $this->processProducts($products, $wishlistProductIds);
            }else{
                $this->processProducts($products, '');
            }
                

            $perPage = 10; // Set the number of items per page
            $page = $request->input('page', 1); // Current page or default to 1
            $offset = ($page - 1) * $perPage;
            $paginatedProducts = $products->slice($offset, $perPage)->values();

            // Create pagination metadata
            $pagination = new LengthAwarePaginator(
                $paginatedProducts,
                $products->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return response()->json(['status' => 'success', 'products' => $pagination]);
        }
    }

    public function AddToWishlist(Request $request)
    {
        $user = auth('sanctum')->user();
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['status' => 'failed', 'message' => 'Data not found', 'add_favourite' => false]);
        }


        $wishlist = Wishlist::where(['user_id' => $user->id, 'product_id' => $request->product_id])->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['status' => 'success', 'message' => 'Favourite product removed successfully', 'add_favourite' => false]);
        } else {
            $wishlistLimit = Wishlist::where('user_id', $user->id)->count();

            if($wishlistLimit >= 20){
                return response()->json(['status' => 'failed', 'message' => 'You can only add up to 20 favorite products.', 'add_favourite' => false]);
            }

            Wishlist::create(['user_id' => $user->id, 'product_id' => $request->product_id]);
            return response()->json(['status' => 'success', 'message' => 'Favourite product added successfully', 'add_favourite' => true]);
        }
    }

    public function AllWishlistProducts()
    {
        $user = auth('sanctum')->user();
        $wishlist_products = Wishlist::where('user_id', $user->id)
            ->with(['product' => function ($query) {
                $query->select('id', 'product_name', 'quantity as available_quantity', 'min_order', 'feature_image', 'regular_price', 'sale_price');
            }])
            ->orderBy('id', 'desc')
            ->get();

        $products = $wishlist_products->map(function ($wishlist) {
            return $wishlist->product;
        });

        if ($products->isEmpty()) {
            return Response::json(['status' => 'success', 'wishlist_products' => []]);
        }

        // Process products for additional data
        $this->processProducts($products, []);

        // Loop through each product to assign loadingcart and loadinglike
        foreach ($products as $product) {
            $product->loadingcart = false;
            $product->loadinglike = false;
        }

        return Response::json(['status' => 'success', 'wishlist_products' => $products]);
    }

    public function AllProducts(Request $request)
    {
        $brand_id = $request->input('brand_id');
        $priceMin = $request->input('price_min', 0);
        $priceMax = $request->input('price_max');
        $priceRanges = $request->input('price_ranges', []);
        $discountRanges = $request->input('discount', []);
        $ratingRanges = $request->input('rating', []);
        $priceSorting = $request->input('price_sorting');
        $topSale = $request->input('top_sale');

        // Ensure $priceRanges is an array
        $priceRanges = is_string($priceRanges) ? explode(',', $priceRanges) : (array) $priceRanges;

        // Ensure $discountRanges is an array
        $discountRanges = is_string($discountRanges) ? explode(',', $discountRanges) : (array) $discountRanges;

        // Ensure $ratingRanges is an array
        $ratingRanges = is_string($ratingRanges) ? explode(',', $ratingRanges) : (array) $ratingRanges;

        $query = Product::select('id', 'product_name', 'brand_id', 'quantity as available_quantity', 'feature_image', 'parent_category', 'regular_price', 'sale_price', 'min_order')->whereStatus(1);

        // Filter by multiple brands
        if (!empty($brand_id)) {
            if (is_string($brand_id)) {
                $brand_id = explode(',', $brand_id); // Convert to array
            }
            $brand_id = array_filter(array_map('trim', $brand_id));
            $query->whereIn('brand_id', $brand_id);
        }
        $products = $query->orderBy('id', 'DESC')->get();

        $user = auth('sanctum')->user();
        if($user){
            $wishlistProductIds = $user->wishlist ? $user->wishlist->pluck('product_id')->toArray() : [];
            $this->processProducts($products, $wishlistProductIds);
        }else{
            $wishlistProductIds = '';
            $this->processProducts($products, $wishlistProductIds);
        }

        // Apply price filtering
        $filteredProducts = $products->filter(function ($product) use ($priceMin, $priceMax) {
            $price = (float)($product->sale_price ?: $product->regular_price);
            return ($priceMin === null || $price >= (float)$priceMin) && ($priceMax === null || $price <= (float)$priceMax);
        });

        // Apply top sale filtering
        if (!empty($topSale) && $topSale == 'top_sale') {
            $filteredProducts = $filteredProducts->sortByDesc('sold_out');
        }

        // Apply price filtering
        if (!empty($priceSorting)) {
            if ($priceSorting === 'high') {
                $filteredProducts = $filteredProducts->sortByDesc(function ($product) {
                    return (float)($product->sale_price ?: $product->regular_price);
                });
            } else {
                $filteredProducts = $filteredProducts->sortBy(function ($product) {
                    return (float)($product->sale_price ?: $product->regular_price);
                });
            }
        }

        // Apply price ranges filtering
        // if (!empty($priceRanges)) {
        //     $price = $products->sale_price ? 'sale_price' : 'regular_price';
        //     $filteredProducts = $this->filterProducts($filteredProducts, $priceRanges, $price);
        // }
        if (!empty($priceRanges)) {
            $filteredProducts = $filteredProducts->filter(function ($product) use ($priceRanges) {
                $price = (float)($product->sale_price ?: $product->regular_price);
                foreach ($priceRanges as $range) {
                    if (strpos($range, '-') !== false) {
                        [$rangeMin, $rangeMax] = explode('-', $range);
                        $rangeMin = trim($rangeMin);
                        $rangeMax = trim($rangeMax);

                        if ($rangeMax === 0 || $price >= 52) {  // Check for the 52+ condition
                            return true;
                        }

                        if (is_numeric($rangeMin) && is_numeric($rangeMax)) {
                            if ($price >= (float)$rangeMin && $price <= (float)$rangeMax) {
                                return true;
                            }
                        }
                    }
                }
                return false;
            });
        }

        // Apply discount ranges filtering
        if (!empty($discountRanges)) {
            $filteredProducts = $this->filterProducts($filteredProducts, $discountRanges, 'discount_percent');
        }

        // Apply rating ranges filtering
        if (!empty($ratingRanges)) {
            $filteredProducts = $filteredProducts->filter(function ($product) use ($ratingRanges) {
                $rating = (float)$product->average_rating;

                foreach ($ratingRanges as $range) {
                    if ($rating >= (float)$range) {
                        return true;
                    }
                }
                return false;
            });
        }

        if (!empty($topSale) && $topSale == 'top_sale') {
            $filteredProducts = $filteredProducts->filter(function ($product) {
                return (float)$product->sold_out > 0; // Check if sold_out quantity is greater than 0
            });
        }

        // Now apply pagination after filtering all products
        $perPage = 16; // Set the number of items per page
        $page = $request->input('page') ? $request->input('page') : 1; // Current page or default to 1
        $offset = ($page - 1) * $perPage;
        $paginatedProducts = $filteredProducts->slice($offset, $perPage)->values();

        // Manually create pagination metadata
        $pagination = new LengthAwarePaginator(
            $paginatedProducts,
            $filteredProducts->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Response::json(['status' => 'success', 'products' => $pagination]);
    }

    public function SingleProduct(Request $request)
    {
        if(auth('sanctum')->user()){
            $user_id = auth('sanctum')->user()->id;
            $product_id = $request->product_id;
            $product = Product::where('id', $product_id)
                ->select(['id', 'id as product_id', 'product_name', 'description', 'brand_id', 'feature_image', 'gallery_image', 'regular_price', 'sale_price', 'quantity as available_quantity', 'min_order', 'flash_deal'])
                ->with(['brands',
                    'reviews' => function ($query) use ($user_id) {
                        $query->select('id', 'user_id', 'user_name', 'description', 'rating', 'images', 'video', 'created_at', 'product_id')->orderBy('id', 'DESC')
                            ->withCount('helpfulVotes')
                            ->with(['helpfulVotes' => function ($helpfulQuery) use ($user_id) {
                                $helpfulQuery->where('user_id', $user_id);
                            }])->limit(2);
                    }
                ])->first();
        }else{
            $product_id = $request->product_id;
            $product = Product::where('id', $product_id)
                ->select(['id', 'id as product_id', 'product_name', 'description', 'brand_id', 'feature_image', 'gallery_image', 'regular_price', 'sale_price', 'quantity as available_quantity', 'min_order', 'flash_deal'])
                ->with(['brands', 'reviews'])->first();
        }

        if (!$product) {
            return response()->json(['status' => 'failed', 'product' => null]);
        }
        $product->makeHidden('formatted_id', 'id', 'brand_id', 'brands');
        // $product_description = strip_tags(preg_replace("/&#?[a-z0-9]+;/i", "", $product->description));
        // $product->description = str_replace(array("\r", "\n"), "", $product_description);
        $product->description =  $product->description;
        $product->brand_name = $product->brands->brand_name ?? null;
        $product->feature_image = $this->getImageUrl('product/feature_img', $product->feature_image);

        if (is_string($product->gallery_image)) {
            $gallery_images = json_decode($product->gallery_image, true);

            // Ensure gallery_images is an array
            if (is_array($gallery_images)) {
                foreach ($gallery_images as &$image) {
                    if (isset($image)) {
                        $image = $this->getImageUrl('product/gallery_img', $image);
                    }
                }
                $product->gallery_image = $gallery_images;
            }
        } else {
            $product->gallery_image = [];
        }

        if(auth('sanctum')->user()){
            $cartQuantity = Cart::where('user_id', $user_id)->where('product_id', $product_id)->first();
            $product->cart_product_quantity = $cartQuantity ? $cartQuantity->quantity : 0;
            $product->cart_id = $cartQuantity ? $cartQuantity->id : 0;
            $product->cart = Cart::where('user_id', $user_id)->where('product_id', $product_id)->exists();
            $product->like = Wishlist::where('user_id', $user_id)->where('product_id', $product_id)->exists();
        }

        $regular_price = (float)($product->regular_price ?? 0);
        $sale_price = (float)($product->sale_price ?? 0);

        $product->discount_percent = ($regular_price > 0 && $sale_price > 0) ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;

        $product->available = $product->available_quantity > 0;

        $product->sold_out = null;
        $product->loading = false;
        $product->cartloading = false;
        $product->counter = $product->min_order;

        $averageRating = round($product->reviews()->avg('rating'), 2);
        $product->average_rating = $averageRating ?: 0; // Set average rating, default to 0 if no reviews

        // Format reviews
        foreach ($product->reviews as $review) {
            $review->helpful = $review->helpfulVotes->isNotEmpty();
            $mediaArray = [];

            if ($review->images) {
                $reviewImages = json_decode($review->images, true);
                if (is_array($reviewImages)) {
                    foreach ($reviewImages as &$reviewImage) {
                        $mediaArray[] = $this->getImagesUrl('review_img', $reviewImage);
                    }
                    // $review->image = $reviewImages;
                }
            }

            if ($review->video) {
                $reviewVideos = json_decode($review->video, true);
                if (is_array($reviewVideos)) {
                    foreach ($reviewVideos as &$reviewVideo) {
                        $mediaArray[] = $this->getVideoUrl('review_videos', $reviewVideo);
                    }
                    // $review->video = $reviewVideos;
                }
            }

            // Fetch user image
            $user_image = User::where('id', $review->user_id)->pluck('image')->first();
            if ($user_image) {
                $review->user_image = $this->getImageUrl('profile_img/user', $user_image); // Use user image
            } else {
                $review->user_image = $this->getImageUrl('profile_img/user', 'common.png'); // Use default image
            }

            $review->media = $mediaArray;
            $review->date = \Carbon\Carbon::parse($review['created_at'])->format('d F, Y');
        }
        $product->reviews->makeHidden(['helpfulVotes', 'created_at', 'images', 'video']);

        // flash deals
        if ($product->flash_deal == 1) {
            $deal = DB::table('product_flash_deals')->where('start_flash_deal', '<=', now())->where('end_flash_deal', '>=', now())->orderBy('start_flash_deal', 'desc')->get();
            // dd($deal);
            $flash_deal_time = [
                'start_time' => $deal[0]->start_flash_deal ?? '',
                'end_time' => $deal[0]->end_flash_deal ?? ''
            ];
        } else {
            $flash_deal_time = null;
        }

        return Response::json(['status' => 'success', 'flash_deal_time' => $flash_deal_time, 'product' => $product]);
    }

    public function AllBrands()
    {
        $brands = Brand::where('status', 1)->orderBy('brand_name', 'asc')->get(['id', 'brand_name', 'slug']);
        foreach ($brands as $brand) {
            $brand->check = false;
        }
        if (!$brands) {
            return Response::json(['status' => 'failed', 'brands' => []]);
        }
        return Response::json(['status' => 'success', 'brands' => $brands]);
    }

    public function product_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'description' => 'required|string',
            'rating' => 'required|min:1|max:5',
            'images.*' => 'nullable|mimes:jpeg,jpg,png,webp|max:2048', // Max image size 2MB per file
            'video.*' => 'nullable|mimes:mp4,mov,avi|max:20480', // Max video size 20MB per file
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        }

        $user_id = auth('sanctum')->user()->id;

        $review = new Review();
        $review->user_id = $user_id;
        $review->user_name = auth('sanctum')->user()->first_name . ' ' . auth('sanctum')->user()->last_name;
        $review->product_id = $request->product_id;
        $review->description = $request->description;
        $review->rating = $request->rating;

        if ($request->hasFile('images')) {
            $reviewImages = [];
            foreach ($request->file('images') as $file) {
                $newName = uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('admin-assets/assets/review_img/'), $newName);
                $reviewImages[] = $newName;
            }
            $review->images = json_encode($reviewImages);
        }

        if ($request->hasFile('video')) {
            $reviewVideos = [];
            foreach ($request->file('video') as $video) {
                $newName = uniqid() . '.' . $video->getClientOriginalExtension();
                $video->move(public_path('admin-assets/assets/review_videos/'), $newName);
                $reviewVideos[] = $newName;
            }
            $review->video = json_encode($reviewVideos);
        }
        $review->save();

        return response()->json(['status' => 'success', 'message' => 'Review submitted successfully']);
    }

    public function product_review_update(Request $request)
    {
        $customer = auth('sanctum')->user();
        
        $review = Review::find($request->review_id);

        if(!$review){
            return response()->json(['status' => 'failed', 'message' => 'Review not found.']);
        }

        $review->user_id = $customer->id;
        $review->user_name = $customer->first_name . ' ' . $customer->last_name;
        $review->product_id = $request->product_id;
        $review->description = $request->description;
        $review->rating = $request->rating;

        $review->update();
        return response()->json(['status' => 'success', 'message' => 'Review updated successfully.']);
    }

    public function product_review_helpful(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'product_review_id' => 'required|exists:product_review,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'failed', 'message' => $validator->errors()->first()]);
        }

        $user_id = auth('sanctum')->user()->id;
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['status' => 'failed', 'message' => 'Data not found', 'add_favourite' => false]);
        }

        $review_helpful = ReviewHelpful::where(['user_id' => $user_id, 'product_id' => $request->product_id, 'product_review_id' => $request->product_review_id])->first();

        if ($review_helpful) {
            $review_helpful->delete();
            return response()->json(['status' => 'success', 'message' => 'Review helpful removed successfully', 'add_helpful' => false]);
        } else {
            ReviewHelpful::create(['user_id' => $user_id, 'product_id' => $request->product_id, 'product_review_id' => $request->product_review_id]);
            return response()->json(['status' => 'success', 'message' => 'Review helpful added successfully', 'add_helpful' => true]);
        }
    }

    public function product_all_reviews(Request $request)
    {
        $user_id = auth('sanctum')->user()->id;
        $product_id = $request->product_id;

        // Get all products ordered by the user
        $orderedProducts = Order::where('user_id', $user_id)->pluck('product_details');

        $canAddReview = false;

        foreach ($orderedProducts as $productDetails) {
            $productList = json_decode($productDetails, true);
            if (is_array($productList)) {
                foreach ($productList as $product) {
                    if (isset($product['product_id']) && $product['product_id'] == $product_id) {
                        $canAddReview = true;
                        break 2; // Exit both loops as match is found
                    }
                }
            }
        }


        // Retrieve reviews for the specified product and count helpful votes
        // $reviews = Review::where('product_id', $product_id)->withCount('helpfulVotes')->get();
        $reviews = Review::select('id', 'user_id', 'user_name', 'description', 'rating', 'images', 'video', 'created_at', 'product_id')->where('product_id', $product_id)->orderBy('id', 'DESC')
            ->withCount('helpfulVotes')
            ->with(['helpfulVotes' => function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            }])
            ->get();

        if ($reviews->isEmpty()) {
            return response()->json(['status' => 'success', 'average_rating' => 0, 'reviews' => null, 'can_add_review' => $canAddReview]);
        }

        $averageRating = round($reviews->avg('rating'), 2);

        foreach ($reviews as $review) {
            // Check if the logged-in user has marked the review as helpful
            $review->helpful = $review->helpfulVotes->isNotEmpty();
            $mediaArray = [];

            if ($review->images) {
                $reviewImages = json_decode($review->images, true);
                if (is_array($reviewImages)) {
                    foreach ($reviewImages as &$reviewImage) {
                        $mediaArray[] = $this->getImagesUrl('review_img', $reviewImage);
                    }
                }
            }

            if ($review->video) {
                $reviewVideos = json_decode($review->video, true);
                if (is_array($reviewVideos)) {
                    foreach ($reviewVideos as &$reviewVideo) {
                        $mediaArray[] = $this->getVideoUrl('review_videos', $reviewVideo);
                    }
                }
            }

            // Fetch user image
            $user_image = User::where('id', $review->user_id)->pluck('image')->first();
            if ($user_image) {
                $review->user_image = $this->getImageUrl('profile_img/user', $user_image); // Use user image
            } else {
                $review->user_image = $this->getImageUrl('profile_img/user', 'common.png'); // Use default image
            }

            $review->media = $mediaArray;
            $review->date = \Carbon\Carbon::parse($review['created_at'])->format('d F, Y');
            $review->review_edit = $review->user_id === $user_id ? 'true' : 'false';
        }
        $reviews->makeHidden(['helpfulVotes', 'created_at', 'images', 'video']);
        return response()->json(['status' => 'success', 'average_rating' => $averageRating, 'reviews' => $reviews, 'can_add_review' => $canAddReview]);
    }

    protected function getImagesUrl($type, $filename)
    {
        return asset("admin-assets/assets/{$type}/{$filename}");
    }

    protected function getVideoUrl($type, $filename)
    {
        return asset("admin-assets/assets/{$type}/{$filename}");
    }

    private function getImageUrl($type, $filename)
    {
        return asset("admin-assets/assets/img/{$type}/{$filename}");
    }

    private function processProducts($products = null, $wishlistProductIds = null)
    {
        foreach ($products as $product) {
            $product->feature_image = $this->getImageUrl('product/feature_img', $product->feature_image);
            if($wishlistProductIds){

                $product->in_wishlist = in_array($product->id, $wishlistProductIds);
            }
            $product->min_order = $product->min_order;

            // Calculate discount percentage
            $regular_price = (float)($product->regular_price ?? 0);
            $sale_price = (float)($product->sale_price ?? 0);

            if ($regular_price > 0 && $sale_price > 0) {
                $discountPercentage = (($regular_price - $sale_price) / $regular_price) * 100;
                $product->discount_percent = round($discountPercentage);
            } else {
                $product->discount_percent = 0;
            }

            $product->loading = false;
            $product->sold_out = $this->soldOut($product->id);

            $averageRating = $product->reviews()->avg('rating');
            $product->average_rating = $averageRating ?: 0;

            // Optionally hide fields if needed
            $product->makeHidden('formatted_id');
        }
    }

    private function soldOut($id = null)
    {
        $orders = Order::where('order_status', '!=', 'cancelled')->get();

        $total = 0;

        foreach ($orders as $order) {
            $product_details = json_decode($order->product_details, true);

            foreach ($product_details as $product_detail) {
                if ($product_detail['product_id'] == $id) {
                    $total += $product_detail['quantity'];
                }
            }
        }

        // Array of random numbers
        $randomNumbers = [1000, 200, 250, 300, 350, 400, 555, 572, 642, 655, 700];

        $total += $randomNumbers[array_rand($randomNumbers)];

        // Format the total
        if ($total >= 100000) {
            return "100K+";
        } elseif ($total >= 10000) {
            return "10K+";
        } elseif ($total >= 1000) {
            return "1K+";
        } else {
            return $total;
        }

        // return $total; // Return the total sold-out quantity
    }

    private function flashDealsoldOut($id = null)
    {
        $orders = Order::where('order_status', '!=', 'cancelled')->get();

        $total = 0;

        foreach ($orders as $order) {
            $product_details = json_decode($order->product_details, true);

            foreach ($product_details as $product_detail) {
                if ($product_detail['product_id'] == $id) {
                    $total += $product_detail['quantity'];
                }
            }
        }

        // Array of random numbers
        $randomNumbers = [1000, 200, 250, 300, 350, 400, 555, 572, 642, 655, 700];

        $total += $randomNumbers[array_rand($randomNumbers)];

        // Format the total
        return $total;
    }

    private function filterProducts($products, $ranges, $field)
    {
        return $products->filter(function ($product) use ($ranges, $field) {
            $value = (float)$product->{$field};

            foreach ($ranges as $range) {
                if (strpos($range, '-') !== false) {
                    [$rangeMin, $rangeMax] = explode('-', $range);
                    $rangeMin = trim($rangeMin);
                    $rangeMax = trim($rangeMax);

                    if (is_numeric($rangeMin) && is_numeric($rangeMax)) {
                        if ($value >= (float)$rangeMin && $value <= (float)$rangeMax) {
                            return true;
                        }
                    }
                }
            }
            return false;
        });
    }

    public function getSuggestions(Request $request)
    {
        $keyword = $request->input('keyword');

        if (empty($keyword)) {
            return response()->json(['status' => 'failed', 'message' => 'Keyword is required'], 400);
        }

        // Fetch product suggestions
        $productSuggestions = Product::where('product_name', 'LIKE', '%' . $keyword . '%')->where('status', 1)->select('id', 'product_name', 'regular_price', 'sale_price', 'feature_image')->limit(10)->get()->makeHidden('formatted_id');

        foreach($productSuggestions as $productSuggestion){
            $productSuggestion->feature_image = $this->getImageUrl('product/feature_img', $productSuggestion->feature_image);
            $productSuggestion->sold_out = $this->soldOut($productSuggestion->id);
        }

        return response()->json(['status' => 'success', 'suggestions' => $productSuggestions]);
    }

    /********************************* Home new Page api *************************/
    public function new_arival(){
        $user = auth('sanctum')->user();
        $wishlistProductIds = $user->wishlist ? $user->wishlist->pluck('product_id')->toArray() : [];

        // Fetch all categories with their subcategories
        $categoriesQuery = Category::select('id', 'category_name', 'image')->whereNull('parent_id')->whereStatus(1)->orderBy('category_order', 'asc');

        $categories = $categoriesQuery->get();

        if ($categories->isEmpty()) {
            return Response::json(['status' => 'failed', 'categories' => []]);
        }

        foreach ($categories as $category) {
            $category->image = $this->getImageUrl('category', $category->image);

            // Fetch products for this category
            $mainCategoryProductsQuery = Product::select(['id', 'product_name', 'quantity as available_quantity', 'feature_image', 'regular_price', 'sale_price'])->where('parent_category', $category->id)->whereStatus(1)->orderBy('id', 'desc');

            $mainCategoryProducts = $mainCategoryProductsQuery->limit(10)->get();

            foreach ($mainCategoryProducts as $product) {
                $product->feature_image = $this->getImageUrl('product/feature_img', $product->feature_image);
                $product->in_wishlist = in_array($product->id, $wishlistProductIds);

                $regular_price = (float)($product->regular_price ?? 0);
                $sale_price = (float)($product->sale_price ?? 0);

                if ($regular_price > 0 && $sale_price > 0) {
                    $discountPercentage = (($regular_price - $sale_price) / $regular_price) * 100;
                    $product->discount_percent = round($discountPercentage);
                } else {
                    $product->discount_percent = 0;
                }

                $product->loading = false;
                $product->sold_out = $this->soldOut($product->id);

                $averageRating = $product->reviews()->avg('rating');
                $product->average_rating = $averageRating ?: 0;
                $product->makeHidden(['formatted_id']);
            }

            $category->products = $mainCategoryProducts;
        }

        return Response::json(['status' => 'success', 'NewArival' => $categories]);
    }

    public function vouchers()
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['status' => 'failed', 'available_coupon' => []]);
        }

        $user_id = $user->id;

        // Retrieve active coupons
        $active_coupons = Coupon::select('id', 'code', 'title', 'amount_type', 'amount', 'product_min_amount', 'main_category', 'status', 'coupon_start_date', 'coupon_end_date')->where('coupon_type', 'category_wise')->where('status', '1')->orderBy('coupon_start_date', 'desc')->get();

        if ($active_coupons->isEmpty()) {
            return response()->json(['status' => false, 'available_coupon' => []]);
        }

        // Filter coupons
        $available_coupons = $active_coupons->filter(function ($coupon) use ($user_id) {
            $apply_user_ids = json_decode($coupon->apply_user_id, true) ?? [];

            $is_user_applicable = empty($apply_user_ids) || !in_array($user_id, $apply_user_ids);
            $is_within_date_range = Carbon::parse($coupon->coupon_start_date)->isPast() &&
                (Carbon::parse($coupon->coupon_end_date)->isFuture() || Carbon::parse($coupon->coupon_end_date)->isToday());

            return $is_user_applicable && $is_within_date_range;
        });

        // Add extra fields
        $available_coupons = $available_coupons->map(function ($coupon) {
            $category = Category::find($coupon->main_category);

            // Get the main category name
            $coupon->main_category_name = $category->category_name;
            $coupon->valid_up_to = $coupon->coupon_end_date;
            $coupon->expire = max(0, Carbon::now()->diffInDays(Carbon::parse($coupon->coupon_end_date)->endOfDay()));
            $coupon->loading = false;
            
            return $coupon;
        });
        return response()->json(['status' => $available_coupons->count(), 'available_coupon' => $available_coupons->values()]);
    }
}

