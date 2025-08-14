@extends ('admin/index')
@section('title', 'All-Review')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>All Reviews</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">All Reviews</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="add_new_staff">
                        <img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" width="16"
                            height="16" alt="Add_circle">
                        <a href="{{ route('add.reviews') }}">ADD Reviews</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="datatable_dashboard header-table">
                        <h6>All Reviews</h6>
                        <div class="table-responsive">
                            <table id="all-coupon-table" cellpadding="0" cellspacing="0" class="w-100">
                                <thead>
                                    <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important;">
                                        <th class="pro_duct_th">ID</th>
                                        <th>Customer Name</th>
                                        <th>Product Name</th>
                                        <th>Rating</th>
                                        <th>Review</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($all_reviews->isNotEmpty())
                                        @foreach ($all_reviews as $review)
                                            <tr>
                                                <td class="sr_no">{{ ($all_reviews->currentpage() - 1) * $all_reviews->perpage() + $loop->index + 1 }}</td>
                                                <td>{{ $review->user->first_name . ' ' . $review->user->last_name }}</td>
                                                <td>
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $review->product->product_name }}">
                                                        {{ strip_tags(Str::limit($review->product->product_name, 50, '...')) }}
                                                    </span>
                                                </td>
                                                <td>{{ $review->rating }}</td>
                                                <td>
                                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $review->description }}">
                                                        {{ strip_tags(Str::limit($review->description, 50, '...')) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <form method="post" action="{{ route('review.destroy', $review->id) }}">
                                                        @method('POST')
                                                        @csrf
                                                        <a href="{{ route('review.destroy', $review->id) }}"
                                                            class="delete_category text-danger admin_delete_category show_confirm">
                                                            <i class="fa fa-trash"
                                                                style="margin-right:10px; font-size:18px;"></i>
                                                        </a>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="10" style="text-align: center;">Records Not Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix category_foot" id="paginationLinks">
                        {{ $all_reviews->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
@section('customJs')
<script>
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endsection
