@extends('admin.index')
@section('title', 'Brand')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>All Brand</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Brands</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="add_new_brand">
                        <img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" width="16"
                            height="16" alt="Add_circle">
                        <button type="button" data-toggle="modal" data-target="#modal-default">Add Brand</button>
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
                            <h6>All Brand</h6>
                            <div class="th_search">
                                 <form id="searchForm" action="{{ route('brand.index') }}" method="GET"
                                    class="d-flex">
                                    <input type="text" placeholder="Search Brand Name" name="keyword"
                                        id="keyword"class="table-header-search-btn">
                                    <button type="submit"><i class="fas fa-search"
                                            style="margin-left:-60px !important; color:#FF8300;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                       <div class="table-responsive">
                            <table class="w-100" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important; border-top:1px solid #FF8300 !important;">
                                        <th class="pro_duct_th">Sr. no.</th>
                                        <th class="pro_duct_th">Brand Id</th>
                                        <th>Brand</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="brandTableBody">
                                    @if ($brands->isNotEmpty())
                                        @foreach ($brands as $brand)
                                            <tr>
                                                <td class="sr_no">
                                                    {{ ($brands->currentpage() - 1) * $brands->perpage() + $loop->index + 1 }}</td>
                                                <td class="sr_no">{{ $brand->id }}</td>
                                                <td class="img_name_gap">{{ $brand->brand_name }}</td>
                                                <td>
                                                    @if ($brand->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex" style="gap:8px;">
                                                        <a href="" class="edit_brand admin_edit_brand text-info"
                                                            brand_id="{{ $brand->id }}" brand_name="{{ $brand->brand_name }}"
                                                            brand_status="{{ $brand->status }}"
                                                            action="{{ route('brand.update', $brand->id) }}"><i
                                                                class="fa fa-pencil"></i></a>
                                                        <form method="post" action="{{ route('brand.destroy', $brand->id) }}">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="{{ route('brand.destroy', $brand->id) }}"
                                                                class="delete_brand text-danger admin_delete_brand show_confirm"><i
                                                                    class="fa fa-trash"></i></a>
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
                    </div>
                    <div class="card-footer clearfix category_foot" id="paginationLinks">
                        <!-- {{ $brands->links() }} -->
                        {{ $brands->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal_brand fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Brand</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add_new_brand_form" name="add_new_brand_form" action="{{ route('brand.store') }}"
                        method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="Inputbrand_name">Brand Name</label>
                                <input type="text" name="brand_name" class="form-control brand_name"
                                    placeholder="Enter Brand Name">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="brand_status" class="form-control" id="brand_status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer" style="padding:0; background:none;">
                            <button type="submit" id="savedata" class="add_new_brand_popup"
                                value="Submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('customJs')
    <script type="text/javascript">
        $(document).ready(function() {
            function loadBrands(data = {}) {
                $.ajax({
                    url: "{{ route('brand.index') }}",
                    type: "GET",
                    data: data,
                    success: function(response) {
                        $('#brandTableBody').empty();
                        $.each(response.data, function(index, brand) {
                            $('#brandTableBody').append(
                                '<tr>' +
                                '<td class="sr_no">' + ((response.current_page -
                                    1) * response.per_page + index + 1) + '</td>' +
                                '<td class="sr_no">' + (brand.id) + '</td>' +
                                '<td class="img_name_gap">' + brand.brand_name +
                                '</td>' +
                                '<td>' + (brand.status == 1 ?
                                    '<span class="badge bg-success">Active</span>' :
                                    '<span class="badge bg-danger">Inactive</span>'
                                ) + '</td>' +
                                '<td>' +
                                '<div class="d-flex" style="gap:8px;">' +
                                '<a href="" class="edit_brand admin_edit_brand text-info" ' +
                                'brand_id="' + brand.id + '" ' +
                                'brand_name="' + brand.brand_name + '" ' +
                                'brand_status="' + brand.status + '">' +
                                '<i class="fa fa-pencil" style="margin-right:10px; font-size:18px; gap:3px;"></i>' +
                                '</a>' +
                                '<form method="post" action="{{ route('brand.destroy', '') }}/' +
                                brand.id + '">' +
                                '@method('DELETE')' +
                                '@csrf' +
                                '<a href="{{ route('brand.destroy', '') }}/' +
                                brand.id +
                                '" class="delete_brand text-danger admin_delete_brand show_confirm">' +
                                '<i class="fa fa-trash" style="margin-right:10px; font-size:18px;"></i>' +
                                '</a>' +
                                '</form>' +
                                '</div>' +
                                '</td>' +
                                '</tr>'
                            );
                        });
                        $('#paginationLinks').html(response.links);
                    }
                });
            }

            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var keyword = $('#keyword').val();
                loadBrands({ keyword: keyword });
            });

            $(document).on('click', '#paginationLinks a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                var keyword = $('#keyword').val();
                loadBrands({ page: page, keyword: keyword });
            });
        });
        // Use event delegation to attach the click event to dynamically added elements
        $(document).on('click', '.show_confirm', function(event) {
            event.preventDefault(); // Prevent the default form submission

            var form = $(this).closest("form"); // Find the closest form
            var name = $(this).data("name"); // Optional: Get data-name attribute if needed

            swal({
                title: `Are you sure you want to delete this record?`,
                text: "If you delete this, it will be gone forever.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    form.submit(); // Submit the form if the user confirms deletion
                }
            });
        });
    </script>
@endsection
