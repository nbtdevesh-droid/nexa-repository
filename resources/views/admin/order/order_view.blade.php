@extends ('admin/index')
@section('title', 'Order Details')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Order Details</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('order.index') }}">All Orders</a></li>
                    <li class="breadcrumb-item active">Order Details</li>
                </ol>
            </div>
        </div>

        <div class="row mb-2 align-items-center">
            <div class="col-md-12 order-id-box">
                <h5>Tracking Number</h5>
                <form method="POST" action="{{ route('order.tracking', $orders->id) }}"
                    id="order-update-form">
                    @csrf
                    <div class="order-change-satus">
                        <input type="hidden" name="order_status" value="{{ $orders->order_status }}">
                        <input type="hidden" name="shiping_date" value="{{ $orders->shiping_date }}">
                        <input type="text" class="tracking" name="tacking_number" value="{{$orders->tracking_number}}" placeholder="Tacking Number">
                        <input type="text" class="tracking" name="carrier_code" value="{{$orders->tracking_carrier_code}}" placeholder="Carrier Code">

                        <div class="order-border-btn"><button type="submit" iclass="save">Update</button></div>
                    </div><br>
                    <p class="text text-danger">*Please add shipping date & change order status before add tracking number & carrier code.*</p>
                </form>
            </div>
        </div>

        <div class="row mb-2">
            <div class="order-detail-box">
                <div class="row mb-2">
                    <div class="row col-md-12 align-items-center">
                        <div class="col-md-10 order-id-box">
                            <div class="order-id">
                                <p>Orders ID: {{ $orders->order_id }}</p>
                                <a class="order-btn order-detail-box-btn ">
                                    @if ($orders->order_status == 'pending')
                                    Pending
                                    @elseif($orders->order_status == 'confirm')
                                    Confirm
                                    @elseif($orders->order_status == 'processing')
                                    Processing
                                    @elseif($orders->order_status == 'dispatch')
                                    Dispatch
                                    @elseif($orders->order_status == 'delivered')
                                    Delivered
                                    @elseif($orders->order_status == 'complete')
                                    Completed
                                    @elseif($orders->order_status == 'cancelled')
                                    Cancelled
                                    @elseif($orders->order_status == 'return')
                                    Return
                                    @elseif($orders->order_status == 'refund')
                                    Refund
                                    @endif
                                </a>
                            </div>
                        </div>
                        @if ($orders->payment_mode !== 'cod' && $orders->order_status == 'cancelled')
                        <div class="col-md-2 order-id-box">
                            <div class="order-id  d-flex justify-content-end ">
                                <form action="{{ route('order.payment.refund', $orders->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="order-btn  order-btn-one ">Refund</button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="order-btn-group">
                            <div class="order-left-group">
                                <p>
                                    <span><i class="fa-solid fa-calendar-days"></i></span>
                                    {{ $orders->created_at->format('d F, Y') }} -
                                    {{ $orders->shiping_date ? $orders->shiping_date->format('d F, Y') : '' }}
                                </p>
                            </div>

                            <div class="order-right-group">
                                @php
                                // Define the order of statuses
                                $status_order = [
                                'pending',
                                'confirm',
                                'processing',
                                'dispatch',
                                'delivered',
                                'cancelled',
                                'refund',
                                ];
                                //$status_order = ['pending', 'confirm', 'dispatch'];
                                $current_status_index = array_search($orders->order_status, $status_order);
                                @endphp

                                <form method="POST" action="{{ route('order.update', $orders->id) }}"
                                    id="order-update-form">
                                    @method('PUT')
                                    @csrf
                                    <div class="order-change-satus">
                                        <select class="form-select" name="order_status" aria-label="Sort By"
                                            style="width:200px !important;">
                                            <option hidden>Change Status</option>

                                            @foreach ($status_order as $index => $status)
                                            <option value="{{ $status }}"
                                                {{ $orders->order_status == $status ? 'selected' : '' }}
                                                {{ $index < $current_status_index ? 'disabled' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                            @endforeach
                                        </select>

                                        <input type="date" class="form-control" placeholder="Shipping date"
                                            name="shipping_date"
                                            value="{{ $orders->shiping_date ? $orders->shiping_date->format('Y-m-d') : '' }}"
                                            required>
                                        @if ($orders->order_status != 'pending')
                                        <div class="order-border-btn" id="printButton" style="cursor: pointer;"><i
                                                class="fa-solid fa-print"></i></div>
                                        @endif
                                        <div class="order-border-btn"><button type="submit" class="save">save</button></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2 ">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-12 ">
                                <div class="profile-box">
                                    <div class="profile-details">
                                        <div class="order-profile order-detail-box-btn"><i
                                                class="fa-regular fa-user"></i></div>
                                        <div class="profile-text-field">
                                            <h5>Customer</h5>
                                            <p><b>Full Name:</b>
                                                {{ $orders->user->first_name . ' ' . $orders->user->last_name }}
                                            </p>
                                            <p><b>Email:</b> {{ $orders->user->email }}</p>
                                            <p><b>Phone:</b>
                                                {{ $orders->user->country_code . ' ' . $orders->user->phone }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="profile-box-view order-detail-box-btn"><a href="#"
                                            data-toggle="modal" class="view-user" data-target="#modal-default"
                                            data-id="{{ $orders->user->id }}">View profile</a></div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="profile-box">
                                    <div class="profile-details">
                                        <div class="order-profile order-detail-box-btn"><i
                                                class="fa-regular fa-user"></i></div>
                                        <div class="profile-text-field">
                                            <h5>Order Info</h5>
                                            <p><b>Payment Method:</b>
                                                @if ($orders->payment_mode == 'cod')
                                                Cash on delivery
                                                @else
                                                Online Payment
                                                @endif
                                            </p>
                                            <p><b>Status:</b>
                                                @if ($orders->order_status == 'pending')
                                                Pending
                                                @elseif($orders->order_status == 'confirm')
                                                Confirm
                                                @elseif($orders->order_status == 'processing')
                                                Processing
                                                @elseif($orders->order_status == 'dispatch')
                                                Dispatch
                                                @elseif($orders->order_status == 'delivered')
                                                Delivered
                                                @elseif($orders->order_status == 'complete')
                                                Completed
                                                @elseif($orders->order_status == 'cancelled')
                                                Cancelled
                                                @elseif($orders->order_status == 'refund')
                                                Refund
                                                @endif
                                            </p>
                                            <p><b>Delivery Option:</b> {{ $orders->delivery_option }}</p>
                                        </div>
                                    </div>
                                    @if ($orders->order_status != 'pending')
                                    <div class="profile-box-view order-detail-box-btn"><a
                                            href="{{ route('order.download.info', $orders->id) }}">Download
                                            info</a></div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12  ">
                                <div class="profile-box">
                                    <div class="profile-details">
                                        <div class="order-profile order-detail-box-btn"><i
                                                class="fa-regular fa-user"></i></div>
                                        <div class="profile-text-field">
                                            @php
                                            $address = json_decode($orders->shiping_address_id, true) ?? [];
                                            @endphp
                                            <h5>Deliver to</h5>
                                            <p><b>Address:</b> {{ $orders->formatted_address }}</p>
                                            <p><b>Customer Name:</b> {{ $orders->shipping_address['name'] }}</p>
                                            <p><b>Phone:</b> {{ $orders->customer_phone }}</p>
                                        </div>
                                    </div>
                                    <div class="profile-box-view order-detail-box-btn"><a href="#"
                                            data-toggle="modal" class="view-address" data-target="#modal-address">View
                                            Delivery Address</a></div>
                                </div>
                            </div>
                            @if ($orders->warehouse_id != null && $orders->delivery_option == 'Pickup')
                            <div class="col-lg-3 col-md-6 col-12  ">
                                <div class="profile-box">
                                    <div class="profile-details">
                                        <div class="order-profile order-detail-box-btn"><i
                                                class="fa-regular fa-user"></i></div>
                                        <div class="profile-text-field">
                                            @php
                                            $warehouse = json_decode($orders->warehouse_id, true) ?? [];
                                            @endphp
                                            <h5>WareHouse Address</h5>
                                            <p><b>WareHouse Name:</b> {{ $warehouse['warehouse_name'] }}</p>
                                            <p><b>Address:</b> {{ $warehouse['street_address'] }},
                                                {{ $warehouse['city'] }}, {{ $warehouse['state'] }}<br>
                                                {{ $warehouse['country'] }} - {{ $warehouse['zip_code'] }}
                                            </p><br>

                                            <h5>WareHouse Contact Details</h5>
                                            <p><b>Name:</b> {{ $warehouse['contact_name'] }}</p>
                                            <p><b>Email:</b> {{ $warehouse['contact_email'] }}</p>
                                            <p><b>Phone:</b> {{ $warehouse['country_code'] }}
                                                {{ $warehouse['contact_number'] }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-2"></div>
                </div>
                <div class="row mb-2">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="profile-box profile-box-2">
                                    <div class="profile-text-field">
                                        <h5>Payment Info</h5>
                                        <p>
                                            @if ($orders->payment_mode == 'cod')
                                            <span><img
                                                    src="{{ asset('admin-assets/assets/img/order/cash.png') }}"></span>Cash
                                            on delivery
                                            @else
                                            Online Payment
                                            @endif
                                        </p>
                                        <p><b>User name:</b>
                                            {{ $orders->user->first_name . ' ' . $orders->user->last_name }}
                                        </p>
                                        <p><b>Phone:</b> {{ $orders->user->country_code . ' ' . $orders->user->phone }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="profile-box  profile-box-2">
                                    <div class="profile-text-field">
                                        <h6>Expected Date Of Delivery</h6>
                                        <p>{{ $orders->shiping_date ? $orders->shiping_date->format('d F, Y') : '' }}
                                        </p>
                                        {{-- <div class="profile-box-tracking-btn order-detail-box-btn"><a href="#">Tracking</a></div> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="profile-box  profile-box-2">
                                    <textarea name="text-area" id="" placeholder="Notes..." readonly>{{ $orders->message_saller }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table id="all-products-table-one" class="datatable_dashboard" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <h6>All Products</h6>
                            </th>
                            <th colspan="10">
                            </th>
                        </tr>
                        <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 ! important;">
                            <th style="width:20%;padding: 15px;">Product</th>
                            <th style="width:20%;padding: 15px;">Order ID</th>
                            <th style="width:10%;padding: 15px;">Quantity</th>
                            <th style="width:10%;padding: 15px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody-item">
                        @if ($orders)
                        @php
                        $subTotal = 0;
                        $total = 0;
                        $productDetails = json_decode($orders->product_complete_details, true) ?? [];
                        @endphp
                        @foreach ($productDetails as $productDetail)
                        <tr>
                            <td><span style="padding-right: 10px;">
                                <img src="{{ asset('admin-assets/assets/img/product/feature_img/' . $productDetail['feature_image']) }}" width="50" height="50" alt="Product Image" style="border-radius:10px;"></span>{{ strip_tags(Str::limit($productDetail['product_name'], 20, '...')) }}
                            </td>
                            <td>{{ $orders->order_id }}</td>
                            <td>{{ $productDetail['purchase_quantity'] }}</td>
                            <td>₦{{ $productDetail['purchase_total_price'] }}</td>
                        </tr>
                        @php
                        $subTotal +=
                        $productDetail['regular_price'] * $productDetail['purchase_quantity'];
                        $total += $productDetail['purchase_total_price'];
                        @endphp
                        @endforeach
                        @php
                        $saveTotal = $subTotal - $total;
                        @endphp
                        <tr>
                            <td>
                                <p style="color: #FF8300;font-weight: 600;">Subtotal</p>
                                <p>Save</p>
                                <p>Total</p>
                                <p>Discount</p>
                                <p>Shipping Charges</p>
                                <p style="color: #232321; font-weight: 600;">Total</p>
                            </td>
                            <td></td>
                            <td></td>
                            <td>
                                <p style="color: #FF8300; font-weight: 600;">₦{{ number_format($subTotal, 2) }}
                                </p>
                                <p>₦{{ number_format($saveTotal, 2) }}</p>
                                <p>₦{{ number_format($subTotal - $saveTotal, 2) }}</p>
                                <p>₦{{ number_format($orders->coupon_discount, 2) }} </p>
                                <p>₦{{ is_null($orders->shipping_charges)?'0.00':number_format($orders->shipping_charges, 2) }} </p>
                                <p style="color: #232321; font-weight: 600;">
                                    ₦{{ number_format($orders->net_amount, 2) }}</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<div class="modal modal_user_view fade" id="modal-default">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Customer Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="user-details">
                <!-- User details will be loaded here -->
            </div>
        </div>
    </div>
</div>
<div class="modal modal_user_view fade" id="modal-address">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Customer Delivery Address</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="user-details">
                <div class="profile-text-field">
                    @php
                    $address = json_decode($orders->shiping_address_id, true) ?? [];
                    @endphp
                    <h5>Deliver to</h5>
                    <p><b>Address:</b> {{ $orders->formatted_address }}</p>
                    <p><b>Customer Name:</b> {{ $orders->shipping_address['name'] }}</p>
                    <p><b>Phone:</b> {{ $orders->customer_phone }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('customJs')
    <script>
        document.getElementById('printButton').addEventListener('click', function () {
            var orderId = '{{ $orders->id }}';
            var printWindow = window.open(base_url +'order/invoice/' + orderId, '_blank');
            printWindow.focus();

            printWindow.onload = function() {
                printWindow.print();
            };
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '.view-user', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('user.show', '') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        var user = response;
                        $.each(response, function(index, user) {

                            var userImage = user.image ?
                                '{{ asset('admin-assets/assets/img/profile_img/user') }}/' +
                                user.image :
                                '{{ asset('admin-assets/assets/img/profile_img/user/common.png') }}';
                            var name = (user.first_name ?? '') + ' ' + (user.last_name ?? '');
                            var full_name = name ? name : '';
                            var email = user.email ? user.email : '';
                            var country_code = user.country_code ? user.country_code :
                                '';
                            var country = user.country ? user.country : '';
                            var address = user.shipping_address ? user.shipping_address : 'No address available';

                            $('#user-details').html(
                                '<div class="text-center">' +
                                '<img class="profile-user-img img-fluid img-circle" src="' +
                                userImage + '" alt="User profile picture">' +
                                '</div>' +
                                '<h3 class="profile-username text-center">' +
                                full_name + '</h3>' +
                                '<ul class="list-group list-group-unbordered mb-3">' +
                                '<li class="list-group-item"><b>Email</b> <div class="float-right">' +
                                email + '</div></li>' +
                                '<li class="list-group-item"><b>Mobile</b> <div class="float-right">' +
                                country_code + ' ' + (user.phone ?? '') + '</div></li>' +
                                '<li class="list-group-item"><b>Country</b> <div class="float-right">' +
                                country + '</div></li>' +
                                '<li class="list-group-item"><b>Status</b> <div class="float-right">' +
                                (user.status == 1 ? 'Active' : 'Inactive') +
                                '</div></li>' +
                                '</ul>'
                            );
                        })
                    }
                });
            });
        });
    </script>
@endsection