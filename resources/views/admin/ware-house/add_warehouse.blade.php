@extends('admin.index')
@section('title', 'Add-WareHouse')
@section('css')
    <!-- intel input -->
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/intlTelInput.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/demo.css') }}" />
@endsection
@section('content')
    <section class="content-header">
    <div class="container-fluid ">
            {{-- <div class="back_btn_add">
                <button><a>Back</a></button>
            </div> --}}
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create WareHouse</h1>
                    <ol class="breadcrumb ">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('warehouse.index') }}">WareHouse</a></li>
                        <li class="breadcrumb-item active"> Add new </li>
                    </ol>
                </div>            
            </div>    
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="add_new_form">
                <div class="form_staff_heading">
                    <h6>Add New WareHouse</h6>
                </div> 
            <div>
            <form method="POST" id="warehouse">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="warehouse_name">WareHouse Name</label>
                            <div class="add_staff_input form-group">
                                <input type="text" name="warehouse_name" id="warehouse_name" value="{{ old('warehouse_name') }}"
                                    class="form-control" placeholder="Enter WareHouse Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="contact_name">Contact Name</label>
                            <div class="add_staff_input form-group">
                                <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                                    class="form-control" placeholder="Enter Contact Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="contact_email">Contact Email</label>
                            <div class="add_staff_input form-group">
                                <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}"
                                    class="form-control" placeholder="Enter Contact Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="contact_number">Contact Number</label>
                            <div class="add_staff_input form-group">
                                <input type="tel" name="contact_number" class="form-control contact_number phoneInput" placeholder="Enter Contact Number" id="phone" value="{{ old('contact_number') }}">
                                <input type="hidden" name="country_code" id="country_code">
                                <span id="error-msg" class="hide"></span>
                                <span id="valid-msg" class="hide"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="add_staff_label_input">
                            <label for="street_address">Street Address</label>
                            <div class="add_staff_input form-group">
                               <textarea class="form-control street_address" name="street_address" style="background-color: #494A54; border-radius: 10px; height: 156px; color: #ffffff;"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="country">Country</label>
                            <div class="add_staff_input form-group">
                                <select name="country" id="country" class="form-control">
                                    <option value="" hidden>Select country</option>
                                    @if($countries->isNotEmpty())
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="state">State</label>
                            <div class="add_staff_input form-group">
                                <select name="state" id="state" class="form-control">
                                    <option value="" hidden>Select state</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="city">City</label>
                            <div class="add_staff_input form-group">
                                <select name="city" id="city" class="form-control">
                                    <option value="" hidden>Select city</option>
                                </select>
                            </div>
                        </div>
                    </div>
                   
                   <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="postal_code">Postal Code</label>
                            <div class="add_staff_input form-group">
                                <input type="number" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                    class="form-control" placeholder="Enter Postal Code">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="add_staff_input">
                            <label for="status">Status</label>
                            <div class="add_staff_label_input">
                                <select name="status" id="status"
                                    class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="add_new_save">
                    <div class="save_btn_add">
                        <button >Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@section('customJs')
    <script src="{{ asset('admin-assets/assets/intel-tel/js/intlTelInputWithUtils.js') }}"></script>
    <script src="{{ asset('admin-assets/assets/intel-tel/js/utils.js?1715508103106') }}"></script>
    <script>
        $(document).ready(function () {
            // Initialize jQuery validation
            $("#warehouse").validate({
                 rules: {
                    warehouse_name: {
                        required: true,
                    },
                    contact_name: {
                        required: true,
                    },
                    contact_email: {
                        required: true,
                    },
                    contact_number: {
                        required: true,
                    },
                    street_address: {
                        required: true,
                    },
                    country: {
                        required: true,
                    },
                    state: {
                        required: true,
                    },
                    city: {
                        required: true,
                    },
                    postal_code: {
                        required: true,
                    },
                },
                messages: {
                    warehouse_name: {
                        required: "Please enter Warehouse name."
                    },
                    contact_name: {
                        required: "Please enter contact name."
                    },
                    contact_email: {
                        required: "Please enter contact email."
                    },
                    contact_number: {
                        required: "Please enter contact number."
                    },
                    street_address: {
                        required: "Please enter street address."
                    },
                    country: {
                        required: "Please select country."
                    },
                    state: {
                        required: "Please select state."
                    },
                    city: {
                        required: "Please select city."
                    },
                    postal_code: {
                        required: "Please enter postal code."
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
                // Form submission handler
                submitHandler: function (form) {
                    console.log('Form validation passed, preparing for AJAX submission.');
                    var formData = new FormData(form);
                    for (var pair of formData.entries()) {
                        console.log(pair[0]+ ', ' + pair[1]); 
                    }
                    // AJAX form submission
                    $.ajax({
                        url: "{{ route('warehouse.store') }}", // Your server-side form handling route
                        method: 'POST',
                        data: formData, // Serialize form data
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
                                location.reload();
                                $('#warehouse')[0].reset();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX request failed:', status, error);
                            toastr.error('An error occurred: ' + error);
                        }
                    });
                    return false; // Prevent default form submission
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#country').change(function(){
                var country_id = $(this).val();
                if (country_id) {
                    $.ajax({
                        url: "{{ route('getStates') }}",
                        type: "GET",
                        data: { country_id: country_id },
                        success: function(response) {
                            var stateOptions = '<option value="">Select State</option>';
                            $.each(response, function(index, state) {
                                stateOptions += '<option value="' + state.id + '">' + state.name + '</option>';
                            });
                            $('#state').html(stateOptions);
                        },
                        error: function() {
                            alert('Error retrieving states');
                        }
                    });
                } else {
                    $('#state').html('<option value="">Select State</option>');
                }
            });

            $('#state').change(function(){
                var state_id = $(this).val();
                if (state_id) {
                    $.ajax({
                        url: "{{ route('getCities') }}", 
                        type: "GET",
                        data: { state_id: state_id },
                        success: function(response) {
                            var cityOptions = '<option value="">Select City</option>';
                            $.each(response, function(index, city) {
                                cityOptions += '<option value="' + city.name + '">' + city.name + '</option>';
                            });
                            $('#city').html(cityOptions);
                        },
                        error: function() {
                            alert('Error retrieving cities');
                        }
                    });
                } else {
                    $('#city').html('<option value="">Select City</option>');
                }
            });
        });
    </script>
    <script>
        const input = document.querySelector("#phone");
        const errorMsg = document.querySelector("#error-msg");
        const validMsg = document.querySelector("#valid-msg");
        const countryCodeInput = document.querySelector("#country_code");

        const iti = window.intlTelInput(input, {
            initialCountry: "us",
            separateDialCode: "+1",
            strictMode: true,
            formatOnDisplay: false,
        });

        const errorMap = ["Invalid number", "Invalid country code", "Too short", "Too long", "Invalid number"];
        const reset = () => {
            input.classList.remove("error");
            errorMsg.innerHTML = "";
            errorMsg.classList.add("hide");
            validMsg.classList.add("hide");
        };

        const showError = (msg) => {
            input.classList.add("error");
            errorMsg.innerHTML = msg;
            errorMsg.classList.remove("hide");
        };

        const validateNumberLength = () => {
            const countryData = iti.getSelectedCountryData();
            const dialCode = countryData.dialCode;
            const phoneLength = input.value.replace(`+${dialCode}`, '').replace(/\D/g, '').length;

            let maxLength = 15; // Default max length
            if (countryData.iso2 === 'in') {
                maxLength = 10;
            } else if (countryData.iso2 === 'kw') {
                maxLength = 8;
            }

            input.setAttribute('maxlength', maxLength);

            if (phoneLength > maxLength) {
                showError(`Phone number cannot be longer than ${maxLength} digits for ${countryData.name}`);
                return false;
            }
            return true;
        };

        // ------- Hyphens aur spaces ko remove karne ka function new code ------------------------------------
        const formatPhoneNumber = () => {
            input.value = input.value.replace(/[-\s]/g, '');
        };

        input.addEventListener("keyup", () => {
            reset();
            formatPhoneNumber();
        });

        input.addEventListener("change", () => {
            reset();
            formatPhoneNumber();
        });

        input.addEventListener("blur", function() {
            reset();
            formatPhoneNumber(); // Format phone number on blur event
            if (input.value.trim()) {
                if (iti.isValidNumber()) {
                    validMsg.classList.remove("hide");
                    countryCodeInput.value = iti.getSelectedCountryData().dialCode;
                } else {
                    const errorCode = iti.getValidationError();
                    showError(errorMap[errorCode] || "Invalid number");
                }
            }
        });

        input.addEventListener('countrychange', function() {
            $('#phone').val('');
        });

        $('.iti__country-container').click(function() {
            vals = $('.iti__selected-dial-code').text();
            var code = vals.split("+");
            $('#country_code').val('+' + code[1])
            console.log(code[1]);
        }).trigger('click');
    </script>
@endsection
