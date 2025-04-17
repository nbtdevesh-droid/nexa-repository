@extends ('admin/index')
@section('title', 'admin-profile')
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
                <div class=" col-lg-4 col-md-5  col-12">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline admin_edit_profile_image">
                        <div class="card-body box-profile">
                            <form method="post" action="{{ route('admin.uploadimage') }}" id="upload-image-form"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="avatar-upload">
                                    <div class="avatar-edit">
                                        <input type='file' name="image" id="adminimageUpload" accept=".png, .jpg, .jpeg, .webp" onchange="readURL(this);" />
                                        <label for="adminimageUpload"></label>
                                    </div>
                                    <div class="avatar-preview">

                                        @if ($admin->pro_img != "")
                                            <div id="imagePreview" style="background-image: url({{ asset('/admin-assets/assets/img/profile_img/admin/') }}/{{ $admin->pro_img }});">
                                            </div>
                                        @else
                                            <div id="imagePreview" style="background-image: url({{ asset('/admin-assets/assets/img/profile_img/user/common.png') }});">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </form>
                            <h3 class="profile-username text-center">{{ $admin->name }}</h3>
                        </div>
                    </div>
                </div>

                <div class=" col-lg-8 col-md-7 col-12">
                    <div class="card">
                        <div class="card-header p-2 tab_bg">
                            <ul class="nav nav-pills ">
                                <li class="nav-item"><a class="nav-link active" href="#information" data-toggle="tab">Info</a></li>
                                <li class="nav-item"><a class="nav-link " href="#settings" data-toggle="tab">Settings</a></li>
                            </ul>
                        </div>
                        <div class="card-body profile-card-body">
                            <div class="tab-content">

                                <!------Information Tab-------->
                                <div class="tab-pane tab-pane-one active" id="information">
                                    <div class="form-group row">
                                        <label class="col-form-label">Full Name :</label>
                                        <div class=" col-form-label">{{ $admin->name }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label class=" col-form-label lable-e ">Email : </label>
                                        <div class=" col-form-label">{{ $admin->email }}</div>
                                    </div>
                                    <div class="form-group row">
                                        <label class=" col-form-label">Mobile : </label>
                                        <div class=" col-form-label">{{ $admin->mobile }}</div>
                                    </div>
                                </div>

                                <!------Setting Tab-------->
                                <div class="tab-pane tab-pane-one" id="settings">
                                    <form class="form-horizontal" method="post" action="{{ route('admin.profile.update') }}" name="general_info" id="general_info">
                                        @csrf
                                        <div class="form-group row">
                                            <label for="inputName" class=" col-form-label">Full Name</label>
                                            <div class=" poduct_input_all_product  poduct_input_all_product-admin" >
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Full Name" value="{{ $admin->name}}">
                                                @error('name')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputEmail" class=" col-form-label">Email</label>
                                            <div class=" poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" value="{{ $admin->email }}">
                                                @error('email')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName2" class=" col-form-label">phone</label>
                                            <div class=" poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" placeholder="Mobile" value="{{ $admin->mobile }}">
                                                @error('mobile')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row mb-5">
                                            <div class="product_save_cancal_btn product_save_cancal_btn-admin">
                                                <button type="submit" class="btn ">Submit</button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-----Password---->
                                    <form class="form-horizontal" action="{{ route('admin.profile.password.update') }}"
                                        method="post" name="password_info" id="password_info">
                                        @csrf

                                        <div class="form-group row">
                                            <label for="inputName" class=" col-form-label">Current Password</label>
                                            <div class=" poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                                    id="current_password" name="current_password" placeholder="Current Password" value="{{ old('current_password') }}">
                                                @error('current_password')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputName" class=" col-form-label">New Password</label>
                                            <div class=" poduct_input_all_product poduct_input_all_product-admin">
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="password" name="password" placeholder="New Password"
                                                    value="{{ old('password') }}">
                                                @error('password')
                                                    <p class="invalid-feedback">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="inputEmail" class=" col-form-label">Confirm
                                                Password</label>
                                            <div class=" poduct_input_all_product poduct_input_all_product-admin">
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
                                            <div class="  product_save_cancal_btn product_save_cancal_btn-admin">
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
@endsection
@section('customJs')
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
@endsection
