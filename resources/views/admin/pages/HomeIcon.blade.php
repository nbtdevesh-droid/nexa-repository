@extends('admin.index')
@section('title', 'Home Banner')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-sm-6">
                    <h1>Edit Home Icons</h1>
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
                <form action="{{ route('home.icon.update') }}" method="POST" name="HomeIcon" id="HomeIcon" enctype="multipart/form-data">
                    @csrf
                    @foreach ($home_icons as $index => $icon)
                        <div class="row">
                            <h3 class="card-title">Icon-{{ $index + 1 }}</h3>
                            <div class="col-md-12">
                                <div class="poduct_input_all_product mb-3 row">
                                    <div class="col-md-6">
                                        <label for="icon_{{ $index + 1 }}">Choose Home Icon {{ $index + 1 }}</label>
                                        <div class="d-flex justify-content-between">
                                            <input type="file" name="icon_{{ $index + 1 }}" id="icon_{{ $index + 1 }}" accept=".png, .jpg, .jpeg" class="form-control image">
                                        </div>
                                        @if ($icon->icon)
                                            <img src="{{ asset('admin-assets/assets/img/Home_icon/' . $icon->icon) }}" alt="Icon {{ $index + 1 }}" class="img-thumbnail" width="50">
                                        @endif
                                        @error("icon_" . ($index + 1))
                                            <p class="invalid-feedback d-block">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="link_{{ $index + 1 }}">Link</label>
                                        <div class="d-flex justify-content-between">
                                            <input type="text" name="link_{{ $index + 1 }}" id="link_{{ $index + 1 }}" class="form-control" value="{{ old('link_' . ($index + 1), $icon->link) }}">
                                        </div>
                                        @error("link_" . ($index + 1))
                                            <p class="invalid-feedback d-block">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="pb-5 pt-3 save_btn_add">
                        <button type="submit" class="btn text-white">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('customJs')

@endsection
