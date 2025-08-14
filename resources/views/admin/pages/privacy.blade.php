@extends('admin.index')
@section('title', 'Privacy-Policy')
@section('content')
    <section class="content-header">
    <div class="container-fluid">
        <div class="row ">
                <div class="col-sm-6">
                <h1>Edit Privacy-Policy</h1>
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
                <form action="{{ route('customer.page.update', $privacy_policy->id) }}" method="POST" name="CustomerSupport" id="CustomerSupport" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="page" value="privacy">
                            <div class="poduct_input_all_product mb-3">
                                <label for="image">Choose Image </label>
                                <input type="file" name="image1" id="image1" accept=".png, .jpg, .jpeg" class="form-control">
                                <img src="{{ asset('admin-assets/assets/img/customer_support') }}/{{ $privacy_policy->image1 }}" alt="" width="100px" height="100px">
                            </div>
                            <div class="poduct_input_all_product mb-3">
                                <label for="description">Description 1</label>
                                <textarea name="description1" id="description1" cols="30" rows="10" class="summernote"
                                    placeholder="Description">{{ $privacy_policy->description1 }}</textarea>
                            </div>
                            <div class="poduct_input_all_product mb-3">
                                <label for="image">Choose Image </label>
                                <input type="file" name="image2" id="image2" accept=".png, .jpg, .jpeg" class="form-control">
                                <img src="{{ asset('admin-assets/assets/img/customer_support') }}/{{ $privacy_policy->image2 }}" alt="" width="100px" height="100px">
                            </div>
                            <div class="poduct_input_all_product mb-3">
                                <label for="description">Description 2</label>
                                <textarea name="description2" id="description2" cols="30" rows="10" class="summernote"
                                    placeholder="Description">{{ $privacy_policy->description2 }}</textarea>
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
