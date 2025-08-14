@extends('admin.index')
@section('title', 'Edit-Product')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Edit Product</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('products.index') }}">All Products</a></li>
                        <li class="breadcrumb-item active">Edit Product</li>
                    </ol>
                </div>
                <!-- <div class="col-sm-6 text-right">
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Back</a>
                </div> -->
            </div>
        </div>
    </section>
    <section class="content content_product_block">
        <div class="container-fluid">
            <form method="POST" name="ProductForm" id="ProductForm" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-md-7 new_pro_duct_add_block">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="poduct_input_all_product form-group">
                                    <label for="product_name">Product Name</label>
                                    <input type="text" name="product_name" id="product_name" value="{{ $product->product_name }}" class="form-control" placeholder="Product Name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="poduct_input_all_product form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="summernote" cols="30" rows="10" value="{{ $product->description }}" class="form-control" placeholder="Description">{{ $product->description }}</textarea>
                                    <p><span class="description_error" style="color: red;"></span></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="parent_category">Parent Category</label>
                                    <select name="parent_category" id="parent_category" class="form-control">
                                        <option value="" hidden>Select Parent Category</option>
                                        @if ($categories->isNotEmpty())
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ $product->parent_category == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="child_category">Child Category</label>
                                    <select name="child_category" id="child_category" class="form-control">
                                        <option value="" hidden>Select Child Category</option>
                                        @php
                                            $childCategories = App\Models\Category::where(
                                                'parent_id',
                                                $product->parent_category,
                                            )->get();
                                        @endphp
                                        @if ($childCategories->isNotEmpty())
                                            @foreach ($childCategories as $child_category)
                                                <option value="{{ $child_category->id }}"
                                                    {{ $product->child_category == $child_category->id ? 'selected' : '' }}>
                                                    {{ $child_category->category_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="poduct_input_all_product form-group">
                                    <label for="category">Brand Name</label>
                                    <select name="brand" id="brand" class="form-control">
                                        <option value="" hidden>Select Brand</option>
                                        @if ($brands->isNotEmpty())
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->brand_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="sku">SKU (Stock Keeping Unit)</label>
                                    <input type="text" name="sku" id="sku" value="{{ $product->sku }}" class="form-control" placeholder="sku">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="stock_quantity">Stock Quantity</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity" value="{{ $product->quantity }}" class="form-control" placeholder="Stock Quantity">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="regular_price">Regular Price</label>
                                    <input type="number" name="regular_price" id="regular_price" value="{{ $product->regular_price }}" class="form-control" placeholder="Regular Price">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="sale_price">Sale Price</label>
                                    <input type="number" name="sale_price" id="sale_price" value="{{ $product->sale_price }}" class="form-control" placeholder="Sale Price">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="min_order">Min Order</label>
                                    <input type="number" name="min_order" id="min_order" value="{{ $product->min_order }}" class="form-control" placeholder="Minimum Order">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="product_status">Product Status</label>
                                        <select name="status" id="status" class="form-control">
                                        <option value="1" {{ $product->status == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ $product->status == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="product_input_all_product">
                                    <input type="checkbox" name="flash_deal" id="flash_deal" value="1"  {{ $product->flash_deal == '1' ? 'checked' : '' }}>
                                    <label for="flash_deal">Flash Deals</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                        <h2 class="h4 mb-3">Feature Image</h2>
                            <div class="gallery_img_block">
                                <div id="feature_image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                    <i class="fa-regular fa-image"></i>
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-feature">
                            <div class="col-md-4">
                                <div class="card">
                                    <img src="{{ asset('admin-assets/assets/img/product/feature_img/') }}/{{ $product->feature_image }}"
                                        class="feature_img card-img-top" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                              <h2 class="h4 mb-3">Gallery Image</h2>
                            <div class="gallery_img_block">
                                <div id="gallery_image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                    <i class="fa-regular fa-image"></i>
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                                @if ($errors->has('gallery_image'))
                                    <span class="text-danger">{{ $errors->first('gallery_image') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="row" id="product-gallery">
                            @if ($product->gallery_image)
                                @php $gallery_img = json_decode($product->gallery_image, true); @endphp
                                @foreach ($gallery_img as $key => $image)
                                    <div class="col-md-4" id="image-row-{{ $key }}">
                                        <div class="card">
                                            <input type="hidden" name="old_image_array[]" value="{{ $image }}">
                                            <img src="{{ asset('admin-assets/assets/img/product/gallery_img/' . $image) }}" class="card-img-top" alt="" height="100px;">
                                            <div class="card-body product_save_delet_btn">
                                                <a href="javascript:void(0)" onclick= "deleteGalleryImage({{ $key }})" class="btn">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3 product_save_cancal_btn">
                    <button class="btn">Save</button>
                    <a href="{{ route('products.index') }}" class="btn">Cancel</a>
                </div>
            </form>
        </div>
    </section>
@endsection
@section('customJs')
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 400,
                callbacks: {
                    onImageUpload: function(files) {
                        console.log('Image upload function called'); // Debugging
                        for (var i = 0; i < files.length; i++) {
                            uploadImage(files[i]);
                        }
                    }
                }
            });

            function uploadImage(file) {
                var form_data = new FormData();
                form_data.append('file', file);
                form_data.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route("upload.summernote.image") }}',
                    type: 'POST',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function(url) {
                        console.log('Uploaded Image URL:', url); // Debugging
                        
                        var imgNode = document.createElement('img');
                        imgNode.src = url;
                        
                        $('#summernote').summernote('insertNode', imgNode);
                    },
                    error: function(xhr) {
                        alert("Image upload failed! Error: " + xhr.responseText);
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#ProductForm").validate({
                rules: {
                    product_name: {
                        required: true,
                    },
                    parent_category: {
                        required: true,
                    },
                    child_category: {
                        required: true,
                    },
                    sku: {
                        required: true,
                    },
                    stock_quantity: {
                        required: true,
                    },
                    regular_price: {
                        required: true,
                        number: true,
                    },
                    sale_price: {
                        required: function () {
                            // Require sale price only if flash deal is enabled
                            return $('#flash_deal').is(':checked') && $('#sale_price').val() === '';
                        },
                        number: true,
                        lessThanRegular: "#regular_price"
                    },
                    min_order: {
                        required: true,
                        number: true,
                    },
                },
                messages: {
                    product_name: "Please enter a product name",
                    parent_category: "Please select a parent category",
                    child_category: "Please select a child category",
                    sku: "Please enter an SKU",
                    stock_quantity: "Please enter stock quantity",
                    regular_price: "Please enter a regular price",
                    sale_price: {
                        required: "Please enter a sale price if the flash deal is enabled",
                        number: "Please enter a valid number",
                        lessThanRegular: "Sale price must be less than the regular price"
                    },
                    min_order: {
                        required: "Please enter minimum order quantity",
                        number: "Please enter a valid number"
                    },
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function (form) {
                    console.log('Form validation passed, preparing for AJAX submission.');

                    var formData = new FormData(form);

                    // AJAX form submission
                    $.ajax({
                        url: "{{ route('products.update', $product->id) }}",
                        method: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        cache: false,
                        beforeSend: function() {
                            console.log('Sending AJAX request...');
                        },
                        success: function (response) {
                            console.log('AJAX request succeeded', response);
                            if (response.success) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                    });
                    return false; 
                }
            });

            // Custom method to check if sale price is less than regular price
            $.validator.addMethod("lessThanRegular", function (value, element, param) {
                var regularPrice = parseFloat($(param).val());
                var salePrice = parseFloat(value);
                return isNaN(salePrice) || salePrice < regularPrice;
            }, "Sale price must be less than the regular price");

            // Custom method to ensure expire date is greater than start date
            $.validator.addMethod("greaterThan", function (value, element, param) {
                var startDateTime = $(param).val();
                return Date.parse(value) > Date.parse(startDateTime);
            }, "Expire date & time must be greater than the start date & time");

            // Custom method to validate date-time format
            $.validator.addMethod("validDateTime", function (value, element) {
                return moment(value, 'YYYY-MM-DD HH:mm:ss', true).isValid();
            }, "Please enter a valid date & time in the format YYYY-MM-DD HH:mm:ss");
        });


        $(document).ready(function() {
            $('body').on('change', '#parent_category', function() {
                var parent_id = $(this).val();
                if (parent_id) {
                    categoryChange(parent_id);
                } else {
                    $('#child_category').html('<option value="" hidden>Select Child Category</option>');
                }
            });
        });

        // Function to get child categories
        function categoryChange(parent_id) {
            $.ajax({
                type: "POST",
                url: "{{ url('get-child-categories') }}",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    parent_id: parent_id
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 'success') {
                        var selectOption = '<option value="" hidden>Select Child Category</option>';
                        $.each(response.data, function(index, value) {
                            if (value.category_name != null) {
                                selectOption += `<option value="${value.id}" data-parent-id="${value.parent_id}">${value.category_name}</option>`;
                            }
                        });
                        $('#child_category').html(selectOption);
                    } else {
                        $('#child_category').html('<option value="" hidden>No Child Categories Available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred while fetching child categories. Please try again.');
                }
            });
        }

        Dropzone.autoDiscover = false;
        const dropzone = $("#gallery_image").dropzone({
            url: "{{ route('product-images.update') }}",
            maxFiles: 10,
            paramName: 'image',
            params: {
                'product_id': '{{ $product->id }}'
            },
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/jpg,image/webp,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                // $("#image_id").val(response.image_id);
                console.log(response)

                var html = `<div class="col-md-4" id="image-row-${response.image_id}"><div class="card">
                <input type="hidden" name="image_array[]" value="${response.image_id}">
                <img src="${response.ImagePath}" class="card-img-top" alt="" width="300px" max-height="100px">
                <div class="card-body">
                    <a href="javascript:void(0)" onclick= "deleteGalleryImage(${response.image_id})" class="btn btn-danger">Delete</a>
                </div>
            </div></div>`;

                $('#product-gallery').append(html);
            },
            complete: function(file) {
                this.removeFile(file);
            }
        });
        Dropzone.autoDiscover = false;
        const dropzone1 = new Dropzone("#feature_image", {
            url: "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: "image/jpeg,image/png,image/jpg,image/webp,image/gif",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                // Update the hidden input field with the new image ID
                $("#image_id").val(response.image_id);

                // Clear the existing feature image section
                $('#product-feature').html('');

                // Append the new feature image
                var html = `
                    <div class="col-md-4" id="image-row-${response.image_id}">
                        <div class="card">
                            <input type="hidden" name="feature_image" value="${response.image_id}">
                            <img src="${response.ImagePath}" class="card-img-top" alt="" width="300px" max-height="100px">
                        </div>
                    </div>
                `;

                // Append the newly uploaded image to the feature section
                $('#product-feature').append(html);

                // Hide any previous images (this is optional if you don't want multiple images showing up)
                $('.feature_img').hide();
            },
            complete: function(file) {
                this.removeFile(file); // Automatically remove the file preview once uploaded
            },
            maxfilesexceeded: function(file) {
                this.removeAllFiles(); // Remove all files if max file limit is exceeded
                this.addFile(file);    // Add the new file
            }
        });


        function deleteImage(id) {
            $('#image-row-' + id).remove();
        }

        function deleteGalleryImage(id) {
            swal({
                title: "Are you sure?",
                text: "Do you want to delete this image?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('product-images.destroy') }}", 
                        type: "DELETE", // Ensure Laravel detects it correctly
                        data: {
                           _token: "{{ csrf_token() }}",
                            product_id: "{{ $product->id }}",
                            key: id
                        },
                        success: function(response) {
                            if (response.status === true) {
                                $('#image-row-' + id).remove(); // Remove deleted image row
                                updateGallery(response.updated_gallery); // Refresh gallery UI
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            toastr.error("An error occurred while deleting the image.");
                        }
                    });
                }
            });
        }


        function updateGallery(updatedGallery) {
            $('#product-gallery').empty();

            $.each(updatedGallery, function(index, image) {
                var html = `<div class="col-md-4" id="image-row-${index}">
                                <div class="card">
                                    <input type="hidden" name="image_array[]" value="${index}">
                                    <img src="{{ asset('admin-assets/assets/img/product/gallery_img/') }}/${image}" class="card-img-top" alt="" width="300px" max-height="100px">
                                    <div class="card-body">
                                        <a href="javascript:void(0)" onclick="deleteGalleryImage(${index})" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>`;
                $('#product-gallery').append(html);
            });
        }
    </script>
@endsection