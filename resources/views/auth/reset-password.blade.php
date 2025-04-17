<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>

    <link rel="icon" href="{{ asset('/admin-assets/assets/img/logo/icon.png') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('admin-assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin-assets/dist/css/adminlte.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/css/toastr.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('/admin-assets/assets/css/style.css') }}">
</head> 

<body class="hold-transition login-page" style="background-color:#FFF0E0;">
    <div class="login-box login_page_block">
        <div class="card ">
            <div class="login_main_block">
                <div class="login_fst_section">
                    <div class="login_img">
                        <img src="{{asset('public/admin-assets/assets/img/menu-icon/login_page_img.png')}}"   alt="lolgin_page_img">
                    </div>
                </div>

                <div class="second_section">
                    <div class="card-body">
                        <div class="login_page_logo">
                            <img src="{{asset('public/admin-assets/assets/img/menu-icon/login_logo.png')}}"   alt="lolgin_logo">
                        </div>
                        <div class="login_heading">
                            <h3>Reset Password</h3>
                            <p>Create a new password to access your account. Please ensure your new password is strong and secure.</p>
                        </div>
                        <form action="{{ route('reset-password') }} " method="post">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="input-group ">
                                <div class=" label_input_block email_gap">
                                    <label>Email</label>
                                <div class="input_icon">
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                                    @error('email')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>  
                            </div>
                            <div class="input-group gap_input_block">
                                <div class=" label_input_block">
                                    <label>Create New Password</label>
                                    <div class="input_icon">
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder=" Enter your new password">
                                        @error('password')
                                            <p class="invalid-feedback">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="input-group gap_input_block">
                                <div class=" label_input_block">
                                    <label>Confirm Password</label>
                                    <div class="input_icon">
                                        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder=" Enter your confirm password">
                                        @error('password_confirmation')
                                            <p class="invalid-feedback">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="reset_btn_block">
                                        <button type="submit">Continue</button>
                                    </div>
                                </div>
                            </div>
                            <div class="login_btn_block_rest">
                                <a href="{{ url('/') }}"><i class="fa-solid fa-chevron-left"></i> Login</a>
                            </div>         
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('admin-assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
    <script>
        $(document).ready(function() {
            toastr.options.timeOut = 3000;

            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
    </script>
</body>
</html>
