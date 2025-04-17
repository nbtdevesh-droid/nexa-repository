@extends('admin.index')
@section('title', 'All-Staff Members')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User</h1>
                    <ol class="breadcrumb user_staff_breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">All Staff</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="add_new_staff">
                        <a href="{{ route('staff.export_staff') }}" style="margin-left: 10px;">Export Staff</a>
                        <a href="{{ route('staff.create') }}"> <img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" height="16" alt="Add_circle"> ADD NEW</a>
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
                        <div class="table-header-contant-text">
                            <h6>All Staff</h6>
                            <div class="th_search">
                                <form id="searchForm" action="{{ route('staff.index') }}" method="GET" class="d-flex">
                                    <input type="text" placeholder="Search" name="keyword" id="keyword"class="table-header-search-btn">
                                    <button type="submit"><i class="fas fa-search" style="margin-left:-60px !important; color:#FF8300;"></i></button>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="w-100" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important; border-top:1px solid #FF8300 !important;">
                                        <th class="pro_duct_th" style="width:10%;">ID</th>
                                        <th>Full Name</th>
                                        <th >Email</th>
                                        <th >Mobile</th>
                                        <th >Status</th>
                                        <th >Action</th>
                                    </tr>
                                </thead>
                                <tbody id="staffTableBody">
                                    @if ($staff_list->isNotEmpty())
                                        @foreach ($staff_list as $staff)
                                            <tr>
                                                <td class="sr_no">
                                                    {{ ($staff_list->currentpage() - 1) * $staff_list->perpage() + $loop->index + 1 }}
                                                </td>
                                                <td class="img_name_gap">
                                                    @if ($staff->image != '')
                                                        <img width="50px" height="50px"
                                                            src="{{ asset('admin-assets/assets/img/profile_img/staff') }}/{{ $staff->image }}"
                                                            alt="User profile picture" style="border-radius:10px;">
                                                    @else
                                                        <img width="50px" height="50px"
                                                            src="{{ asset('admin-assets/assets/img/profile_img/user/common.png') }}"
                                                            alt="User profile picture" style="border-radius:10px;">
                                                    @endif
                                                    {{ $staff->first_name . ' ' . $staff->last_name }}
                                                </td>
                                                <td>{{ $staff->email }}</td>
                                                <td>{{ $staff->country_code . ' ' . $staff->phone }}</td>
                                                <td>
                                                    @if ($staff->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('staff.edit', $staff->id) }}"
                                                            class="text-info"><i class="fa fa-pencil"
                                                                style="font-size:18px; gap:3px;"></i></a>
                                                        <form method="post"
                                                            action="{{ route('staff.destroy', $staff->id) }}">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="{{ route('staff.destroy', $staff->id) }}"
                                                                class="delete_staff text-danger admin_delete_staff show_confirm"
                                                                style="padding:0 6px 0 6px">
                                                                <i class="fa fa-trash" style="font-size:18px; gap:3px;"></i>
                                                            </a>
                                                        </form>
                                                        <a href="#" class="view-staff" data-toggle="modal"
                                                            data-target="#modal-default" data-id="{{ $staff->id }}"><i
                                                                class="fa-regular fa-eye"
                                                                style="font-size:18px; color:#000;"></i></a>
                                                    </div>
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
                    <div class="pagination_user_staff">
                        <!-- {{ $staff_list->links() }} -->
                        {{ $staff_list->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal_staff_view fade" id="modal-default">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Staff Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="staff-details">
                    <!-- User details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script type="text/javascript">
        $(document).ready(function() {
            function loadStaffs(data = {}) {
                $.ajax({
                    url: "{{ route('staff.index') }}",
                    type: "GET",
                    data: data,
                    success: function(response) {
                        $('#staffTableBody').empty();
                        $.each(response.data, function(index, staff) {
                            var editUrl = `{{ route('staff.edit', ':id') }}`.replace(
                                ':id', staff.id);
                            var deleteUrl = `{{ route('staff.destroy', ':id') }}`
                                .replace(':id', staff.id);

                            $('#staffTableBody').append(
                                `<tr>
                        <td class="sr_no">${(response.current_page - 1) * response.per_page + index + 1}</td>
                        <td class="img_name_gap">
                            ${staff.image ?
                                `<img width="50px" height="50px" src="{{ asset('admin-assets/assets/img/profile_img/staff') }}/${staff.image}" alt="User profile picture" style="border-radius:10px;">`
                                :
                                `<img width="50px" height="50px" src="{{ asset('admin-assets/assets/img/profile_img/user/common.png') }}" alt="User profile picture" style="border-radius:10px;">`
                            }
                            ${staff.first_name} ${staff.last_name}
                        </td>
                        <td>${staff.email}</td>
                        <td>${staff.country_code} ${staff.phone}</td>
                        <td>${staff.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                        <td>
                            <div class="d-flex">
                                <a href="${editUrl}" class="text-info">
                                    <i class="fa fa-pencil" style="font-size:18px; gap:3px;"></i>
                                </a>
                                <form method="post" action="${deleteUrl}">
                                    @method('DELETE')
                                    @csrf
                                    <a href="#" class="delete_staff text-danger admin_delete_staff show_confirm" style="padding:0 6px 0 6px">
                                        <i class="fa fa-trash" style="font-size:18px; gap:3px;"></i>
                                    </a>
                                </form>
                                <a href="#" class="view-staff" data-toggle="modal" data-target="#modal-default" data-id="${staff.id}">
                                    <i class="fa-regular fa-eye" style="font-size:18px; color:#000;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>`
                            );
                        });
                        $('#paginationLinks').html(response.links);
                    }
                });
            }

            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                var keyword = $('#keyword').val();
                loadStaffs({
                    keyword: keyword
                });
            });

            $(document).on('click', '#paginationLinks a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                var keyword = $('#keyword').val();
                loadStaffs({
                    page: page,
                    keyword: keyword
                });
            });

            $(document).on('click', '.view-staff', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('staff.show', '') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        var staff = response;
                        var userImage = staff.image ?
                            '{{ asset('admin-assets/assets/img/profile_img/staff') }}/' + staff
                            .image :
                            '{{ asset('admin-assets/assets/img/profile_img/user/common.png') }}';
                        var name = staff.first_name + ' ' + staff.last_name;
                        var full_name = name ? name : '';
                        var email = staff.email ? staff.email : '';
                        var country_code = staff.country_code ? staff.country_code : '';
                        var country = staff.country ? staff.country : '';

                        $('#staff-details').html(
                            '<div class="text-center">' +
                            '<img class="profile-user-img img-fluid img-circle" src="' +
                            userImage + '" alt="User profile picture">' +
                            '</div>' +
                            '<h3 class="profile-username text-center">' + full_name +
                            '</h3>' +
                            '<ul class="list-group list-group-unbordered mb-3">' +
                            '<li class="list-group-item"><b>Email</b> <div class="float-right">' +
                            email + '</div></li>' +
                            '<li class="list-group-item"><b>Mobile</b> <div class="float-right">' +
                            country_code + ' ' + staff.phone + '</div></li>' +
                            '<li class="list-group-item"><b>Country</b> <div class="float-right">' +
                            country + '</div></li>' +
                            '<li class="list-group-item"><b>Status</b> <div class="float-right">' +
                            (staff.status == 1 ? 'Active' : 'Inactive') + '</div></li>' +
                            '</ul>'
                        );
                    }
                });
            });
        });
        // Use event delegation to attach the click event to dynamically added elements
        $(document).on('click', '.show_confirm', function(event) {
            event.preventDefault(); // Prevent the default form submission

            var form = $(this).closest("form"); // Find the closest form
            var name = $(this).data("name"); // Optional: Get data-name attribute if needed

            swal({
                    title: `Are you sure you want to delete this record?`,
                    text: "If you delete this, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        form.submit(); // Submit the form if the user confirms deletion
                    }
                });
        });
    </script>
@endsection
