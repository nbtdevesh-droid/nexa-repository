@extends('admin.index')
@section('title', 'Home Banner')
@section('css')
    <style>
        .product_save_cancal_btn a{
            border: 1px solid #FF8300;
            font-size: 16px;
            font-weight: bold;
            color: #FF8300;
            max-width: 100%;
            width: 100px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 5px;
        }
        .image_add_btn a{
            background: linear-gradient(0deg, rgba(255, 128, 8, 1) 0%, rgba(255, 175, 55, 1) 100%);
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            max-width: 100%;
            width: 180px;
            height: 50px;
            border-radius: 10px;
        }
    </style>
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-sm-6">
                    <h1>Edit Home Banner</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="add_new_form">
                <form action="{{ route('home.banner.update') }}" method="POST" name="HomeBanner" id="HomeBanner" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            @if($home_banners->isNotEmpty())
                                @foreach ($home_banners as $index => $home)
                                    <div class="poduct_input_all_product mb-3">
                                        <label for="image_{{ $home->id }}">Choose Banner Image</label>
                                        <div class="d-flex justify-content-between">
                                            <input type="file" name="image[{{ $home->id }}]" id="image_{{ $home->id }}" accept=".png, .jpg, .jpeg" class="form-control image" data-id="{{ $home->id }}">
                                            @if($index != 0)
                                                <div class="input-group-btn product_save_cancal_btn">
                                                    <a href="javascript:void(0)" class="btn delete" data-id="{{ $home->id }}"><i class="glyphicon glyphicon-remove"></i>Delete</a>
                                                </div>
                                            @endif
                                        </div>
                                        <img src="{{ asset('admin-assets/assets/img/home_banner') }}/{{ $home->image }}" alt="" width="100px" height="100px" id="preview_{{ $home->id }}" style="border-radius:10px;">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div id="newfile" class="col-md-12"></div>
                        @if($home_banners->count() != 5)
                            <div class="col-md-12 mb-3 image_add_btn">
                                <a class="btn text-white" id="add_more_image">Add More</a>
                            </div>
                        @endif
                    </div>
                    {{-- @if($home_banners->count() != 5) --}}
                        <div class="pb-5 pt-3 save_btn_add">
                            <button type="submit" class="btn text-white">Submit</button>
                        </div>
                    {{-- @else
                        <div class="pb-5 pt-3 save_btn_add">
                            <button class="btn text-white" type="submit"
                                onclick="window.location.href='{{ route('home.banner') }}'">Submit</button>
                        </div>
                    @endif --}}
                </form>
            </div>
        </div>
    </section>
@endsection

@section('customJs')
    <script>
        $(document).ready(function(){
            $('.image').on('change', function () {
                const file = this.files[0];
                const id = $(this).data('id');
                if (file) {
                    var form_data = new FormData();
                    form_data.append("image", file);
                    form_data.append("id", id);

                    let reader = new FileReader();
                    reader.onload = function(event){
                        $('#preview_' + id).attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);

                    $.ajax({
                        type: "POST",
                        url: "{{ route('home.banner.update') }}",
                        data: form_data,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function (response) {
                            console.log(response);
                            if (response.status == true) {
                                toastr.success(response.message);
                                window.location.href = "{{ route('home.banner') }}";
                            }else{
                                toastr.error(response.message);
                            }
                        },
                    });
                }
            });

            let max_fields = 5; // Maximum number of new image fields
            let existing_images = {{ $home_banners->count() }}; // Count of existing images
            let added_fields = 0;

            $("#add_more_image").on("click", function() {
                if (existing_images + added_fields < max_fields) {
                    $("#newfile").append(`
                        <div class="row poduct_input_all_product mb-3">
                            <div class="d-flex justify-content-between">
                                <input type="file" name="image[]" accept=".png, .jpg, .jpeg, .webp" class="form-control image col-md-12">
                                <div class="input-group-btn product_save_cancal_btn">
                                    <a href="javascript:void(0)" class="btn" id="removerow"><i class="glyphicon glyphicon-remove"></i>Delete</a>
                                </div>
                            </div>
                        </div>
                    `);
                    added_fields++;
                } else {
                    alert("You can only have a total of " + max_fields + " images.");
                }
            });

            $("#newfile").on("click", "#removerow", function() {
                $(this).closest('.row').remove();
                added_fields--;
            });

            $(document).on('click', '.delete', function () {
                var data_id = $(this).data("id");

                if (confirm("Are you sure you want to delete?")) {
                    $.ajax({
                        type: "get",
                        url: "{{route('banner.image.delete')}}",
                        data: {
                            data_id: data_id,
                        },
                        success: function (response) {
                            if (response.status == true) {
                                toastr.success(response.message);
                                 window.location.href = "{{ route('home.banner') }}";
                            }else{
                                toastr.error(response.message);
                            }
                        },
                    });
                }
            });
        });
    </script>
@endsection
