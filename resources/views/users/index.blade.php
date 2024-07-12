@extends('layouts.admin')

@section('title', 'User Management')
@section('content-header', 'User Management')
@section('content-actions')
    <a href="{{ route('users.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Users</a>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8">
                <!-- User Filter Dropdowns -->
                <form id="user-filter-form" action="{{ route('users.index') }}" method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="profile-filter" class="mr-2">Filter by Profile:</label>
                        <select name="profile_verified" id="profile-filter" class="form-control">
                            <option value="">All</option>
                            <option value="1" {{ request()->input('profile_verified') === '1' ? 'selected' : '' }}>Verified</option>
                            <option value="0" {{ request()->input('profile_verified') === '0' ? 'selected' : '' }}>Not Verified</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cover-filter" class="mr-2">Filter by Cover Image:</label>
                        <select name="cover_img_verified" id="cover-filter" class="form-control">
                            <option value="">All</option>
                            <option value="1" {{ request()->input('cover_img_verified') === '1' ? 'selected' : '' }}>Verified</option>
                            <option value="0" {{ request()->input('cover_img_verified') === '0' ? 'selected' : '' }}>Not Verified</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-right">
                <!-- Search Form -->
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by...">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                    <th>Actions</th>
                    <th>ID <i class="fas fa-sort"></i></th>
                    <th>Profile</th>
                    <th>Cover Image</th>
                    <th>Name <i class="fas fa-sort"></i></th>
                    <th>Unique Name <i class="fas fa-sort"></i></th>
                    <th>Email <i class="fas fa-sort"></i></th>
                    <th>Mobile <i class="fas fa-sort"></i></th>
                    <th>Age <i class="fas fa-sort"></i></th>
                    <th>Gender <i class="fas fa-sort"></i></th>
                    <th>State <i class="fas fa-sort"></i></th>
                    <th>City <i class="fas fa-sort"></i></th>
                    <th>Profession <i class="fas fa-sort"></i></th>
                    <th>Refer Code <i class="fas fa-sort"></i></th>
                    <th>Referred By <i class="fas fa-sort"></i></th>
                    <th>Points <i class="fas fa-sort"></i></th>
                    <th>Total Points <i class="fas fa-sort"></i></th>
                    <th>Introduction <i class="fas fa-sort"></i></th>
                    <th>Verified <i class="fas fa-sort"></i></th>
                    <th>Online Status <i class="fas fa-sort"></i></th>
                    <th>Dummy <i class="fas fa-sort"></i></th>
                    <th>Message Notify <i class="fas fa-sort"></i></th>
                    <th>Add Friend Notify <i class="fas fa-sort"></i></th>
                    <th>View Notify <i class="fas fa-sort"></i></th>
                    <th>Profile Verified <i class="fas fa-sort"></i></th>
                    <th>Cover Image Verified <i class="fas fa-sort"></i></th>
                    <th>DateTime <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                    <td>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete" data-url="{{route('users.destroy', $user)}}"><i class="fas fa-trash"></i></button>
                        </td>
                        <td>{{$user->id}}</td>
                        <td>
    @if(Str::startsWith($user->profile, ['http://', 'https://']))
        <a href="{{ asset('storage/app/public/users/' . $user->profile) }}" data-lightbox="profile-{{ $user->id }}">
        <img class="customer-img img-thumbnail img-fluid rounded-circle" src="{{ $user->profile }}" alt=""
            style="max-width: 100px; max-height: 100px;">
    @else
    <a href="{{ asset('storage/app/public/users/' . $user->profile) }}" data-lightbox="profile-{{ $user->id }}">
        <img class="customer-img img-thumbnail img-fluid rounded-circle" src="{{ asset('storage/app/public/users/' . $user->profile) }}" alt=""
            style="max-width: 100px; max-height: 100px;">
    @endif
</td>
<td>
    @if(Str::startsWith($user->cover_img, ['http://', 'https://']))
        <a href="{{ asset('storage/app/public/users/' . $user->cover_img) }}" data-lightbox="cover_img-{{ $user->id }}">
        <img class="customer-img img-thumbnail img-fluid " src="{{ $user->cover_img }}" alt=""
            style="max-width: 100px; max-height: 100px;">
    @else
    <a href="{{ asset('storage/app/public/users/' . $user->cover_img) }}" data-lightbox="cover_img-{{ $user->id }}">
        <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/users/' . $user->cover_img) }}" alt=""
            style="max-width: 100px; max-height: 100px;">
    @endif
