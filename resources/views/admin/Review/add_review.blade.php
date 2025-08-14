@extends('admin.index')
@section('title', 'Add-Review')
@section('css')
    <style>
        .stars {
            font-size: 35px;
            color: gray;
            cursor: pointer;
        }
        
        .stars.selected {
            color: gold;
        }
        /* Select2 container background color & height */
        .select2-container--default .select2-selection--single {
            background-color: #494A54 !important;
            color: #ffffff !important;
            border: 1px solid #6c757d;
            height: 50px !important; /* Height set to 50px */
            border-radius: 10px !important;
            display: flex;
            align-items: center; /* Text vertically center align */
           
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            color: #ced4da !important;
        }
        /* Select2 dropdown text size & spacing */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 50px !important;
            font-size: 16px;
            padding-left: 10px;
        }

        /* Dropdown options styling */
        .select2-container--default .select2-results__option {
            background-color: #494A54;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Hover and selected option style */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6c757d !important;
        }

        /* Ensure dropdown width is same as select field */
        .select2-dropdown {
            width: auto !important;
            min-width: 100% !important;
            max-width: 100% !important;
        }
        /* Adjust error message position */
        #customer_id-error {
            margin-top: 5px !important;
        }
        #product_id-error {
            margin-top: 5px !important;
        }
    </style>
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Add Review</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('all.reviews') }}">All Reviews</a></li>
                        <li class="breadcrumb-item active">Add Review</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="add_new_form">
                <div class="form_staff_heading">
                    <h6>Add New Review</h6>
                </div>
                <form method="POST" id="review_add">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="customer_id">Select Customer:</label>
                                <select class="form-control select2" name="customer_id" id="customer_id">
                                    <option value="">Select a customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->first_name . ' ' . $customer->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="product_id">Select Product:</label>
                                <select class="form-control select2" name="product_id" id="product_id">
                                    <option value="">Select a product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="poduct_input_all_product form-group">
                                <label for="review">Rating:</label>
                                <div id="rating">
                                    <span class="stars" data-value="1">&#9733;</span>
                                    <span class="stars" data-value="2">&#9733;</span>
                                    <span class="stars" data-value="3">&#9733;</span>
                                    <span class="stars" data-value="4">&#9733;</span>
                                    <span class="stars" data-value="5">&#9733;</span>
                                </div>
                                <input type="hidden" name="rating_value" id="rating_value">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="poduct_input_all_product form-group">
                                <label for="review">Review:</label>
                                <textarea class="form-control mb-2" style="background-color: #494A54 !important; color: #ced4da;" id="review" name="review" rows="10" placeholder="Enter your review"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pb-5 pt-3 product_save_cancal_btn justify-content-center ">
                        <button class="btn">Save</button>
                        <a href="{{ route('coupon.index') }}" class="btn">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('customJs')
    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script>
        $(document).ready(function () {
            $("#product_id").select2({
                width: 'resolve',
                dropdownAutoWidth: true,
                placeholder: "Select a product",
                allowClear: true
            });

            $("#customer_id").select2({
                width: 'resolve',
                dropdownAutoWidth: true,
                placeholder: "Select a customer",
                allowClear: true
            });
        });

        $(document).ready(function () {
            // Star Rating Functionality
            $(".stars").click(function () {
                let selectedValue = $(this).data("value");

                // Remove selected class from all stars and add only up to selected
                $(".stars").removeClass("selected");
                $(".stars").each(function () {
                    if ($(this).data("value") <= selectedValue) {
                        $(this).addClass("selected");
                    }
                });

                // Store selected rating in hidden input and trigger validation check
                $("#rating_value").val(selectedValue).trigger("change");

                // Remove validation error when a rating is selected
                $("#rating_error").remove();
            });

            // Initialize jQuery Validation
            $("#review_add").validate({
                rules: {
                    customer_id: { required: true },
                    product_id: { required: true },
                    rating_value: { 
                        required: function () {
                            return $("#rating_value").val() == ""; // Check if rating is empty
                        }
                    },
                    review: { required: true, minlength: 5 }
                },
                messages: {
                    customer_id: "Please select a customer",
                    product_id: "Please select a product",
                    rating_value: "Please select a rating",
                    review: { required: "Please enter a review", minlength: "Review must be at least 5 characters long" }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    if (element.attr("name") == "rating_value") {
                        if ($("#rating_error").length == 0) { 
                            $("<span id='rating_error' class='text-danger'></span>").insertAfter("#rating");
                        }
                        $("#rating_error").text(error.text()); // Show error message
                    } else {
                        error.addClass('invalid-feedback');
                        element.closest('.form-group').append(error);
                    }
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid mt-2');
                },
                submitHandler: function (form) {
                    if ($("#rating_value").val() == "") {
                        if ($("#rating_error").length == 0) { 
                            $("<span id='rating_error' class='text-danger'>Please select a rating.</span>").insertAfter("#rating");
                        } else {
                            $("#rating_error").text("Please select a rating.");
                        }
                        return false;
                    }

                    var formData = new FormData(form);
                    $(".btn[type=submit]").prop("disabled", true);
                    $.ajax({
                        url: "{{ route('review.store') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            if (response.status === true) {
                                toastr.success(response.message);
                                setTimeout(function() {
                                    window.location.href = "{{ route('add.reviews') }}";
                                }, 500);
                            } else {
                                toastr.error(response.message);
                                setTimeout(function() {
                                    window.location.href = "{{ route('add.reviews') }}";
                                }, 500);
                            }
                        },
                        error: function (xhr) {
                            toastr.error("An error occurred. Please try again.");
                            $(".btn[type=submit]").prop("disabled", false);
                        }
                    });
                    return false;
                }
            });

            // Manually validate rating when form is submitted
            $("#review_add").submit(function () {
                if ($("#rating_value").val() == "") {
                    if ($("#rating_error").length == 0) { 
                        $("<span id='rating_error' style='font-size: 15px;'' class='text-danger'>Please select a rating.</span>").insertAfter("#rating");
                    } else {
                        $("#rating_error").text("Please select a rating.");
                    }
                    return false;
                }
            });
        });
    </script>
@endsection

