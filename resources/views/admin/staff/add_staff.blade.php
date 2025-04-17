@extends('admin.index')
@section('title', 'Add-Staff')
@section('css')
    <!-- intel input -->
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/intlTelInput.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/demo.css') }}" />
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid ">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User</h1>
                    <ol class="breadcrumb ">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('staff.index') }}">User</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('staff.index') }}"> Staff </a></li>
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
                    <h6>Add New Staff</h6>
                </div>
            <div>
            <form action="" method="POST" enctype="multipart/form-data" id="add_staff_member">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="name">First Name</label>
                            <div class="add_staff_input form-group">
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                                    class="form-control" placeholder="Enter Your first Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="name">Last Name</label>
                            <div class="add_staff_input form-group">
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="form-control" placeholder="Enter Your Last Name">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="email">Email</label>
                            <div class="add_staff_input form-group">
                                <input type="text" name="email" id="email" value="{{ old('email') }}" class="form-control" placeholder="Email">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="country">Country</label>
                            <div class="add_staff_input form-group">
                                <select name="country" id="country" class="form-control">
                                    <option value="" hidden>Select country</option>
                                    @if ($countries->isNotEmpty())
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->name }}" {{ old('country') == $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="phone">Phone</label>
                            <div class="add_staff_input form-group">
                                <input type="tel" name="phone" class="form-control phoneInput" placeholder="Enter Phone Number" id="phone" value="{{ old('phone') }}">
                                <input type="hidden" name="country_code" id="country_code">
                                <span id="error-msg" class="hide"></span>
                                <span id="valid-msg" class="hide"></span>
                                {{-- <span id="valid-msg" class="hide">✓ Valid</span> --}}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="password">Password</label>
                            <div class="add_staff_input form-group">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="password">Confirm password</label>
                            <div class="add_staff_input form-group">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder=" Password">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="image">Image</label>
                            <div class="add_staff_input">
                                <input type="file" name="image" id="image" class="form-control" accept=".png, .jpg, .jpeg, .svg">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="add_staff_label_input">
                            <label for="status">Status</label>
                            <div class="add_staff_input">
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
            $("#add_staff_member").validate({
                rules: {
                    first_name: {
                        required: true,
                    },
                    last_name: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true,
                        remote: {
                            url: base_url + 'unique-email',
                            type: 'POST',
                            data: {
                                email: function () {
                                    return $("#email").val(); // Get phone number value
                                },
                                _token: csrf_token // Include CSRF token
                            },
                            dataFilter: function (response) {
                                if (response === "true") {
                                    return true; // Phone number is unique
                                } else {
                                    return false; // Phone number is already taken
                                }
                            }
                        }
                    },
                    phone: {
                        required: true,
                        remote: {
                            url: base_url + 'unique-phone-number',
                            type: 'POST',
                            data: {
                                phone: function () {
                                    return $("#phone").val(); // Get phone number value
                                },
                                country_code: function () {
                                    return $("#country_code").val(); // Get country code value
                                },
                                _token: csrf_token // Include CSRF token
                            },
                            dataFilter: function (response) {
                                if (response === "true") {
                                    return true; // Phone number is unique
                                } else {
                                    return false; // Phone number is already taken
                                }
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 6
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#password"
                    },
                    country: {
                        required: true
                    }
                },
                messages: {
                    first_name: "Please enter a valid first name",
                    last_name: "Please enter a valid last name",
                    email: {
                        required: "Please enter a valid email",
                        remote: "This email is already taken"
                    },
                    phone: {
                        required: "Please enter a valid phone number",
                        remote: "This phone number is already taken"
                    },
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 6 characters long"
                    },
                    confirm_password: {
                        required: "Please confirm your password",
                        equalTo: "Passwords do not match"
                    },
                    country: "Please select your country"
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
                        url: "{{ route('staff.store') }}", // Your server-side form handling route
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
                                $('#add_staff_member')[0].reset();
                            } else {
                                toastr.error(response.message || 'An error occurred.');
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
