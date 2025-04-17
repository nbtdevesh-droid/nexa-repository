@extends('admin.index')
@section('title', 'WareHouse')
@section('css')
    <!-- intel input -->
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/intlTelInput.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/demo.css') }}" />
    <style>
        .iti .iti__selected-dial-code {
            color: #000;
            margin: 0 !important;
        }
    </style>
@endsection
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>WareHouse</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">All WareHouse</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="add_new_staff">  
                         <a href="{{ route('warehouse.create') }}"><img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" height="16" alt="Add_circle"> Add WareHouse</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="datatable_dashboard header-table">
                        <div class="table-header-contant-text">
                            <h6>All WareHouse</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="w-100" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important; border-top:1px solid #FF8300 !important;">
                                        <th class="pro_duct_th">ID</th>
                                        <th style="width:15%">WareHouse Name</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Address</th>
                                        <th style="width:15%">Status</th>
                                        <th style="width:10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="warehouseTableBody">
                                    @if ($warehouse->isNotEmpty())
                                        @foreach ($warehouse as $house)
                                            <tr>
                                                <td class="sr_no">
                                                    {{ ($warehouse->currentpage() - 1) * $warehouse->perpage() + $loop->index + 1 }}
                                                </td>
                                                <td>{{ $house->warehouse_name }}</td>
                                                <td>{{ $house->contact_name }}</td>
                                                <td>{{ $house->contact_email }}</td>
                                                <td>{{ $house->country_code }} {{ $house->contact_number }}</td>
                                                <td>{{ $house->street_address }}<br> {{ $house->city. ', '. $house->state . ', ' . $house->zip_code }}<br>{{ $house->country }}</td>
                                                <td>
                                                    @if ($house->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('warehouse.edit', $house->id) }}" type="button" class="text-info"><i class="fa fa-pencil" style="font-size:18px; gap:3px;"></i></a> &nbsp;&nbsp;
                                                        <form method="post"
                                                            action="{{ route('warehouse.destroy', $house->id) }}">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="{{ route('warehouse.destroy', $house->id) }}"
                                                                class="delete_warehouse text-danger admin_delete_warehouse show_confirm">
                                                                <i class="fa fa-trash"
                                                                    style="margin-right:10px; font-size:18px;"></i>
                                                            </a>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" style="text-align: center;">Records Not Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="clearfix warehouse_foot" id="paginationLinks">
                            {{ $warehouse->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
