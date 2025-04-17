@extends ('admin/index')
@section('title', 'Product')
@section('content')
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <h4>Validation Errors:</h4>
        <ul>
            @foreach ($errors->getMessages() as $row => $errorMessages)
                <li>
                    <strong>Row {{ $row }}:</strong>
                    <ul>
                        @foreach ($errorMessages as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </div>
@endif
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-md-3 col-sm-12 col-12">
                <h1>All Product</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">All Product</li>
                </ol>
            </div>
            <div class="col-md-9 col-sm-12 col-12" style="text-align:end;">
                <a href="{{ asset('admin-assets/product.xlsx') }}" class="btn btn-success" style="text-decoration: none;" download>
                    Download Excel Sheet Formate
                </a>
                <button type="button" id="" class="btn btn-success">
                    <form action="{{ route('import.product') }}" method="POST"  id ="importForm" enctype="multipart/form-data">
                        @csrf
                        <label for="file"  style="cursor: pointer;">Import Excel Sheet Products-list</label>
                        <input type="file" name="file" id="file" required style="display:none;">
                    </form>
                </button>
                <button type="button" id="deleteSelected" class="btn btn-danger delete-select-btn">
                    Delete Selected
                </button>
                <div class="add_new_brand">
                    <a href="{{ route('products.create') }}" style="width:200px;">
                     <img src="{{asset('admin-assets/assets/img/menu-icon/Add_circle.svg')}}"  width="16" height="16"  alt="Add_circle"> Add Product</a>
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
                                    <h6 style="width: 150px;">All Product</h6>
                                </th>
                                <th colspan="10">
                                    <div class="th_search th_product">
                                        <form id="searchForm" class="searh_fm" action="{{ route('products.index') }}" method="GET" class="d-flex">
                                            <div class="input-group input-group">
                                                <input type="text" name="keyword" id="keyword" class="form-control float-right" placeholder="Search Product Id/Sku/Name" style="width:305px !important;">
                                            </div>
                                            <button type="submit"><i class="fas fa-search" style="margin-left:-20px;"></i></button>
                                        </form>
                                        <div class="select_sort">
                                            <select class="form-select sort_category" aria-label="Default select example">
                                                <option value='' hidden>Sort By:</option>
                                                <option value=''>All Products</option>
                                                @if ($categories->isNotEmpty())
                                                    @foreach ($categories as $category)
                                                        <?php $dash = ''; ?>
                                                        <option value="{{ $category->id }}" class="select_heading">{{ $category->category_name }}
                                                        </option>
                                                        @if (count($category->subcategory))
                                                            @foreach ($category->subcategory as $subcategory)
                                                                <option value="{{ $subcategory->id }}">
                                                                    {{ $subcategory->category_name }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 ! important;">
                                <th class="pro_duct_th" style="width:10%;"><input type="checkbox" id="selectAll" name="product_delete_all[]" style="margin-right: 5px;">Sr. no.</th>
                                <th style="width:10%;">Product ID</th>
                                <th>Product</th>
                                <th style="width:10%;">SKU</th>
                                <th style="width:10%;">Regular Price</th>
                                <th style="width:10%;">Sale Price</th>
                                <th style="width:7%;">Quantity</th>
                                <th style="width:7%;">View Count</th>
                                <th style="width:7%;">Status</th>
                                <th style="width:7%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            @if($products->isNotEmpty())
                                @foreach ($products as $product)
                                <tr>
                                    <td class="sr_no"><input type="checkbox" name="product_delete[]" class="product-checkbox" data-id="{{ $product->id }}" style="margin-right: 5px;">{{ ($products->currentpage()-1) * $products->perpage() + $loop->index + 1 }}@if($product->flash_deal == '1') <img src="{{ asset('admin-assets/assets/sale.png') }}" width="50px" height="50px"> @endif</td>
                                    <td>{{ $product->formatted_id }}</td>
                                    <td class="img_name_gap">
                                        @if ($product->feature_image != '')
                                        <img class="" width="50px" height="50px" src="{{ asset('admin-assets/assets/img/product/feature_img') }}/{{ $product->feature_image }}" style="border-radius:10px;">
                                        @endif
                                        {{ strip_tags(Str::limit($product->product_name, 20, '...')) }}
                                    </td>
                                    <td>{{$product->sku}}</td>
                                    <td>₦{{ $product->regular_price }}</td>
                                    <td>{{ $product->sale_price ? '₦' . $product->sale_price : ''}}</td>
                                    <td>{{  $product->quantity}} </td>
                                    <td>{{ $product->total_click_count }}</td>
                                    <td>
                                        @if($product->status != 0)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex" style="gap:8px;">
                                            <a href="{{ route('products.edit', $product->id) }}" type="button" class="text-info"><i class="fa fa-pencil"></i></a>
                                            <form method="post" action="{{ route('products.destroy', $product->id) }}">
                                                @method('DELETE')
                                                @csrf
                                                <a href="{{ route('products.destroy', $product->id) }}" class="delete_user text-danger admin_delete_user show_confirm"><i class="fa fa-trash"></i></a>
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
                <div class="card-footer clearfix product_foot" id="paginationLinks">
                    {{ $products->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('customJs')
<script>
    document.getElementById('file').addEventListener('change', function() {
        // Automatically submit the form when a file is selected
        document.getElementById('importForm').submit();
    });
</script>
<script type="text/javascript">
    function generateProductRow(product, index, current_page, per_page) {
        let productRow = '';
        let featureImage = '';
        let sku = '';
        let regularPrice = '';
        let salePrice = '';
        let stockQuantity = '';

        var editUrl = `{{ route('products.edit', ':id') }}`.replace(':id', product.id);
        var deleteUrl = `{{ route('products.destroy', ':id') }}`.replace(':id', product.id);

        featureImage = product.feature_image ?
            `<img width="50px" height="50px" src="{{ asset('admin-assets/assets/img/product/feature_img') }}/${product.feature_image}"  style="border-radius:10px;">` : '';
        sku = product.sku;
        regularPrice = `₦${product.regular_price}`;
        salePrice = product.sale_price ? `₦${product.sale_price}` : '';
        stockQuantity = product.quantity;
        flashDeal = product.flash_deal ? `<img width="50px" height="50px" src="{{ asset('admin-assets/assets/sale.png') }}">` : '';

        productRow = `
            <tr>
                <td class="sr_no"><input type="checkbox" name="product_delete[]" class="product-checkbox" data-id="${product.id}" style="margin-right: 5px;">${((current_page - 1) * per_page + index + 1)}${flashDeal}</td>
                <td>${product.formatted_id}</td>
                <td class="img_name_gap">
                    ${featureImage}
                    ${truncateText(product.product_name, 20)}
                </td>
                <td>${sku}</td>
                <td>${regularPrice}</td>
                <td>${salePrice}</td>
                <td>${stockQuantity}</td>
                <td>${product.total_click_count}</td>
                <td>${product.status != 0 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                <td>
                    <div class="d-flex" style="gap:8px;">
                        <a href="${editUrl}" class="text-info"><i class="fa fa-pencil"></i></a>
                        <form method="post" action="${deleteUrl}">
                            @method('DELETE')
                            @csrf
                            <a href="${deleteUrl}" class="delete_user text-danger admin_delete_user show_confirm"><i class="fa fa-trash"></i></a>
                        </form>
                    </div>
                </td>
            </tr>
        `;

        return productRow;
    }

    // Helper function to truncate text
    function truncateText(text, limit) {
        if (text.length > limit) {
            return text.substring(0, limit) + '...';
        }
        return text;
    }

    $(document).ready(function() {
        // Load products based on search, sort, or pagination
        function loadProducts(url, data) {
            $.ajax({
                url: url,
                type: "GET",
                data: data,
                success: function(response) {
                    $('#productTableBody').empty();
                    $.each(response.data, function(index, product) {
                        $('#productTableBody').append(generateProductRow(product, index, response.current_page, response.per_page));
                    });
                    $('#paginationLinks').html(response.links);

                    restoreCheckedProducts();
                },
                error: function() {
                    alert('Failed to load products.');
                }
            });
        }

        // Search form submission
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            var keyword = $('#keyword').val();
            var category_id = $('.sort_category').val() || '';
            loadProducts("{{ route('products.index') }}", { keyword: keyword, category_id: category_id });
        });

        // Category sorting
        $('.sort_category').on('change', function(e) {
            e.preventDefault();
            var keyword = $('#keyword').val() || '';
            var category_id = $('.sort_category').val();
            loadProducts("{{ route('products.index') }}", { keyword: keyword, category_id: category_id });
        });

        // Pagination links click
        $(document).on('click', '#paginationLinks a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var keyword = $('#keyword').val() || '';
            var category_id = $('.sort_category').val() || '';

            loadProducts("{{ route('products.index') }}", { page: page , keyword: keyword, category_id: category_id});
        });

        $('#deleteSelected').on('click', function(e) {
            e.preventDefault();
            var selectedProducts = [];
            $('input[name="product_delete[]"]:checked').each(function() {
                selectedProducts.push($(this).data('id'));
            });

            if (selectedProducts.length === 0) {
                alert('No products selected for deletion');
                return;
            }

            if (confirm('Are you sure you want to delete the selected products?')) {
                $.ajax({
                    url: "{{ route('product.bulkDelete') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_ids: selectedProducts
                    },
                    success: function(response) {
                        if(response.success == true){
                            toastr.success(response.message);
                            window.location.href = "{{ route('products.index') }}";

                        }else{
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Something went wrong, please try again.');
                    }
                });
            }
        });

        $('#selectAll').on('change', function() {
            $('.product-checkbox').prop('checked', this.checked);
        });
    });

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
