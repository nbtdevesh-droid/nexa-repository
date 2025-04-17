@extends ('admin/index')
@section('title', 'All-Events')
@section('content')
<?php
use Carbon\Carbon;
?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>All Events</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">All Events</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="add_new_staff">
                    <img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" width="16"
                        height="16" alt="Add_circle">
                    <a href="{{ route('setting.add-flash_deal') }}">Add Events</a>
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
                    <h6>All Events</h6>
                    <div class="table-responsive">
                        <table id="all-coupon-table" cellpadding="0" cellspacing="0" class="w-100">
                            <thead>
                                <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important;">
                                    <th class="pro_duct_th">ID</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($deals->isNotEmpty())
                                @foreach ($deals as $deal)
                                <tr>
                                    <td class="sr_no">{{ ($deals->currentpage()-1) * $deals->perpage() + $loop->index + 1 }}</td>
                                    <td>{{ $deal->start_flash_deal }}</td>
                                    <td>{{ $deal->end_flash_deal }}</td>
                                    <td>{{ $deal->quantity }}</td>
                                    <td>
                                        @if (Carbon::now()->greaterThan(Carbon::parse($deal->end_flash_deal)->setTimezone(config('app.timezone'))))
    <span class="text-danger">Expired</span>
@else
    <span class="text-success">Active</span>
@endif

                                    
                                    </td>

                                    <td>
                                        <div class="d-flex" style="gap:6px;">
                                            <a href="{{ route('setting.deal.edit', $deal->id) }}" type="button"
                                                class="text-info"><i class="fa fa-pencil"></i></a>
                                            <!-- &nbsp;&nbsp;&nbsp;&nbsp; -->
                                            <form method="post"
                                                action="{{ route('setting.deal.destroy', $deal->id) }}">

                                                @csrf
                                                <a href="{{ route('setting.deal.destroy', $deal->id) }}" class="delete_user text-danger admin_delete_coupon show_confirm"><i class="fa fa-trash"></i></a>
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
                <div class="card-footer clearfix product_foot" id="paginationLinks">
                    {{ $deals->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@stop