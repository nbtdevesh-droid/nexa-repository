<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('/admin-assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/admin-assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/admin-assets/assets/css/style.css') }}">
</head>
<body>
    <div class="container-fluid bg-white">
        <div class="container">
            <div class="header-logo">
                <div class="logo"> <a href="#"><img src="{{ asset('/admin-assets/assets/img/menu-icon/login_logo.png') }}" alt="logo"></a> </div>
            </div>
            <div class="privacy_section">
                {{-- <h1>privacy-policy</h1> --}}
                <div class="">
                    <div class="row">
                        <div class="col-lg-5 col-md-6 col-12">
                            <div class="privacy-policy-img">
                                <img src="{{ asset('admin-assets/assets/img/customer_support/' . $pagecontent->image1) }}" alt="privacy-policy-img">
                            </div>
                        </div>
                        <div class="col-lg-7 col-md-6 col-12">
                            <div class="privacy-policy-text">
                                {!! $pagecontent->description1 !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="row pt-3">
                        <div class="col-lg-7 col-md-6 col-12  order-2 order-md-1">
                            <div class="privacy-policy-text">
                                {!! $pagecontent->description2 !!}
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-6 col-12 order-1 order-md-2">
                            <div class="privacy-policy-img">
                                <img src="{{ asset('admin-assets/assets/img/customer_support/' . $pagecontent->image2) }}" alt="privacy-policy-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

