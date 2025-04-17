@extends('admin/index')
@section('title', 'Dashboard')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid p-0">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="dashboard_box">
            @if (Auth::guard('web')->user())
                <div class="row col-md-12">
                    <div class="col-xl-4  col-md-6 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/my_order_svg.svg') }}"
                                        width="30" height="30" alt="order-list-icon">
                                </div>
                                <div class="inner">
                                    <a href="{{ route('user.index') }}"><p>Total wholesalers</p></a>
                                    <h3>{{ $customer }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4  col-md-6 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/my_order_svg.svg') }}"
                                        width="30" height="30" alt="order-list-icon">
                                </div>
                                <div class="inner">
                                    <a href="{{ route('staff.index') }}"><p>Total Staff member</p></a>
                                    <h3>{{ $staff }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4  col-md-6 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/my_order_svg.svg') }}" width="30" height="30" alt="order-list-icon">
                                </div>
                                <div class="inner">
                                    <a href="{{ route('products.index') }}"><p>Total Products</p></a>
                                    <h3>{{ $product }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4  col-md-6 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/t_order.svg') }}" width="30" height="30" alt="t_order">
                                </div>
                                <div class="inner">
                                    <a href="{{ route('order.index') }}"><p>Total Orders</p></a>
                                    <h3>{{ $order_count }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4  col-md-6 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/t_sale.svg') }}" width="30" height="30" alt="t-sale">
                                </div>
                                <div class="inner">
                                    <p>Total Sales</p>
                                    <h3>₦{{ $total_sales }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4  col-md-6 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/unit_sold.svg') }}" width="30" height="30" alt="unit_sold">
                                </div>
                                <div class="inner">
                                    <p>Total units sold</p>
                                    <h3>{{ $total_unit_sold }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(Auth::guard('member')->user())
                <div class="row col-md-12">
                    <div class="col-lg-3 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/my_order_svg.svg') }}" width="30" height="30" alt="order-list-icon">
                                </div>
                                <div class="inner">
                                    <a href="{{ route('products.index') }}"><p>Total Products</p></a>
                                    <h3>{{ $product }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-12 info_col">
                        <div class="small-box">
                            <div class="dashboard_small_box">
                                <div class="icon_bg_small">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/t_order.svg') }}" width="30" height="30" alt="t_order">
                                </div>
                                <div class="inner">
                                    <a href="{{ route('order.index') }}"><p>Total Orders</p></a>
                                    <h3>{{ $staff_order_count }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section class="data_tbl_section">
    <div class="Responsive">
        <table class="datatable_dashboard " cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <th>
                        <h6>Recent Orders</h6>
                    </th>
                    {{-- <th colspan="10">
                        <div class="th_search th_product justify-content-end">
                            <div class="select_sort">
                                <select class="form-select" aria-label="Sort By" style="width:300px !important;">
                                    <option>Sort By:</option>
                                    <option value="1">Day</option>
                                    <option value="2">Week</option>
                                    <option value="3">Month</option>
                                    <option value="4">Year</option>
                                </select>
                            </div>
                        </div>
                    </th> --}}
                </tr>
                <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important;">
                    <th class="pro_duct_th" style="width:30%; ">Product</th>
                    <th style="width:10%; padding: 0 15px;">Order ID</th>
                    <th style="width:10%; padding: 0 15px;">Amount</th>
                    <th style="width:15%; padding: 0 15px;">Customer Name</th>
                    <th style="width:15%; padding: 0 15px;">Date</th>
                    <th style="width:10%; padding: 0 15px;">Status</th>
                    {{-- <th style="width:10%; ">Action</th> --}}
                </tr>
            </tbody>
            <tbody>
                @if(count($orders) > 0)
                    @php
                        $allProductDetails = [];
                        foreach ($orders as $order) {
                            $productCompleteDetails = json_decode($order->product_complete_details, true) ?? [];

                            // Flatten product details with complete details
                            foreach ($productCompleteDetails as $productDetail) {
                                $productDetail['order_id'] = $order->order_id;
                                $productDetail['user_name'] = $order->user->first_name . ' ' . $order->user->last_name;
                                $productDetail['order_created_at'] = $order->created_at;
                                $productDetail['order_status'] = $order->order_status;
                                $allProductDetails[] = $productDetail;
                            }
                        }

                        // Sort and limit to latest 8 products based on the order created_at
                        usort($allProductDetails, function($a, $b) {
                            return strtotime($b['order_created_at']) - strtotime($a['order_created_at']);
                        });
                        $latestProductDetails = array_slice($allProductDetails, 0, 8);
                    @endphp

                    @foreach ($latestProductDetails as $productDetail)
                        <tr>
                            <td class="product_name_img">
                                <img src="{{ asset('admin-assets/assets/img/product/feature_img/' . ($productDetail['feature_image'] ?? 'menu-icon/tbl_product_img.png')) }}" width="50" height="50" alt="Product Image" style="border-radius:10px;">
                                <span>{{ strip_tags(Str::limit($productDetail['product_name'] ?? 'N/A', 50, '...')) }}</span>
                            </td>
                            <td>{{ $productDetail['order_id'] }}</td>
                            <td> ₦{{ $productDetail['purchase_total_price'] ?? 'N/A' }}</td>
                            <td>{{ $productDetail['user_name'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($productDetail['order_created_at'])->format('F d, Y') }}</td>
                            @if($productDetail['order_status'] == 'pending')
                                <td class="status_span_pending"><span></span>Pending</td>
                            @elseif($productDetail['order_status'] == 'confirm')
                                <td class="status_span_confirm"><span></span>Confirm</td>
                            @elseif($productDetail['order_status'] == 'processing')
                                <td class="status_span_processing"><span></span>Processing</td>
                            @elseif($productDetail['order_status'] == 'dispatch')
                                <td class="status_span_dispatch"><span></span>Dispatch</td>
                            @elseif($productDetail['order_status'] == 'delivered')
                                <td class="status_span_delivered"><span></span>Delivered</td>
                            @elseif($productDetail['order_status'] == 'complete')
                                <td class="status_span_complete"><span></span>Completed</td>
                            @elseif($productDetail['order_status'] == 'cancelled')
                                <td class="status_span_cancelled"><span></span>Canceled</td>
                            @elseif($productDetail['order_status'] == 'return')
                                <td class="status_span_return"><span></span>Return</td>
                            @elseif($productDetail['order_status'] == 'refund')
                                <td class="status_span_return"><span></span>Refund</td>
                            @endif
                            {{-- <td>
                                <a href="">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/edit_icon.svg') }}" width="22" height="22" alt="edit_icon" style="margin-right:6px;">
                                </a>
                                <a href="">
                                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/eye-off-icon.svg') }}" width="22" height="22" alt="eye-off-icon">
                                </a>
                            </td> --}}
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="10" style="text-align: center;">No Orders Found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
@endsection
