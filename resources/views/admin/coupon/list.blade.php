@extends ('admin/index')
@section('title', 'All-Coupon')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>All Coupons</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">All Coupons</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="add_new_staff">
                        <img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" width="16"
                            height="16" alt="Add_circle">
                        <a href="{{ route('coupon.create') }}">ADD COUPONS</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="datatable_dashboard header-table">
                        <h6>All Coupons</h6>
                        <div class="table-responsive">
                            <table id="all-coupon-table" cellpadding="0" cellspacing="0" class="w-100">
                                <thead>
                                    <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important;">
                                        <th class="pro_duct_th">ID</th>
                                        <th>Coupon</th>
                                        <th>Coupon Title</th>
                                        <th>Discount Category</th>
                                        <th>Discount Type</th>
                                        <th>Discount</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($coupons->isNotEmpty())
                                        @foreach ($coupons as $coupon)
                                            <tr>
                                                <td class="sr_no">{{ $loop->iteration }}</td>
                                                <td>{{ $coupon->code }}</td>
                                                <td>{{ $coupon->title }}</td>
                                                <td>@if($coupon->coupon_type == 'product_wise') Product @elseif($coupon->coupon_type == 'category_wise') Category @elseif($coupon->coupon_type == 'user_wise') User @endif</td>
                                                <td>{{ $coupon->amount_type }}</td>
                                                <td>
                                                    @if ($coupon->amount_type == 'percentage')
                                                        {{ $coupon->amount }}%
                                                    @else
                                                        {{ $coupon->amount }}
                                                    @endif
                                                </td>
                                                <td>{{ $coupon->coupon_start_date }}</td>
                                                <td>{{ $coupon->coupon_end_date }}</td>
                                                <td>
                                                    @if ($coupon->status == 1)
                                                        <a href="{{ route('coupon.show', $coupon->id) }}"><span
                                                                class="badge bg-success">Active</span></a>
                                                    @else
                                                        <a href="{{ route('coupon.show', $coupon->id) }}"><span
                                                                class="badge bg-danger">Inactive</span></a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex" style="gap:6px;">
                                                        <a href="{{ route('coupon.edit', $coupon->id) }}" type="button"
                                                            class="text-info"><i class="fa fa-pencil"></i></a>
                                                        <!-- &nbsp;&nbsp;&nbsp;&nbsp; -->
                                                        <form method="post"
                                                            action="{{ route('coupon.destroy', $coupon->id) }}">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="{{ route('coupon.destroy', $coupon->id) }}" class="delete_user text-danger admin_delete_coupon show_confirm"><i class="fa fa-trash"></i></a>
                                                        </form>
                                                    </div>
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
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
