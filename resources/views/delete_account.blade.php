<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NEXA</title>
    <link rel="icon" href="{{ asset('/admin-assets/assets/img/menu-icon/login_logo.png') }}">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('/admin-assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/admin-assets/assets/css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.0/css/toastr.css" rel="stylesheet" />
</head>

<body class="hold-transition login-page" style="background-color:#FFF0E0;">
    <div class="login-box login_page_block">
        <div class="card ">
            <div class="login_main_block">
                {{-- <div class="login_fst_section">
                    <div class="login_img">
                        <img src="{{asset('admin-assets/assets/img/menu-icon/login_page_img.png')}}"   alt="lolgin_page_img">
                    </div>
                </div> --}}
                <div class="second_section">
                    <div class="card-body">
                        <div class="login_page_logo">
                            <img src="{{asset('admin-assets/assets/img/menu-icon/login_logo.png')}}"   alt="lolgin_logo">
                        </div>
                        <div class="login_heading">
                            <h3>Account Delete</h3>
                            {{-- <p>Welcome back! Please sign in to access the admin dashboard.</p> --}}
                        </div>
                        <form action="{{ route('customer.account.delete') }}" method="post" name="accountDelete" class="accountDelete_form">
                            @csrf
                            <div class="input-group gap_input_block">
                                <div class="mb-3 label_input_block">
                                    <label>Email</label>
                                    <div class="input_icon">
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter Your Email"
                                        value="{{ old('email') }}">
                                        @error('email')
                                            <p class="invalid-feedback">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="label_input_password">
                                    <label>Password</label>
                                    <div class="input_icon">
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder=" Enter Your Password"
                                        value="{{ old('password') }}">
                                        @error('password')
                                            <p class="invalid-feedback">{{ $message }}</p>
                                        @enderror 
                                    </div> 
                                </div>                                
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="login_btn_block">
                                        <button type="submit">Account Delete</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="{{ asset('/admin-assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('/admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
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