</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->unique_name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->mobile}}</td>
                        <td>{{$user->age}}</td>
                        <td>{{$user->gender}}</td>
                        <td>{{$user->state}}</td>
                        <td>{{$user->city}}</td>
                        <td>{{ optional($user->professions)->profession }}</td>

                        <td>{{$user->refer_code}}</td>
                        <td>{{$user->referred_by}}</td>
                        <td>{{$user->points}}</td>
                        <td>{{$user->total_points}}</td>
                        <td>{{$user->introduction}}</td>
                        <td>
                        <span class="{{ $user->verified == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->verified == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>
                        <td>
                        <span class="{{ $user->online_status == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->online_status == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>
                        <td>
                        <span class="{{ $user->dummy == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->dummy == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>
                        <td>
                        <span class="{{ $user->view_notify == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->view_notify == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>

                        <td>
                        <span class="{{ $user->add_friend_notify == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->add_friend_notify == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>

                        <td>
                        <span class="{{ $user->view_notify == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->view_notify == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>

                        <td>
                        <span class="{{ $user->profile_verified == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->profile_verified == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>

                        <td>
                        <span class="{{ $user->cover_img_verified == 1 ? 'text-enable' : 'text-disable' }}">
                                {{ $user->cover_img_verified == 1 ? 'Enable' : 'Disable' }}
                            </span>
                        </td>
                        <td>{{$user->datetime}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->render() }}
    </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
        $(document).ready(function () {

            // Function to handle filtering and pagination
            function applyFiltersAndPagination(pageUrl) {
                var profileVerified = $('#profile-filter').val();
                var coverVerified = $('#cover-filter').val();

                // Check if filters are applied
                if (profileVerified !== '' || coverVerified !== '') {
                    // Append filter parameters to the page URL
                    var separator = pageUrl.includes('?') ? '&' : '?';
                    pageUrl += separator;

                    if (profileVerified !== '') {
                        pageUrl += 'profile_verified=' + profileVerified + '&';
                    }
                    if (coverVerified !== '') {
                        pageUrl += 'cover_img_verified=' + coverVerified + '&';
                    }

                    // Remove trailing '&' if it exists
                    pageUrl = pageUrl.replace(/&$/, '');
                }

                // Navigate to the constructed URL
                window.location.href = pageUrl;
            }

            // Handle filter change
            $('#profile-filter, #cover-filter').change(function () {
                applyFiltersAndPagination('{{ request()->fullUrl() }}');
            });

            // Handle pagination link click
            $('.pagination a').click(function (e) {
                e.preventDefault();
                var pageUrl = $(this).attr('href');
                applyFiltersAndPagination(pageUrl);
            });

            $(document).on('click', '.btn-delete', function () {
                $this = $(this);
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                })

                swalWithBootstrapButtons.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this customer?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.post($this.data('url'), {_method: 'DELETE', _token: '{{csrf_token()}}'}, function (res) {
                            $this.closest('tr').fadeOut(500, function () {
                                $(this).remove();
                            })
                        })
                    }
                })
            });

            $('.table th').click(function () {
                var table = $(this).parents('table').eq(0);
                var index = $(this).index();
                var rows = table.find('tr:gt(0)').toArray().sort(comparer(index));
                this.asc = !this.asc;
                if (!this.asc) {
                    rows = rows.reverse();
                }
                for (var i = 0; i < rows.length; i++) {
                    table.append(rows[i]);
                }
                // Update arrows after sorting
                updateArrows(table, index, this.asc);
            });

            function comparer(index) {
                return function (a, b) {
                    var valA = getCellValue(a, index),
                        valB = getCellValue(b, index);
                    return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
                };
            }

            function getCellValue(row, index) {
                return $(row).children('td').eq(index).text();
            }

            function updateArrows(table, index, asc) {
                table.find('.arrow').remove();
                var arrow = asc ? '<i class="fas fa-arrow-up arrow"></i>' : '<i class="fas fa-arrow-down arrow"></i>';
                table.find('th').eq(index).append(arrow);
            }

            // Function to perform filtering
            function filterUsers(searchValue, profileVerified, coverVerified) {
                var rows = $('.table tbody tr');

                // Loop through each row and hide/show based on search value and filters
                rows.each(function () {
                    var profileVerifiedText = $(this).find('td:eq(7)').text().toLowerCase(); // Profile Verified column (adjust index as per your actual table structure)
                    var coverVerifiedText = $(this).find('td:eq(8)').text().toLowerCase(); // Cover Verified column (adjust index as per your actual table structure)

                    var showRow = true;

                    // Apply search filter
                    var name = $(this).find('td:eq(4)').text().toLowerCase(); // Name column
                    var uniqueName = $(this).find('td:eq(5)').text().toLowerCase(); // Unique Name column
                    var email = $(this).find('td:eq(6)').text().toLowerCase(); // Email column

                    if (!(name.includes(searchValue.toLowerCase()) ||
                        uniqueName.includes(searchValue.toLowerCase()) ||
                        email.includes(searchValue.toLowerCase()))) {
                        showRow = false;
                    }

                    // Apply profile verified filter
                    if (profileVerified !== '' && profileVerified !== 'all' && !profileVerifiedText.includes(profileVerified.toLowerCase())) {
                        showRow = false;
                    }

                    // Apply cover verified filter
                    if (coverVerified !== '' && coverVerified !== 'all' && !coverVerifiedText.includes(coverVerified.toLowerCase())) {
                        showRow = false;
                    }

                    // Show or hide row based on filters
                    if (showRow) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            // Perform filtering when input or filter value changes
            $('#user-search, #profile-filter, #cover-filter').on('input change', function () {
                var searchValue = $('#user-search').val().trim();
                var profileVerified = $('#profile-filter').val().trim();
                var coverVerified = $('#cover-filter').val().trim();

                // Adjust 'all' option handling
                if (profileVerified === 'all') {
                    profileVerified = '';
                }
                if (coverVerified === 'all') {
                    coverVerified = '';
                }

                filterUsers(searchValue, profileVerified, coverVerified);
            });

            // Display all data when page is loaded or refreshed
            filterUsers('', '', '');
            
        });
    </script>
@endsection
