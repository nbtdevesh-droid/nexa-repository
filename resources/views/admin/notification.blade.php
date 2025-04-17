@extends('admin/index')
@section('title', 'All Notifications')
@section('content')

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-md-3 col-sm-12 col-12">
                <h1>All Notifications</h1>
            </div>
            <div class="col-md-9 col-sm-12 col-12" style="text-align:end;">
                <button type="button" id="deleteSelected" class="btn btn-danger delete-select-btn">
                    Delete Notifications
                </button>
                <div class="add_new_brand">
                    <a href="{{ route('admin.dashboard') }}" style="width:200px;"> Back</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if($notifications->isEmpty())
                    <div class="alert alert-info text-center">
                        No new notifications.
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <form id="deleteNotificationsForm" method="POST" action="{{ route('delete.notification') }}">
                                @csrf
                                @foreach($notifications as $notification)
                                    <div class="notification-item d-flex mb-3">
                                        <!-- Checkbox for deletion -->
                                        <input type="checkbox" name="notification_ids[]" class="product-checkbox" value="{{ $notification->id }}" style="margin-right: 10px;">
                                        <div class="img_col_round mr-3">
                                            <img src="{{ asset('/admin-assets/assets/img/menu-icon/order_panding.svg') }}"
                                                width="40" height="40px" alt="notification_icon">
                                        </div>
                                        <div class="name_col_font">
                                            <h6 class="mb-1 font-weight-bold">{{ $notification->other_recive_about }}</h6>
                                            <p class="mb-0">{{ $notification->other_recive_notification_content }}</p>
                                            <small>{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </form>
                        </div>
                    </div>
                @endif
                <div class="card-footer clearfix product_foot" id="paginationLinks">
                    {{ $notifications->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJs')
<script>
    $(document).on('click', '#deleteSelected', function(event) {
        event.preventDefault();

        var form = $('#deleteNotificationsForm');
        var checkedBoxes = $('input[name="notification_ids[]"]:checked').length;

        if (checkedBoxes === 0) {
            swal({
                title: "No notifications selected!",
                text: "Please select at least one notification to delete.",
                icon: "info",
            });
            return false;
        }

        swal({
            title: `Are you sure you want to delete selected notifications?`,
            text: "If you delete this, it will be gone forever.",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
    });
</script>
@endsection
