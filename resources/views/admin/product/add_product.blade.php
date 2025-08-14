@extends('admin.index')
@section('title', 'Add-Product')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-sm-6">
                    {{-- <a href="{{ route('products.index') }}" class="btn btn-primary back_btn">Back</a> --}}
                    <h1>Add New Product</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('products.index') }}">All Products</a></li>
                        <li class="breadcrumb-item active">Add New Product</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content content_product_block">
        <div class="container-fluid">
            <form method="POST" name="ProductForm" id="ProductForm" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-7 new_pro_duct_add_block">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="poduct_input_all_product form-group">
                                    <label for="product_name">Product Name</label>
                                    <input type="text" name="product_name" id="product_name" value="" class="form-control" placeholder="Product Name">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="poduct_input_all_product form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="summernote" cols="30" rows="10" class="form-control" placeholder="Description"></textarea>
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
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
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
                                                <option value="{{ $brand->id }}">
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
                                    <input type="text" name="sku" id="sku" value="" class="form-control" placeholder="sku">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="stock_quantity">Stock Quantity</label>
                                    <input type="number" name="stock_quantity" id="stock_quantity" value="" class="form-control" placeholder="Stock Quantity">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="regular_price">Regular Price</label>
                                    <input type="number" name="regular_price" id="regular_price" value="" class="form-control" placeholder="Regular Price">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="sale_price">Sale Price</label>
                                    <input type="number" name="sale_price" id="sale_price" value="" class="form-control" placeholder="Sale Price">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="min_order">Min Order</label>
                                    <input type="number" name="min_order" id="min_order" value="" class="form-control" placeholder="Minimum Order">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="poduct_input_all_product">
                                    <label for="product_status">Product Status</label>
                                        <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="product_input_all_product">
                                    <input type="checkbox" name="flash_deal" id="flash_deal" value="1">
                                    <label for="flash_deal">Flash Deals</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                        <h2 class="h4 mb-3">Feature Image</h2>
                            <p><span class="feature_image_error" style="color: red;"></span></p>
                            <div class="gallery_img_block form-group">
                                <div id="feature_image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <i class="fa-regular fa-image"></i>
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="product-feature">
                            <input type="hidden" name="" class="feature_img" value="">
                        </div>

                        <div>
                        <h2 class="h4 mb-3">Gallery Image</h2>
                            <div class=" gallery_img_block">
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
                        <div class="row" id="product-gallery"></div>
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
                    console.log('Image upload function called');
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
                    number: true,
                    required: function () {
                        //return $('#sale_price').val() !== '';
                        return $('#flash_deal').is(':checked') && $('#sale_price').val() === '';
                    },
                    lessThanRegular: "#regular_price"
                },
                min_order: {
                    required: true,
                },
                feature_image: {
                    required: function () {
                        // Validate based on Dropzone images or hidden input (for previously uploaded images)
                        return Dropzone.forElement("#feature_image").getAcceptedFiles().length === 0 && $('.feature_img').val() === '';
                    }
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
                feature_image: "Please upload a feature image",
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.attr("id") === "feature_image") {
                    $("#feature_image").after(error);
                } else {
                    element.closest('.form-group').append(error);
                }
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form) {
                console.log('Form validation passed, preparing for AJAX submission.');
                
                let isValid = true;

                // Check Dropzone for feature image
                if (Dropzone.forElement("#feature_image").getAcceptedFiles().length === 0 && $('.feature_img').val() === '') {
                    $(".feature_image_error").text('**Select product feature image**').show();
                    setTimeout(function() {
                        $(".feature_image_error").hide();
                    }, 5000);
                    isValid = false;
                }

                if (!isValid) {
                    return false;
                }

                var formData = new FormData(form);

                // Append Dropzone files to FormData
                Dropzone.forElement("#feature_image").getAcceptedFiles().forEach(file => {
                    formData.append('feature_image', file);
                });

                Dropzone.forElement("#gallery_image").getAcceptedFiles().forEach(file => {
                    formData.append('gallery_images[]', file);
                });

                // AJAX form submission
                $.ajax({
                    url: "{{ route('products.store') }}",
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
                            $('#ProductForm')[0].reset();
                            Dropzone.forElement("#feature_image").removeAllFiles(true);
                            Dropzone.forElement("#gallery_image").removeAllFiles(true);
                            $('.summernote').summernote('reset');
                            $('.feature_img').val('');
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

        // Initialize Dropzone
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
                $("#image_id").val(response.image_id);
                console.log(response);

                var html = `
                    <div class="product_thumb_block" id="image-row-${response.image_id}">
                        <div class="product_thumb_icon" style="padding-right: 150px;">
                            <div class="thumb_icon_bg">
                                <input type="hidden" name="feature_image" class="feature_img" value="${response.image_id}">
                                <img src="${response.ImagePath}" class="card-img-top" alt="" width="100px" height="50px">
                            </div>
                        </div>
                        <div class="thumb_heading">
                            <span>${response.image}</span>
                            <div class="thumb_border"></div>
                        </div>
                        <div class="thumb_right_round">
                            <div class="thumb_round">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </div>
                    </div>
                `;

                // Remove any existing thumbnail and append the new one
                $('#product-feature').html(html);

                // Hide the error message once the image is successfully uploaded
                $(".feature_image_error").text('').hide();
            },
            complete: function(file) {
                this.removeFile(file);
            },
            maxfilesexceeded: function(file) {
                this.removeAllFiles();
                this.addFile(file);
            }
        });
    });

    $(document).ready(function(){
        $('body').on('change', '#parent_category', function() {
            var parent_id = $(this).val();
            getChildCategories(parent_id);
        });
    })

    function getChildCategories(parent_id) {
        var url = '{{ url('get-child-categories') }}';
        $.ajax({
            url: url,
            method: 'POST',
            data: {
                parent_id: parent_id,
                _token: '{{ csrf_token() }}'
            },
            dataType: "json",
            success: function(response) {
                if (response.status == 'success') {
                    var selectOption = '<option value="" hidden>Select Child Category</option>';
                    $.each(response.data, function(index, value) {
                        if (value.category_name != null) {
                            selectOption +=
                                `<option value="${value.id}" data-parent-id="${value.parent_id}">${value.category_name}</option>`;
                        }
                    });
                    $('#child_category').html(selectOption);
                } else {
                    console.log('No child categories found.');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while fetching child categories: ' + error);
            }
        });
    }
</script>
<script>
    Dropzone.autoDiscover = false;

    const dropzone = $("#gallery_image").dropzone({
        url: "{{ route('temp-images.create') }}",
        maxFiles: 10,
        paramName: 'image',
        addRemoveLinks: true,
        acceptedFiles: "image/jpeg,image/png,image/jpg,image/webp,image/gif",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(file, response) {
            $("#image_id").val(response.image_id);
            console.log(response);

            var imageId = response.image_id;
            var imagePath = response.ImagePath; // Ensure this is a unique URL for each image
            var imageName = response.image;

            var html = `<div class="product_thumb_block" id="image-row-${imageId}">
                            <div class="product_thumb_icon" style="padding-right: 150px;">
                                <div class="thumb_icon_bg">
                                    <input type="hidden" name="image_array[]" value="${imageId}">
                                    <img src="${imagePath}" class="card-img-top" alt="${imageName}" width="100px" height="50px">
                                </div>
                            </div>
                            <div class="thumb_heading">
                                <span>${imageName}</span>
                                <div class="thumb_border"></div>
                            </div>
                            <div class="thumb_right_round">
                                <div class="thumb_round">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            </div>
                            <div class="remove-image"><a href="javascript:void(0)" onclick="deleteImage(${imageId})"><i class="fa fa-close"></i></a></div>
                        </div>
            `;

            $('#product-gallery').append(html);
        },
        complete: function(file) {
            this.removeFile(file);
        }
    });

    function deleteImage(id) {
        $('#image-row-' + id).remove();
    }
</script>
@endsection
