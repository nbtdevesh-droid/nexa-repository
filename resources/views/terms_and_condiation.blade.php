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
                {{-- <h3> Terms & Condition</h3> --}}
                <div class="terms-contant-text">
                    {!! $pagecontent->description1 !!}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
