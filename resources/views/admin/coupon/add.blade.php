@extends('admin.index')
@section('title', 'Add-Coupon')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
        <div class="row ">
                <div class="col-sm-6">
                <h1>Create Coupon</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('coupon.index') }}">All Coupons</a></li>
                        <li class="breadcrumb-item active">Add Coupons</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="add_new_form">
                <div class="form_staff_heading">
                    <h6>Add New Coupons</h6>
                </div>
                <form method="POST" id="coupon_add">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="code">Code:</label>
                                <input type="text" class="form-control" id="code" placeholder="Enter code" value="{{ old('code') }}" name="code">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="code">Coupon Title:</label>
                                <input type="text" class="form-control" id="coupon_title" placeholder="Enter coupon title" value="{{ old('coupon_title') }}" name="coupon_title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="poduct_input_all_product form-group">
                                <label for="sel1">Select type:</label>
                                <select class="form-control" name="type" id="type">
                                    <option value="" hidden>Please select coupon type</option>
                                    <option value="product_wise" id="pro_wise" {{ old('type') == 'product_wise' ? 'selected' : '' }}>product wise</option>
                                    <option value="category_wise" {{ old('type') == 'category_wise' ? 'selected' : '' }}>category wise</option>
                                    {{-- <option value="user_wise" {{ old('type') == 'user_wise' ? 'selected' : '' }}>user wise</option> --}}
                                </select>
                            </div>
                        </div>

                        <div class="row col-md-6" id="category_id" style="display:none;">
                            <div class="poduct_input_all_product form-group col-md-12">
                                <label for="sel1">Category:</label>
                                <select class="form-control" name="category_wise" id="category_wise">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6" id="multi_select_div" style="display:none;">
                            <div class="form-group">
                                <div class="poduct_input_all_product form-group">
                                    <label id="multi_select_lable" for="discount_select">Select </label>
                                    <select class="form-control discount_select chosen cpn_input select2-multi"
                                        name="discount_select[]" id="item_select" multiple>
                                        <option value="">Select </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" id="user_id" style="display: none;">
                            <div class="poduct_input_all_product form-group">
                                <label for="sel1">User:</label>
                                <select class="form-control select2-multi" name="user_id[]" id="user_select"
                                    multiple="multiple">
                                    @if ($users->isNotEmpty())
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->first_name . ' ' . $user->last_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="sel1">Select Discount type:</label>
                                <select class="form-control" name="amount_type" id="amount_type">
                                    <option value="flat">Flat</option>
                                    <option value="percentage">percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="amount" id="amount_label">Coupon Discount(Flat):</label>
                                <input type="number" class="form-control" id="amount" placeholder="Enter Here." value="" name="amount">
                            </div>
                        </div>


                        <div class="col-md-6" id="min_amount">
                            <div class="poduct_input_all_product form-group">
                                <label for="min_amount">Minimum Order Amount:</label>
                                <input type="number" class="form-control" id="min_amount" placeholder="Enter Minimum Amount" value="" name="min_amount">
                            </div>
                        </div>

                       <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="start_at">Start Date & Time:</label>
                                <input type="text" class="datetime form-control" id="start_at" name="start_at" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="expire_at">Expire Date & Time:</label>
                                <input type="text" class="datetime form-control" id="expire_at" name="expire_at" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="poduct_input_all_product form-group">
                                <label for="max_uses">User Limit:</label>
                                <input type="number" class="form-control" id="max_uses"
                                    placeholder="Enter user limit" name="max_uses" value="">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <!-- Load Full Moment Timezone Data -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.39/moment-timezone-with-data.min.js"></script>
    <script>
        $(document).ready(function() {
            let lagosTime = moment().tz("Africa/Lagos").format("YYYY-MM-DD HH:mm:ss");
            console.log("Lagos Current Time: ", lagosTime); // Debugging
            $('.datetime').datetimepicker({
                format: 'MM/DD/YYYY HH:mm:ss',
                locale: 'en',
                sideBySide: true,
                icons: {
                    up: 'fas fa-chevron-up',
                    down: 'fas fa-chevron-down',
                    previous: 'fas fa-chevron-left',
                    next: 'fas fa-chevron-right'
                },
                minDate: lagosTime, // Ensure minDate is set in Lagos timezone
                useCurrent: false // Prevent auto-setting current date and time
            });

            $('.select2-multi').select2({
                closeOnSelect: false // Keeps the dropdown open after selection
            });

            $('#type').change(function() {
                if ($(this).val() == "product_wise") {
                    $('#product_id').hide();
                    $('#category_id').hide();
                    $('#user_id').hide();
                    // $('#user_select').val(null).trigger('change');
                    // $('.select2-multi').select2({
                    //     closeOnSelect: false
                    // });
                }
                if ($(this).val() == "user_wise") {
                    $('#user_id').show();
                    $('#product_id').hide();
                    $('#pro_select').val(null).trigger('change');
                    $('#category_id').hide();
                    $('.select2-multi').select2({
                        closeOnSelect: false
                    });
                }
                if ($(this).val() == "category_wise") {
                    $('#category_id').show();
                    $('#product_id').hide();
                    $('#pro_select').val(null).trigger('change');
                    $('#user_id').hide();
                    $('#user_select').val(null).trigger('change');
                    $('.select2-multi').select2({
                        closeOnSelect: false
                    })
                }
            });

            $('#amount_type').change(function() {
                if ($(this).val() == "flat") {
                    $('#min_amount').show();
                    $('#amount_label').html('Coupon Discount(Flat)')
                } else {
                    $('#min_amount').show();
                    $('#amount_label').html('Coupon Discount(Persent)')
                }
            });

            // Function to dynamically update validation rules based on coupon type
            function updateValidationRules() {
                let type = $('#type').val();
                let categorySelected = $('#category_wise').val(); // Check if category is selected

                if (type === 'category_wise') {
                    // Make category selection required
                    $("#category_wise").rules("add", {
                        required: true,
                        messages: {
                            required: "Please select a category"
                        }
                    });

                    // Make subcategory selection required only if a category is selected
                    if (categorySelected) {
                        $("#item_select").rules("add", {
                            required: true,
                            messages: {
                                required: "Please select at least one subcategory"
                            }
                        });
                    } else {
                        $("#item_select").rules("remove");
                    }
                } else {
                    $("#category_wise").rules("remove");
                    $("#item_select").rules("remove");
                }
            }

            // Listen for changes on the type dropdown
            $('#type').change(function () {
                updateValidationRules();
            });

            // Listen for changes on category selection
            $('#category_wise').change(function () {
                updateValidationRules(); // Re-apply validation when category changes
            });

            $.validator.addMethod("noSpecialChars", function(value, element) {
                return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
            }, "Code must not contain special characters.");

            $("#coupon_add").validate({
                rules: {
                    code: {
                        required: true,
                        noSpecialChars: true,
                    },
                    coupon_title: {
                        required: true,
                    },
                    type: {
                        required: true,
                    },
                    amount: {
                        required: true,
                        number: true,
                    },
                    min_amount: {
                        required: true,
                        number: true,
                        greaterThanAmount: true,
                    },
                    start_at: {
                        required: true,
                        date: true
                    },
                    expire_at: {
                        required: true,
                        date: true,
                        greaterThan: "#start_at"
                    },
                    max_uses: {
                        required: true,
                        number: true,
                        min: 1, // Ensure minimum value is 1
                    }
                },
                messages: {
                    code: {
                        required: "Coupon Code is required",
                        noSpecialChars: "Code must not contain special characters"
                    },
                    coupon_title: "Please enter a coupon title",
                    type: "Please select a coupon type",
                    amount: {
                        required: "Please enter a discount amount",
                        number: "Please enter a valid number",
                    },
                    min_amount: {
                        required: "Please enter a minimum amount",
                        number: "Please enter a valid number",
                        greaterThanAmount: "Min Amount must be greater than the amount for 'flat' type",
                    },
                    start_at: {
                        required: "Please select a start date",
                        date: "Please enter a valid date"
                    },
                    expire_at: {
                        required: "Please select an end date",
                        date: "Please enter a valid date",
                        greaterThan: "End date must be greater than start date"
                    },
                    max_uses: {
                        required: "Please enter the user limit",
                        number: "Please enter a valid number",
                        min: "At least 1 user must be selected",
                    }
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
                    for (var pair of formData.entries()) {
                        console.log(pair[0] + ', ' + pair[1]); 
                    }
                    // AJAX form submission
                    $.ajax({
                        url: "{{ route('coupon.store') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            console.log('Sending AJAX request...');
                        },
                        success: function (response) {
                            console.log('AJAX request succeeded', response);
                            if (response.success) {
                                toastr.success(response.message);
                                window.location.reload();
                                $('#coupon_add')[0].reset();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log('AJAX request failed', textStatus, errorThrown);
                        }
                    });
                    return false; 
                }
            });

            // Ensure validation rules are updated when the form is loaded
            updateValidationRules();
        });
        // Custom validator for date comparison
        $.validator.addMethod('greaterThan', function (value, element, params) {
            var startDate = $(params).val();
            return this.optional(element) || new Date(value) > new Date(startDate);
        }, 'End date must be after start date');

        $.validator.addMethod("greaterThanAmount", function (value, element) {
            var type = $("#amount_type").val();
            var Amount = parseFloat($("#amount").val());
            var minAmount = parseFloat(value);

            if (type === "flat" && !isNaN(Amount) && !isNaN(minAmount)) {
                return minAmount > Amount;
            }
            return true; // Skip validation if type is not "flat" or if inputs are invalid
        },"Min Amount must be greater than the discount amount for 'flat' type");

    </script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var startDateInput = document.getElementById("start_at");
            var endDateInput = document.getElementById("expire_at");

            // Disable previous dates on the end date input
            startDateInput.addEventListener("change", function() {
                endDateInput.min = startDateInput.value;
                // Ensure end date is greater than start date
                if (endDateInput.value < startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
            });

            // Ensure end date is greater than start date initially
            endDateInput.addEventListener("change", function() {
                if (endDateInput.value < startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
            });
        });

        $("#subcategory_container").remove();
        $('#item_select option').remove();
        $('#type').change(function () {
            var discount_wise = $(this).val();
            
            if (discount_wise != 'product_wise') {
                $('#item_select option').remove();
                $('#subcategory_container').remove();
                discounts_wise_data(discount_wise, 0);
            }else{
                $('#multi_select_div').hide();
            }
        });

        function discounts_wise_data(discount_wise, main_cat) {
            if(discount_wise != "product_wise"){
                var urlss = '{{ url('get-discount-wise') }}';
                $.ajax({
                    url: urlss,
                    method: 'POST',
                    async: false,
                    data: {
                        discount_wise: discount_wise,
                        _token: csrf_token
                    },
                    dataType: "json",
                    success: function (response) {
                        console.log(response);
                        $('#category_wise').empty();
                        $('#category_wise').append('<option value="" selected disabled>Select</option>');
                        $.each(response.data, function (index, value) {
                            // if(discount_wise == "product_wise"){
                            // $('#multi_select_lable').text('Select Products');
                            // }
                            if(discount_wise == "category_wise"){
                                $('#multi_select_div').show();
                                $('#multi_select_lable').text('Select Sub Categories');
                            }
                            $('#category_wise').append(
                                `<option value="${value.id}" ${value.id == main_cat ? "selected" : ""}>${value.category_name}</option>`
                            );
                        });
                    },
                    error: function (response) {
                        console.error("Error fetching discount wise data:", response);
                    }
                });
            }
        }

         $('#category_wise').change(function () {
            var category_wise = $(this).val();
            var discount_wise = $('#type').val();
            category_wise_data(category_wise, discount_wise, null);
        });

        function category_wise_data(category_wise, discount_wise, coupon) {
            var urlss = '{{ url('get-category-wise') }}';
            $.ajax({
                url: urlss,
                method: 'POST',
                async: false,
                data: {
                    category_wise: category_wise,
                    discount_wise: discount_wise,
                    _token: csrf_token
                },
                dataType: "json",
                success: function (response) {
                    console.log(response.data);
                    var discount_wise = response.discount_wise;
                    if (discount_wise == 'category_wise') {
                        // alert('mjmj');
                        $('#item_select').empty();
                        $('#subcategory_container').remove();
                        $.each(response.categories, function (index, value) {
                            let selected = '';

                            if (coupon != null) {
                                let cat_ids = JSON.parse(coupon
                                    .category_id);

                                if ($.inArray(value.id.toString(), cat_ids) !== -1) {
                                    selected = 'selected';
                                }
                            }
                            $('#item_select').append(
                                `<option value="${value.id}" ${selected}>${value.category_name}</option>`
                            );
                        });

                        $('#item_select').trigger('change');
                    } 
                    // else if (discount_wise == 'product_wise') {
                    //     // alert('ok');
                    //     $('#subcategory_select').empty();
                    //     $('#item_select').empty();
                    //     $('#subcategory_container').remove();

                    //     var subcategorySelect = $(
                    //         '<select class="form-control" name="subcategory_select" id="subcategory_select"></select>'
                    //     );
                    //     subcategorySelect.append('<option value="" hidden>Select Subcategory</option>');

                    //     $('#category_wise').closest('.form-group').after(
                    //         '<div class="col-md-6 poduct_input_all_product form-group" id="subcategory_container"><label for="subcategory_select">Select Subcategory</label></div>'
                    //     );

                    //     $.each(response.categories, function (index, value) {
                    //         if (coupon != null) {
                    //             subcategorySelect.append(
                    //                 `<option value="${value.id}" ${value.id == coupon.sub_cat ? "selected" : ""}>${value.category_name}</option>`
                    //             );
                    //         } else {
                    //             subcategorySelect.append('<option value="' + value.id + '">' +
                    //                 value.category_name + '</option>');
                    //         }
                    //     });

                    //     $('#subcategory_container').append(subcategorySelect);
                    // }
                },
                error: function (response) {
                    console.error("Error fetching category wise data:", response);
                }
            });
        }

        $(document).on('change', '#subcategory_select', function () {
            var subcategory_wise = $(this).val();
            var discount_wise = $('#type').val();
            subcategoryWiseData(discount_wise, subcategory_wise, null);
        });

        function subcategoryWiseData(discount_wise, subcategory_wise, coupon) {
            var url = '{{ url('get-subcategory-wise') }}';
            $.ajax({
                url: url,
                method: 'POST',
                async: false,
                data: {
                    discount_wise: discount_wise,
                    subcategory_wise: subcategory_wise,
                    _token: csrf_token
                },
                dataType: "json",
                success: function (response) {
                    var product_data = response.product_data;

                    $('#item_select').empty();

                    for (const [id, name] of Object.entries(response.product_data)) {
                        let selected = '';

                        if (coupon != null) {
                            let cat_ids = JSON.parse(coupon
                                .product_id);

                            if ($.inArray(id.toString(), cat_ids) !== -1) {
                                selected = 'selected';
                            }
                        }
                        $('#item_select').append(`<option value="${id}" ${selected}>${name}</option>`);
                    }
                    $('#item_select').trigger('change');

                },
                error: function (xhr, status, error) {
                    console.error("Error fetching product data:", error);
                }
            });
        }
    </script>
@endsection
