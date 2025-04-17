@extends('admin.index')
@section('title', 'Edit-Product-Flash-Deal')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h1>Shipping Charge</h1>
                <ol class="breadcrumb user_staff_breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content content_product_block">
    <div class="container-fluid">
        <form method="POST" action="{{ route('setting.update_shipping_charge') }}">
            @csrf
            <div class="row">
                <div class="col-md-12 new_pro_duct_add_block">
                    <div class="row">
                        <div class="row col-md-12" id="flash_deal_dates">
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="s_amount">Free Shipping After Amount:</label>
                                    <input type="number" class="datetime form-control" id="shipping_amount" name="shipping_amount" value="{{ $shipping_data->shipping_amount }}" placeholder="Enter Shipping Amount" autocomplete="off">
                                    @error('shipping_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="poduct_input_all_product form-group">
                                    <label for="Scharges_label" id="Scharges_label">Shipping Charges Before Amount:<span id="Scharges_label_span">{{ $shipping_data->shipping_amount }} (₦)</span></label>
                                    <input type="number" class="datetime form-control" id="after_charges" name="after_charges" value="{{ $shipping_data->after_charges }}" placeholder="Enter Charges" autocomplete="off">
                                    @error('after_charges')
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

{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" rel="stylesheet" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script> --}}

<script>
$(document).ready(function () {
    const shippingAmountInput = document.getElementById('shipping_amount');
    // const label_field = document.getElementById('Scharges_label');

    // Add an event listener for the 'input' event
    shippingAmountInput.addEventListener('input', function(event) {
        const value = event.target.value;
        
        // $('#Scharges_label')='Shipping Amount After charges: 'value;
        // label_field=`Shipping Amount After Charges:`.value;
        $('#Scharges_label_span').html(value);

    });
   
});
</script>
@endsection
