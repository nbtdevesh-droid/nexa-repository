@extends('admin.index')
@section('title', 'App Notification Setting')
@section('css')
<style>
    /* Custom Switch Button */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 25px;
    }

    /* Hide default checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 25px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 4px;
        bottom: 3.5px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    /* When checked, move the slider */
    input:checked+.slider {
        background-color: #4CAF50;
    }

    input:checked+.slider:before {
        transform: translateX(24px);
    }

    /* Round slider */
    .slider.round {
        border-radius: 25px;
    }
</style>
@endsection
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1>App Notification Setting</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content content_product_block">
    <div class="container-fluid">
        <form method="POST" action="{{ route('app.notification.setting.update') }}">
            @csrf
            <div class="row">
                <div class="col-md-12 new_pro_duct_add_block">
                    <div class="row">
                        <div class="col-sm-6 col-8 mb-sm-4 mb-3">
                            <div class="row">
                                <div class="col-sm-8 col-8 mb-sm-4 mb-3">
                                    <h4 class="m-0">Maintenance Setting</h4>
                                </div>
                                <div class="col-sm-4 col-4 mb-sm-4 mb-3">
                                    <label class="switch">
                                        <input type="hidden" name="maintenance_setting" value="0">
                                        <input type="checkbox" name="maintenance_setting" value="1"
                                            {{ old('maintenance_setting', $settings->maintenance_setting ?? 0) == 1 ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- Update Setting -->
                       <div class="col-sm-6 col-8 mb-sm-4 mb-3">
                           <div class="row">
                               <div class="col-sm-8 col-8 mb-sm-4 mb-3">
                                   <h4 class="m-0">Update Setting</h4>
                               </div>
                               <div class="col-sm-4 col-4 mb-sm-4 mb-3">
                                   <label class="switch">
                                       <input type="hidden" name="update_setting" value="0">
                                       <input type="checkbox" name="update_setting" value="1"
                                           {{ old('update_setting', $settings->update_setting ?? 0) == 1 ? 'checked' : '' }}>
                                       <span class="slider round"></span>
                                   </label>
                               </div>
                           </div>
                       </div>
                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3 product_save_cancal_btn">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</section>
@endsection
