@extends ('admin/index')
@section('title', 'Order List')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>All Orders</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Orders List</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="add_new_staff">
                    {{-- <a href="{{ route('order.export_order2') }}">Export Order 2</a> --}}
                    <a href="{{ route('order.export_order') }}" style="margin-right: 10px;">Export Order</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="Responsive">
                    <table id="all-products-table" class="datatable_dashboard" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th>
                                    <h6 style="width:200px;">All Orders</h6>
                                </th>
                                <th colspan="10">
                                </th>
                            </tr>
                            <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 ! important;">
                                <th class="pro_duct_th" style="width:5%;">Sr. no.</th>
                                <th style="width:10%;">Order ID</th>
                                <th style="width:20%;">Customer Name</th>
                                <th style="width:10%;">Amount</th>
                                <th style="width:10%;">Quantity</th>
                                <th style="width:10%;">Payment</th>
                                <th style="width:20%;">Order Date</th>
                                <th style="width:10%;">Order Status</th>
                                <th style="width:7%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            @if($orders->isNotEmpty())
                                @foreach ($orders as $order)
                                    @php
                                        $productDetails = json_decode($order->product_details, true) ?? [];
                                        $quantity = count($productDetails);

                                        $orderReciveDate = \Carbon\Carbon::parse($order->created_at);
   
                                        // Format the date as 'Day, DD Month'
                                        $formattedDate = $orderReciveDate->format('l, d F');
                                    @endphp
                                    <tr>
                                        <td class="sr_no">{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                                        <td>{{ $order->order_id }}</td>
                                        <td>{{ $order->user->first_name . ' ' . $order->user->last_name }}</td>
                                        <td>₦{{ $order->sub_total }}</td>
                                        <td>{{ $quantity }}</td>
                                        <td>₦{{ $order->net_amount }} </td>
                                        <td>{{ $formattedDate }}</td>
                                        @if($order->order_status == 'pending')
                                            <td class="status_span_pending"><span></span>Pending</td>
                                        @elseif($order->order_status == 'confirm')
                                            <td class="status_span_confirm"><span></span>Confirm</td>
                                        @elseif($order->order_status == 'processing')
                                            <td class="status_span_processing"><span></span>Processing</td>
                                        @elseif($order->order_status == 'dispatch')
                                            <td class="status_span_dispatch"><span></span>Dispatch</td>
                                        @elseif($order->order_status == 'delivered')
                                            <td class="status_span_delivered"><span></span>Delivered</td>
                                        @elseif($order->order_status == 'complete')
                                            <td class="status_span_complete"><span></span>Completed</td>
                                        @elseif($order->order_status == 'cancelled')
                                            <td class="status_span_cancelled"><span></span>Canceled</td>
                                        @elseif($order->order_status == 'return')
                                            <td class="status_span_return"><span></span>Return</td>
                                        @elseif($order->order_status == 'refund')
                                            <td class="status_span_return"><span></span>Refund</td>
                                        @endif
                                        <td>
                                            <a href="{{ route('order.edit', $order->id) }}">
                                                <img src="{{ asset('/admin-assets/assets/img/menu-icon/edit_icon.svg') }}" width="22" height="22" alt="edit_icon">
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" style="text-align: center;">Records Not Found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix product_foot" id="paginationLinks">
                    {{-- {{ $orders->links() }} --}}
                    {{ $orders->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
