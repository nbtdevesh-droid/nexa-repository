@extends('admin.index')
@section('title', 'Update-Coupon')
@section('content')
<section class="content-header">

    <div class="container-fluid">
        <div class="row ">
            <div class="">
                <h1>Update Coupon</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('coupon.index') }}">All Coupons</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('coupon.index') }}"> Update Coupons</a></li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="add_new_form">
            <div class="form_staff_heading">
                <h6>Update Coupons</h6>
            </div>
            <form id="coupon_edit" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="update" value="update">
                <div class="row">
                    <div class="col-md-6">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="code">Code:</label>
                            <input type="text" class="form-control" id="code" placeholder="Enter code"
                                value="{{ $coupon->code }}" name="code">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="code">Coupon Title:</label>
                            <input type="text" class="form-control" id="coupon_title" placeholder="Enter coupon title" value="{{ $coupon->title }}" name="coupon_title">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="poduct_input_all_product  poduct_input_all_product-one  form-group">
                            <label for="sel1">Select type:</label>
                            <select class="form-control" name="type" id="type">
                                <option value="product_wise" id="pro_wise"
                                    {{ $coupon->coupon_type == 'product_wise' ? 'selected' : '' }}>product wise
                                </option>
                                <option value="category_wise"
                                    {{ $coupon->coupon_type == 'category_wise' ? 'selected' : '' }}>category wise
                                </option>
                                {{-- <option value="user_wise"
                                    {{ $coupon->coupon_type == 'user_wise' ? 'selected' : '' }}>user wise</option> --}}
                            </select>
                        </div>
                    </div>

                    <div class="row col-md-12" id="category_id" style="display: none;">
                        <div class="col-md-6">
                            <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                                <label for="parent_category">Category:</label>
                                <select class="form-control" name="parent_category" id="parent_category">
                                    <option value="" hidden>Select Category</option>
                                    @if ($categories->isNotEmpty())
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ $coupon->main_category == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        @if($coupon->coupon_type == 'product_wise')
                            <div class="col-md-6">
                                <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                                    <label for="sel1">Child Category:</label>
                                    <select class="form-control" name="child_Category[]" id="child_Category" multiple="multiple">
                                    @if($coupon->main_category != null)
                                        @php
                                            $childCategories = App\Models\Category::where(
                                                'parent_id',
                                                $coupon->main_category,
                                            )->get();
                                        @endphp
                                        @if ($childCategories->isNotEmpty())
                                            @foreach ($childCategories as $childCategory)
                                                <option value="{{ $childCategory->id }}" {{ in_array($childCategory->id, json_decode($coupon->category_id, true) ?? []) ? 'selected' : '' }}>{{ $childCategory->category_name }}</option>
                                            @endforeach
                                        @endif
                                    @endif
                                    </select>
                                </div>
                            </div>
                        @elseif ($coupon->coupon_type == 'category_wise')
                            <div class="col-md-6">
                                <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                                    <label for="sel1">Child Category:</label>
                                    <select class="form-control select2-multi" name="child_Category[]" id="child_Category" multiple="multiple">
                                    @php
                                        $childCategories = App\Models\Category::where(
                                            'parent_id',
                                            $coupon->main_category,
                                        )->get();
                                    @endphp
                                    @if ($childCategories->isNotEmpty())
                                        @foreach ($childCategories as $childCategory)
                                            <option value="{{ $childCategory->id }}" {{ in_array($childCategory->id, json_decode($coupon->category_id, true) ?? []) ? 'selected' : '' }}>{{ $childCategory->category_name }}</option>
                                        @endforeach
                                    @endif
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-12" id="product_id" style="display: none;">
                        <div class="poduct_input_all_product  form-group">
                            <label for="product_id">Product:</label>
                            <select class="form-control select2-multi select2-multi-one product-one " name="product_id[]" id="pro_select" multiple="multiple">
                                @if ($products->isNotEmpty())
                                    @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ in_array($product->id, json_decode($coupon->product_id, true) ?? []) ? 'selected' : '' }}>
                                        {{ $product->product_name }}
                                    </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" id="user_id" style="display: none;">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="user_id">User:</label>
                            <select class="form-control select2-multi" name="user_id[]" id="user_select"
                                multiple="multiple">
                                <option value="" hidden>Select User</option>
                                @if ($users->isNotEmpty())
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ in_array($user->id, json_decode($coupon->user_id, true) ?? []) ? 'selected' : '' }}>
                                    {{ $user->first_name. ' ' . $user->last_name }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="sel1">Select Amount type:</label>
                            <select class="form-control" name="amount_type" id="amount_type">
                                <option value="flat" {{ $coupon->amount_type == 'flat' ? 'selected' : '' }}>
                                    Flat</option>
                                <option value="percentage"
                                    {{ $coupon->amount_type == 'percentage' ? 'selected' : '' }}>percentage
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="amount" id="amount_label">Coupon Discount(Flat):</label>
                            <input type="number" class="form-control" id="amount" placeholder="Enter Here."
                                value="{{ $coupon->amount }}" name="amount">
                        </div>
                    </div>

                    <div class="col-md-12" id="min_amount">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="pro_min_amount">Minimum Order Amount:</label>
                            <input type="number" class="form-control" id="min_amount"
                                placeholder="Enter min_amount" value="{{ $coupon->product_min_amount }}"
                                name="min_amount">
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="start_at">Start_at:</label>
                            <input type="date" class="form-control" id="start_at" placeholder="Enter start date" value="{{ $coupon->coupon_start_date }}"
                                name="start_at">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="expire_at">Expire At:</label>
                            <input type="date" class="form-control" id="expire_at"
                                value="{{ $coupon->coupon_end_date }}" placeholder="Enter amount"
                                name="expire_at">
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <div class="poduct_input_all_product form-group">
                            <label for="start_at">Start Date & Time:</label>
                            <input type="text" class="datetime form-control" id="start_at" name="start_at" value="{{ $coupon->coupon_start_date }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="poduct_input_all_product form-group">
                            <label for="expire_at">Expire Date & Time:</label>
                            <input type="text" class="datetime form-control" id="expire_at" name="expire_at" value="{{ $coupon->coupon_end_date }}">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="poduct_input_all_product poduct_input_all_product-one form-group">
                            <label for="max_uses">User Limit:</label>
                            <input type="number" class="form-control" id="max_uses" placeholder="Enter user limit" name="max_uses" value="{{ $coupon->remain_uses }}">
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3  product_save_cancal_btn justify-content-center ">
                    <button class="btn">Update</button>
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

<script>
    $(document).ready(function() {
        $('.datetime').datetimepicker({
            format: 'YYYY-MM-DD  HH:mm:ss',
            locale: 'en',
            sideBySide: true,
            icons: {
                up: 'fas fa-chevron-up',
                down: 'fas fa-chevron-down',
                previous: 'fas fa-chevron-left',
                next: 'fas fa-chevron-right'
            },
            useCurrent: false
        });
        
        $('.select2-multi').select2({
            closeOnSelect: false // Keeps the dropdown open after selection
        });

        $('#type').change(function() {
            if ($(this).val() == "product_wise") {
                $('#product_id').hide();
                $('#category_id').hide();
                $('#child_Category').hide();
                $('#cat_select').val().trigger('change');
                $('#user_id').hide();
                $('#user_select').val(null).trigger('change');
                $('.select2-multi').select2({
                    closeOnSelect: false
                });
            }
            if ($(this).val() == "user_wise") {
                $('#user_id').show();
                $('#product_id').hide();
                $('#pro_select').val(null).trigger('change');
                $('#category_id').hide();
                $('#cat_select').val(null).trigger('change');
                $('#child_Category').hide();
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
                $('#child_Category').hide();
                $('.select2-multi').select2({
                    closeOnSelect: false
                });
            }
        });

        // Page load par initial state set karte hain
        if ($('#type').val() == "product_wise") {
            $('#product_id').show();
            $('#category_id').show(); // Category ko bhi show karte hain
            $('#child_Category').show(); // Subcategory ko bhi show karte hain
        }
        if ($('#type').val() == "user_wise") {
            $('#user_id').show();
            $('#product_id').hide();
            $('#pro_select').val(null).trigger('change');
            $('#category_id').hide();
            $('#cat_select').val(null).trigger('change');
            $('#child_Category').hide(); // Subcategory ko bhi hide karte hain
        }
        if ($('#type').val() == "category_wise") {
            $('#category_id').show();
            $('#product_id').hide();
            $('#pro_select').val(null).trigger('change');
            $('#user_id').hide();
            $('#user_select').val(null).trigger('change');
            $('#child_Category').hide(); // Subcategory ko bhi hide karte hain
        }
        $('#amount_type').change(function() {
            if ($(this).val() == "flat") {
                $('#min_amount').show();
                $('#amount_label').html('Coupon Discount(Flat)');
            } else {
                $('#min_amount').show();
                $('#amount_label').html('Coupon Discount(Persent)');
            }
        }).trigger('change'); // Initial value ke saath initialize karte hain

        // Function to dynamically update validation rules based on coupon type
        function updateValidationRules() {
            let type = $('#type').val();
            let categorySelected = $('#parent_category').val(); // Parent category selected or not
            if (type === 'category_wise') {
                // Make category selection required
                $("#parent_category").rules("add", {
                    required: true,
                    messages: {
                        required: "Please select a category"
                    }
                });

                // If parent category is selected, then child category should be required
                if (type === 'category_wise' && categorySelected != '') {
                    $("#child_Category").rules("add", {
                        required: true,
                        messages: {
                            required: "Please select at least one subcategory"
                        }
                    });
                } else {
                    $("#child_Category").rules("remove");
                }
            } else {
                // Remove validation rules if not category-wise
                $("#parent_category").rules("remove");
                $("#child_Category").rules("remove");
            }
        }

        // Listen for changes on the type dropdown
        $('#type').change(function () {
            updateValidationRules();
        });

        $('#type, #parent_category').change(function () {
            updateValidationRules();
        });

        $.validator.addMethod("noSpecialChars", function(value, element) {
            return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
        }, "Code must not contain special characters.");

         $("#coupon_edit").validate({
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
                    number: true
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
                    number: "Please enter a valid number"
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
                    url: "{{ route('coupon.update', $coupon->id) }}",
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
                        } else {
                            toastr.error(response.message);
                        }
                    },
                });
                return false; 
            }
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
        },"Min Amount must be greater than the discount amount for 'flat' type"
        );
    });
