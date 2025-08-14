@extends ('admin/index')
@section('title', 'profile')
@section('css')
    <!-- intel input -->
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/intlTelInput.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/assets/intel-tel/css/demo.css') }}" />
    <!-- -->
@endsection
@section('content')
    <section class="content-header">
    <div class="container-fluid">
        <div class="row ">
                <div class="col-sm-6">
                <h1>Profile</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
     <!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-md-5 col-12">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline admin_edit_profile_image">
                        <div class="card-body box-profile">

                            <form method="post" action="{{ route('admin.uploadimage') }}" id="upload-image-form"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="image" id="adminimageUpload"
                                            accept=".png, .jpg, .jpeg" onchange="readURL(this);" />
                                        <label for="adminimageUpload"></label>
                                    </div>
                                    <div class="avatar-preview">
                                        @if ($staff->image != "")
                                            <div id="imagePreview"
                                                style="background-image: url({{ asset('/admin-assets/assets/img/profile_img/staff/') }}/{{ $staff->image }});">
                                            </div>
                                        @else
                                            <div id="imagePreview"
                                                style="background-image: url({{ asset('/admin-assets/assets/img/profile_img/user/common.png') }});">
                                            </div>
                                        @endif 
                                    </div>
                                </div>
                            </form>
                            <h3 class="profile-username text-center">{{ $staff->first_name . ' ' . $staff->last_name }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-7 col-12">
                    <div class="card">
                        <div class="card-header p-2 tab_bg">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#information"
                                        data-toggle="tab">Info</a></li>
                                <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">

                                <!------Information Tab-------->
                                <div class="tab-pane tab-pane-one active" id="information">
                                    <div class="form-group row label_size">
                                        <label class="col-form-label">Full Name :</label>
                                        <div class=" col-form-label">{{ $staff->first_name . ' ' . $staff->last_name }}</div>
                                    </div>
                                    <div class="form-group row label_size">
                                        <label class=" col-form-label lable-e">Email : </label>
                                        <div class=" col-form-label  ">{{ $staff->email }}</div>
                                    </div>
                                    <div class="form-group row label_size">
                                        <label class=" col-form-label">Country : </label>
                                        <div class=" col-form-label">{{ $staff->country }}</div>
                                    </div>
                                    <div class="form-group row label_size">
                                        <label class=" col-form-label ">Mobile : </label>
                                        <div class=" col-form-label">{{ $staff->country_code . ' '. $staff->phone }}</div>
                                    </div>
                                </div>

                                <!------Setting Tab-------->
                                <div class="tab-pane tab-pane-one" id="settings">
                                    <form class="form-horizontal" method="post"
                                        action="{{ route('admin.profile.update') }}" name="general_info" id="general_info">
                                        @csrf
                                        <div class="form-group row ">
                                            <label for="inputName" class=" col-form-label">Full Name</label>
                                            <div class="poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="text"
                                                    class="form-control @error('first_name') is-invalid @enderror" id="name"
                                                    name="first_name" placeholder="Full Name" value="{{ $staff->first_name}}">
                                                @error('first_name')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label for="inputName" class="col-form-label">Last Name</label>
                                            <div class="poduct_input_all_product poduct_input_all_product-admin ">
                                                <input type="text"
                                                    class="form-control @error('last_name') is-invalid @enderror" id="name"
                                                    name="last_name" placeholder="Last Name" value="{{ $staff->last_name}}">
                                                @error('last_name')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label for="inputEmail" class=" col-form-label">Email</label>
                                            <div class="poduct_input_all_product poduct_input_all_product-admin ">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email" placeholder="Email" value="{{ $staff->email }}">
                                                @error('email')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <label for="country" class=" col-form-label">Country</label>
                                            <div class="poduct_input_all_product poduct_input_all_product-admin ">
                                                <select name="country" id="country"
                                                    class="form-control @error('country') is-invalid @enderror">
                                                    <option value="" hidden>Select country</option>
                                                    @if ($countries->isNotEmpty())
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $staff->country }}"
                                                                {{ $staff->country == $country->name ? 'selected' : '' }}>
                                                                {{ $country->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('country')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label for="phone" class=" col-form-label">Phone</label>
                                            <div class=" phone_width poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="tel" name="phone"
                                                    class="form-control phoneInput @error('phone') is-invalid @enderror"
                                                    placeholder="Enter Phone Number" id="phone" value="{{ $staff->country_code . $staff->phone }}">
                                                <input type="hidden" name="country_code" id="country_code">
                                                <span id="error-msg" class="hide"></span>
                                                <span id="valid-msg" class="hide"></span>
                                                {{-- <span id="valid-msg" class="hide">✓ Valid</span> --}}

                                                @error('phone')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row mb-5">
                                            <div class=" product_save_cancal_btn product_save_cancal_btn-admin">
                                                <button type="submit" class="btn">Submit</button> 
                                            </div>
                                        </div>
                                    </form>

                                    <!-----Password---->
                                    <form class="form-horizontal" action="{{ route('admin.profile.password.update') }}"
                                        method="post" name="password_info" id="password_info">
                                        @csrf

                                        <div class="form-group row ">
                                            <label for="inputName" class=" col-form-label">Current
                                                Password</label>
                                            <div class="poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="password"
                                                    class="form-control @error('current_password') is-invalid @enderror"
                                                    id="current_password" name="current_password"
                                                    placeholder="Current Password" value="{{ old('current_password') }}">
                                                @error('current_password')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <label for="inputName" class=" col-form-label">New Password</label>
                                            <div class="poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="New Password"
                                                    value="{{ old('password') }}">
                                                @error('password')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <label for="inputEmail" class=" col-form-label">Confirm
                                                Password</label>
                                            <div class="poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="password"
                                                    class="form-control @error('confirm_password') is-invalid @enderror"
                                                    id="confirm_password" name="confirm_password"
                                                    placeholder="Confirm Password" value="{{ old('confirm_password') }}">
                                                @error('confirm_password')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class=" product_save_cancal_btn product_save_cancal_btn-admin">
                                                <button type="submit" class="btn">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('customJs')
    <script src="{{ asset('admin-assets/assets/intel-tel/js/intlTelInputWithUtils.js') }}"></script>
    <script src="{{ asset('admin-assets/assets/intel-tel/js/utils.js?1715508103106') }}"></script>
    <script type="text/javascript">
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                    $('#imagePreview').hide();
                    $('#imagePreview').fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#adminimageUpload').change(function(e) {
            e.preventDefault();
            $('#upload-image-form').submit();
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
