@extends('admin.index')
@section('title', 'Terms-of-Use')
@section('content')
    <section class="content-header">
    <div class="container-fluid">
        <div class="row ">
                <div class="col-sm-6">
                <h1>Edit Terms-of-Use</h1>
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
                <form action="{{ route('customer.page.update', $terms->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="poduct_input_all_product mb-3">
                                <label for="heading">Heading</label>
                                <input type="text" name="page" value="{{ $terms->page }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="poduct_input_all_product mb-3">
                                <label for="description">Description</label>
                                <textarea name="description1" id="description1" cols="30" rows="10" class="summernote1" placeholder="Description">{{ $terms->description1 }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="pb-5 pt-3 product_save_cancal_btn justify-content-center">
                        <button type="submit" class="btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('customJs')
    <script>
        $(function() {
            Dropzone.autoDiscover = false;
            $('.summernote1').summernote({
                height: '700px'
            });
        });
    </script>
@endsection