</script>
<script>
    $(document).ready(function() {
        $('#type').change(function() {
            var selectedType = $(this).val();
            var $childCategory = $('#child_Category');

            if (selectedType === 'product_wise') {
                $childCategory.attr('multiple', false);
            } else if(selectedType === 'category_wise') {
                $childCategory.attr('multiple', true).select2({
                    minimumResultsForSearch: true
                });
            }
        });
        $('#type').change();
    });
</script>
<script>
    $(document).ready(function() {
        //$('#child_Category').select2({
            //placeholder: "Select a category"
        //});
        //$("#child_Category").attr("placeholder", "Select a category")
        var selectedProducts = JSON.parse('{!! json_encode(old("product_id", json_decode($coupon->product_id, true) ?? [])) !!}');

        // Store old data
        var oldData = {
            category_wise: {
                parent_category: $('#parent_category').val(),
                child_Category: $('#child_Category').val(),
                products: $('#pro_select').val()
            },
            product_wise: {
                parent_category: null,
                child_Category: null,
                products: null
            },
            user_wise: {
                users: null
            }
        };

        $('#type').change(function() {
            var type = $(this).val();
            saveCurrentData();

            if (type !== 'category_wise') {
                $('#parent_category').val('').trigger('change');
                $('#child_Category').val(null).trigger('change');
                $('#pro_select').val(null).trigger('change');
            }

            restoreData(type);
        });

        function saveCurrentData() {
            var currentType = $('#type').val();

            if (currentType === 'category_wise') {
                oldData.category_wise.parent_category = $('#parent_category').val();
                oldData.category_wise.child_Category = $('#child_Category').val();
                oldData.category_wise.products = $('#pro_select').val();
            } else if (currentType === 'product_wise') {
                oldData.product_wise.parent_category = $('#parent_category').val();
                oldData.product_wise.child_Category = $('#child_Category').val();
                oldData.product_wise.products = $('#pro_select').val();
            } else if (currentType === 'user_wise') {
                oldData.user_wise.users = $('#user_select').val();
            }
        }

        function restoreData(type) {
            if (type === 'category_wise') {
                $('#parent_category').val(oldData.category_wise.parent_category).trigger('change');
                $('#child_Category').val(oldData.category_wise.child_Category).trigger('change');
                $('#pro_select').val(oldData.category_wise.products).trigger('change');
            } else if (type === 'product_wise') {
                $('#parent_category').val(oldData.product_wise.parent_category).trigger('change');
                $('#child_Category').val(oldData.product_wise.child_Category).trigger('change');
                $('#pro_select').val(oldData.product_wise.products).trigger('change');
            } else if (type === 'user_wise') {
                $('#user_select').val(oldData.user_wise.users).trigger('change');
            }
        }
        // Load initial data for parent category, child category, and products based on type
        function loadInitialData() {
            var parentCategory = $('#parent_category').val();
            var childCategory = $('#child_Category').val();
            if (parentCategory) {
                loadChildCategories(parentCategory, childCategory);
            }
            if (childCategory) {
                loadProducts(childCategory);
            }

            // Ye line ensure karegi ki initial load ke time selected products show ho
            var selectedProducts = JSON.parse('{{ json_encode(old($coupon->product_id, [])) }}');
            $('#pro_select').val(selectedProducts).trigger('change');
        }

        function loadChildCategories(parentCategoryId, selectedChildCategoryId = null) {
            $.ajax({
                url: '{{ route("get.subcategories") }}',
                type: 'POST',
                data: {
                    category_id: parentCategoryId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#child_Category').empty().append('');
                    $('#pro_select').empty();

                    $.each(response, function(key, value) {
                        $('#child_Category').append('<option value="' + value.id + '">' + value.category_name + '</option>');
                    });
                    if (selectedChildCategoryId) {
                        $('#child_Category').val(selectedChildCategoryId).trigger('change');
                    }
                }
            });
        }
        $('#pro_select').select2();

        function loadProducts(childCategoryId) {
            $.ajax({
                url: '{{ route("get.products") }}',
                type: 'POST',
                data: {
                    subcategory_id: childCategoryId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#pro_select').empty().append('');
                    $.each(response, function(key, value) {
                        $('#pro_select').append('<option value="' + value.id + '">' + value.product_name + '</option>');
                    });
                    var selectedProducts = JSON.parse('{!! json_encode(old("product_id", json_decode($coupon->product_id, true) ?? [])) !!}');
                    $.each(selectedProducts, function(index, productId) {
                        $('#pro_select').find('option[value="' + productId + '"]').prop('selected', true);
                    });

                    // Reinitialize Select2
                    $('#pro_select').select2();
                }
            });
        }
        loadInitialData();

        $('#parent_category').change(function() {
            var parentCategoryId = $(this).val();
            if (parentCategoryId) {
                loadChildCategories(parentCategoryId);
            }
        });

        $('#child_Category').change(function() {
            var childCategoryId = $(this).val();
            if (childCategoryId) {
                loadProducts(childCategoryId);
            }
        });
    });
</script>

@endsection