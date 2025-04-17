@extends('admin.index')
@section('title', 'Edit-Product-Flash-Deal')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1>Product Flash Deal Timing</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content content_product_block">
    <div class="container-fluid">
        <form method="POST" action="{{ route('setting.store-flash_deal') }}">
            @csrf
            <div class="row">
                <div class="col-md-12 new_pro_duct_add_block">
                    <div class="row">
                        <div class="row col-md-12" id="flash_deal_dates">
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="start_at">Start Date & Time:</label>
                                    <input type="text" class="datetime form-control" id="start_at" name="start_at" value="{{ old('start_at') }}" placeholder="Enter start date & time" autocomplete="off">
                                    @error('start_at')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="expire_at">Expire Date & Time:</label>
                                    <input type="text" class="datetime form-control" id="expire_at" name="expire_at" value="{{ old('expire_at') }}" placeholder="Enter expire date & time" autocomplete="off">
                                    @error('expire_at')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="quantity">Quantity:</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" placeholder="Enter Quantity" autocomplete="off">
                                    @error('quantity')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
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

@section('customJs')
<!-- Include moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>

<script>
$(document).ready(function () {
    $('.datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
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

    $('.datetime').on('focus', function (e) {
        $(this).data('DateTimePicker').show();  
    }).on('dp.show', function (e) {
        if (!$(this).is(':focus')) {
            $(this).data('DateTimePicker').hide();  
        }
    });

    $('form').on('submit', function (e) {
        document.activeElement.blur();  
    });
});
</script>
@endsection
