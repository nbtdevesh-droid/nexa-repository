@extends('admin/index')
@section('title', 'All-user')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>User</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><a href="{{ route('user.index') }}">User</a></li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="add_new_staff">  
                        <a href="{{ route('order.export_user') }}" style="margin-left: 10px;">Export Users</a>
                        <a href="{{ route('user.create') }}"><img src="{{ asset('/admin-assets/assets/img/menu-icon/Add_circle.svg') }}" height="16" alt="Add_circle"> ADD NEW</a>
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
                            <h6>All Wholesaler</h6>
                            <div class="th_search">
                               <form id="searchForm" action="{{ route('user.index') }}" method="GET"
                                    class="d-flex">
                                    <input type="text" placeholder="Search" name="keyword"
                                        id="keyword"class="table-header-search-btn">
                                    <button type="submit"><i class="fas fa-search"
                                            style="margin-left:-60px !important; color:#FF8300;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="w-100" cellpadding="0" cellspacing="0">
                                <thead>
                                    <tr class="tr_dashboard" style="border-bottom:1px solid #FF8300 !important; border-top:1px solid #FF8300 !important;">
                                        <th class="pro_duct_th" style="width:8%;">ID</th>
                                        <th style="width:30%;">Full Name</th>
                                        <th style="width:30%;">Email</th>
                                        <th style="width:13%;">Mobile</th>
                                        <th style="width:10%;">Status</th>
                                        <th style="width:10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="userTableBody">
                                    @if ($users_list->isNotEmpty())
                                        @foreach ($users_list as $user)
                                            <tr>
                                                <td class="sr_no">
                                                    {{ ($users_list->currentpage() - 1) * $users_list->perpage() + $loop->index + 1 }}
                                                </td>
                                                <td class="img_name_gap">
                                                    @if ($user->image)
                                                        <img width="50px" height="50px"
                                                            src="{{ asset('admin-assets/assets/img/profile_img/user') }}/{{ $user->image }}"
                                                            alt="User profile picture" style="border-radius:10px;">
                                                    @else
                                                        <img width="50px" height="50px"
                                                            src="{{ asset('admin-assets/assets/img/profile_img/user/common.png') }}"
                                                            alt="User profile picture" style="border-radius:10px;">
                                                    @endif
                                                    {{ $user->first_name . ' ' . $user->last_name }}
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->country_code . ' ' . $user->phone }}</td>
                                                <td>
                                                    @if ($user->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('user.edit', $user->id) }}" type="button" class="text-info"><i class="fa fa-pencil" style="font-size:18px; gap:3px;"></i></a>
                                                        <form method="post" action="{{ route('user.destroy', $user->id) }}">
                                                            @method('DELETE')
                                                            @csrf
                                                            <a href="{{ route('user.destroy', $user->id) }}" style="padding:0 6px 0 6px;" class="delete_user text-danger admin_delete_user show_confirm"><i class="fa fa-trash" style="font-size:18px; gap:3px;"></i></a>
                                                        </form>
                                                        <a href="#" data-toggle="modal" class="view-user" data-target="#modal-default" data-id="{{ $user->id }}"><i class="fa-regular fa-eye" style="font-size:18px; color:#000;"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" style="text-align: center;">Records Not Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix product_foot" id="paginationLinks">
                            <!-- {{ $users_list->links() }} -->
                        {{ $users_list->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal_user_view fade" id="modal-default">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">User Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="user-details">
                    <!-- User details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('customJs')
    <script type="text/javascript">
        $(document).ready(function() {
            function loadUsers(data = {}) {
                $.ajax({
                    url: "{{ route('user.index') }}",
                    type: "GET",
                    data: data,
                    success: function(response) {
                        $('#userTableBody').empty();
                        $.each(response.data, function(index, user) {
                            var editUrl = `{{ route('user.edit', ':id') }}`.replace(
                                ':id', user.id);
                            var deleteUrl = `{{ route('user.destroy', ':id') }}`
                                .replace(':id', user.id);

                            $('#userTableBody').append(
                                `<tr>
                        <td class="sr_no">${(response.current_page - 1) * response.per_page + index + 1}</td>
                        <td class="img_name_gap">
                            ${user.image ?
                                `<img width="50px" height="50px" src="{{ asset('admin-assets/assets/img/profile_img/user') }}/${user.image}" alt="User profile picture" style="border-radius:10px;">`
                                :
                                `<img width="50px" height="50px" src="{{ asset('admin-assets/assets/img/profile_img/user/common.png') }}" alt="User profile picture" style="border-radius:10px;">`
                            }
                            ${user.first_name ? user.first_name : ''} ${user.last_name ? user.last_name : ''}
                        </td>
                        <td>${user.email ? user.email : ''}</td>
                        <td>${user.country_code != null ? user.country_code : ''} ${user.phone != null ? user.phone : ''}</td>
                        <td>${user.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</td>
                        <td>
                            <div class="d-flex">
                                <a href="${editUrl}" class="text-info"><i class="fa fa-pencil" style="font-size:18px; gap:3px;"></i></a>
                                <form method="post" action="${deleteUrl}">
                                    @method('DELETE')
                                    @csrf
                                    <a href="#" class="delete_user text-danger admin_delete_user show_confirm" style="padding:0 6px 0 6px">
                                        <i class="fa fa-trash" style="font-size:18px; gap:3px;"></i>
                                    </a>
                                </form>
                                <a href="#" class="view-user" data-toggle="modal" data-target="#modal-default" data-id="${user.id}">
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
                loadUsers({ keyword: keyword });
            });

            $(document).on('click', '#paginationLinks a', function(e) {
                e.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                var keyword = $('#keyword').val();
                loadUsers({ page: page, keyword: keyword });
            });

            $(document).on('click', '.view-user', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('user.show', '') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        var user = response;
                        $.each(response, function(index, user) {

                            var userImage = user.image ?
                                '{{ asset('admin-assets/assets/img/profile_img/user') }}/' +
                                user.image :
                                '{{ asset('admin-assets/assets/img/profile_img/user/common.png') }}';
                            var name = user.first_name + ' ' + user.last_name;
                            var full_name = name ? name : '';
                            var email = user.email ? user.email : '';
                            var country_code = user.country_code ? user.country_code :
                                '';
                            var country = user.country ? user.country : '';
                            var address = user.shipping_address ? user
                                .shipping_address : 'No address available';

                            var addressesHtml = '';
                            if (address && address.length > 0) {
                                $.each(address, function(index, address) {
                                    addressesHtml +=
                                        '<li class="list-group-item"><b>Shipping Address ' +
                                        (index + 1) +
                                        '</b> <div class="float-right" style="text-align:right;">' +
                                        address.name + '<br>' + address
                                        .country_code + ' ' + address.phone +
                                        '<br>' + address.address + ', '+ '<br>' + address.zip_code +
                                        '</div></li>';
                                });
                            } else {
                                addressesHtml =
                                    '<li class="list-group-item"><b>Shipping Address</b> <div class="float-right">No address available</div></li>';
                            }

                            $('#user-details').html(
                                '<div class="text-center">' +
                                '<img class="profile-user-img img-fluid img-circle" src="' + userImage + '" alt="User profile picture">' +
                                '</div>' +
                                '<h3 class="profile-username text-center">' + full_name + '</h3>' +
                                '<ul class="list-group list-group-unbordered mb-3">' +
                                '<li class="list-group-item"><b>Email</b> <div class="float-right">' + email + '</div></li>' +
                                '<li class="list-group-item"><b>Mobile</b> <div class="float-right">' + country_code + ' ' + user.phone + '</div></li>' +
                                '<li class="list-group-item"><b>Country</b> <div class="float-right">' + country + '</div></li>' +
                                '<li class="list-group-item"><b>Status</b> <div class="float-right">' + (user.status == 1 ? 'Active' : 'Inactive') + '</div></li>' + addressesHtml +
                                '</ul>'
                            );
                        })
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
