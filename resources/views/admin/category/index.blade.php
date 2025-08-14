@extends('admin.index')
@section('title', 'Category')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Category</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">All Category</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="add_new_category">
                        <img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" width="16"
                            height="16" alt="Add_circle">
                        <button type="button" data-toggle="modal" data-target="#modal-default">Add Category</button>
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
                            <h6>All Category</h6>
                            <div class="th_search">
                                <form id="searchForm" action="{{ route('category.index') }}" method="GET" class="d-flex">
                                    <input type="text" placeholder="Search Category Name" name="keyword"
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
                                        <th>Sr. no.</th>
                                        <th>Category Id</th>
                                        <th style="width:15%">Image</th>
                                        <th>Category Name</th>
                                        <th>Parent Category</th>
                                        <th>Category Order</th>
                                        <th style="width:15%">Status</th>
                                        <th style="width:10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="categoryTableBody">
                                    @if ($categories->isNotEmpty())
                                        @foreach ($categories as $category)
                                            <tr>
                                                <td class="sr_no">
                                                    {{ ($categories->currentpage() - 1) * $categories->perpage() + $loop->index + 1 }}
                                                </td>
                                                <td>
                                                    {{ $category->id }}
                                                </td>
                                                <td class="img_name_gap">
                                                    <img class="" width="50px" height="50px"
                                                        src="{{ asset('admin-assets/assets/img/category') }}/{{ $category->image }}"
                                                        alt="category picture" style="border-radius:10px;">
                                                </td>
                                                <td>
                                                    @if($category->parent_id)
                                                        {{ '--' .$category->category_name }}
                                                    @else
                                                        <strong>{{ $category->category_name }}</strong>
                                                    @endif
                                                </td>
                                                <td>{{ $category->parent_id ? $category->parent->category_name : '' }}</td>
                                                <td>{{ $category->category_order ? $category->category_order : '' }}</td>
                                                <td>
                                                    @if ($category->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="" class="edit_category admin_edit_category text-info"
                                                            category_id="{{ $category->id }}"
                                                            category_name="{{ $category->category_name }}"
                                                            parent_category="{{ $category->parent_id }}"
                                                            category_icon="{{ $category->image }}"
                                                            category_icon_path="{{ asset('admin-assets/assets/img/category') }}"
                                                            category_banner="{{ $category->banner_image }}"
                                                            category_banner_path="{{ asset('admin-assets/assets/img/category_banner_image') }}"
                                                            category_status="{{ $category->status }}"
                                                            category_order="{{ $category->category_order }}">
                                                            <i class="fa fa-pencil"
                                                                style="margin-right:10px; font-size:18px; gap:3px;"></i>
                                                        </a>
                                                        <form method="post"
                                                            action="{{ route('category.destroy', $category->id) }}">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="{{ route('category.destroy', $category->id) }}"
                                                                class="delete_category text-danger admin_delete_category show_confirm">
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
                    </div>
                    <div class="card-footer clearfix category_foot" id="paginationLinks">
                        {{ $categories->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal_category fade" id="modal-default">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add_new_category_form" name="add_new_category_form" action="{{ route('category.store') }}"
                        method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="form-group upload_caticon_outer">
                                <div class="containers">
                                    <div class="imageWrapper">
                                        <img class="image show_cat_icon" src="">
                                    </div>
                                </div>
                                <button class="file-upload" style="color: #000000">
                                    <input type="file" name="category_icon" class="cat-file-input category_image"
                                        accept="image/png, image/gif, image/jpeg, image/jpg, image/webp">Choose File
                                </button>
                            </div>

                            <div class="form-group">
                                <label for="parent_id">Choose Parent Category</label>
                                <select name="parent_id" id="parent_id" class="form-control">
                                    <option value="">Select Parent Category</option>
                                    @if ($categories->isNotEmpty())
                                        @foreach ($categoriess as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                            @if (count($category->subcategory))
                                                @foreach ($category->subcategory as $subcategory)
                                                    <option value="{{ $subcategory->id }}">--
                                                        {{ $subcategory->category_name }}</option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="category_name">Category Name</label>
                                <input type="text" name="category_name" class="form-control category_name"
                                    placeholder="Enter Category Name">
                            </div>

                            <div class="form-group banner_images">
                                <label for="banner_image">Banner Image</label>
                                <input type="file" name="banner_image" class="form-control banner_image"
                                    placeholder="Enter Banner Image">
                            </div>

                            <div class="form-group category_order">
                                <label for="category_order">Category Order</label>
                                <input type="number" name="category_order" class="form-control category_order"
                                    placeholder="Enter category order">
                            </div>

                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="category_status" class="form-control" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="card-footer" style="background:none; padding:0;">
                            <button type="submit" class="add_new_brand_popup">Submit</button>
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
            function loadCategories(data = {}) {
                $.ajax({
                    url: "{{ route('category.index') }}",
                    type: "GET",
                    data: data,
                    success: function(response) {
                        $('#categoryTableBody').empty();
                        $.each(response.data, function(index, category) {
                            $('#categoryTableBody').append(
                                '<tr>' +
                                '<td class="sr_no">' + ((response.current_page - 1) *
                                    response.per_page + index + 1) + '</td>' +
                                '<td>' + (category.id) + '</td>' +
                                '<td class="img_name_gap">' +
                                '<img width="50px" height="50px" src="{{ asset('admin-assets/assets/img/category') }}/' +
                                category.image +
                                '" alt="category picture" style="border-radius:10px;">' +
                                '</td>' +
                               '<td>' +
                                    (category.parent_id ? '--' + category.category_name : '<strong>' + category.category_name + '</strong>') +
                                '</td>' +
                                '<td>' + (category.parent_id ? category
                                    .parent.category_name : '') + '</td>' +
                                '<td>' + (category.category_order ? category.category_order : '')
                                + '</td>' +
                                '<td>' + (category.status == 1 ?
                                    '<span class="badge bg-success">Active</span>' :
                                    '<span class="badge bg-danger">Inactive</span>'
                                ) + '</td>' +
                                '<td>' +
                                '<div class="d-flex">' +
                                '<a href="" class="edit_category admin_edit_category text-info" ' +
                                'category_id="' + category.id + '" ' +
                                'category_name="' + category.category_name + '" ' +
                                'parent_category="' + (category.parent_id ? category.parent_id: '') + '" ' +
                                'category_icon="' + category.image + '" ' +
                                'category_icon_path="{{ asset('admin-assets/assets/img/category') }}" ' +
                                'category_banner="' + category.banner_image + '" ' +
                                'category_banner_path="{{ asset('admin-assets/assets/img/category_banner_image') }}" ' +
                                'category_status="' + category.status + '" ' +
                                'category_order="' + category.category_order + '">' +
                                '<i class="fa fa-pencil" style="margin-right:10px; font-size:18px; gap:3px;"></i>' +
                                '</a>' +
                                '<form method="post" action="{{ route('category.destroy', '') }}/' +
                                category.id + '">' +
                                '@method('DELETE')' +
                                '@csrf' +
                                '<a href="{{ route('category.destroy', '') }}/' +
                                category.id +
                                '" class="delete_category text-danger admin_delete_category show_confirm">' +
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
                loadCategories({
                    keyword: keyword
                });
            });

            $('#parent_id').change(function() {
                if ($(this).val() == '') {
                    $('.banner_images').show();
                } else {
                    $('.banner_images').hide();
                }
            });
            
            $(document).on('click', '#paginationLinks a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                var keyword = $('#keyword').val();
                loadCategories({
                    page: page,
                    keyword: keyword
                });
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
