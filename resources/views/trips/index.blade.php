@extends('layouts.admin')

@section('title', 'Trips Management')
@section('content-header', 'Trips Management')
@section('content-actions')
    <a href="{{ route('trips.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Trip</a>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-8 d-flex align-items-center">
                <!-- Checkbox for Select All -->
                <div class="form-check mr-3">
                <input type="checkbox" class="form-check-input" id="checkAll">
                <label class="form-check-label" for="checkAll">Select All</label>
                </div>
                
                <!-- Verify Button -->
                <button class="btn btn-primary mr-3" id="pendingButton">Pending</button>
                <button class="btn btn-success mr-3" id="verifyButton">Approved</button>
                <button class="btn btn-danger mr-3" id="cancelButton">Cancelled</button>
                
            </div>
            
            <div class="col-md-8"><br>
                <!-- User Filter Dropdown -->
                <form id="filter-form" action="{{ route('trips.index') }}" method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="user-filter" class="mr-2">Filter by Users:</label>
                        <select name="user_id" id="user-filter" class="form-control">
                            <option value="">All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if($user->id == request()->input('user_id')) selected @endif>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
    <label for="status-filter" class="mr-2">Filter by Status:</label>
    <select name="trip_status" id="status-filter" class="form-control">
        <option value="0" {{ request()->input('trip_status') === '0' ? 'selected' : '' }}>Pending</option>
        <option value="1" {{ request()->input('trip_status') === '1' ? 'selected' : '' }}>Approved</option>
        <option value="2" {{ request()->input('trip_status') === '2' ? 'selected' : '' }}>Cancelled</option>
    </select>
</div>



                </form>
            </div>

            <div class="col-md-4 text-right">
                <!-- Search Form -->
                <form action="{{ route('trips.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by....">
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                    <th>Checkbox</th>
                        <th>Actions</th>
                        <th>ID <i class="fas fa-sort"></i></th>
                        <th>Trip Image</th>
                        <th>User Name <i class="fas fa-sort"></i></th>
                        <th>Trip Type <i class="fas fa-sort"></i></th>
                        <th>Location <i class="fas fa-sort"></i></th>
                        <th>From Date <i class="fas fa-sort"></i></th>
                        <th>To Date <i class="fas fa-sort"></i></th>
                        <th>Trip Title<i class="fas fa-sort"></i></th>
                        <th>Trip Description<i class="fas fa-sort"></i></th>
                        <th>Trip Status</th>
                        <th>Trip DateTime<i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trips as $trip)
                        <tr>
                        <td><input type="checkbox" class="checkbox" data-id="{{ $trip->id }}"></td>
                            <td>
                                <a href="{{ route('trips.edit', $trip) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                                <button class="btn btn-danger btn-delete" data-url="{{ route('trips.destroy', $trip) }}"><i class="fas fa-trash"></i></button>
                            </td>
                            <td>{{ $trip->id }}</td>
                            <td>
                                <a href="{{ asset('storage/app/public/trips/' . $trip->trip_image) }}" data-lightbox="trip_image-{{ $trip->id }}">
                                    <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/trips/' . $trip->trip_image) }}" alt="Trip Image" style="max-width: 100px; max-height: 100px;">
                                </a>
                            </td>
                            <td>{{ optional($trip->users)->name }}</td>
                            <td>{{ $trip->trip_type }}</td>
                            <td>{{ $trip->location }}</td>
                            <td>{{ $trip->from_date }}</td>
                            <td>{{ $trip->to_date }}</td>
                            <td>{{ $trip->trip_title }}</td>
                            <td>{{ $trip->trip_description }}</td>
                            <td>
                                @if ($trip->trip_status === 1)
                                    <span class="badge badge-success">Approved</span>
                                @elseif ($trip->trip_status === 0)
                                    <span class="badge badge-primary">Pending</span>
                                @elseif ($trip->trip_status === 2)
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $trip->trip_datetime }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $trips->render() }}
   
        </div>
</div>

@endsection

@section('js')
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script>
    $(document).ready(function () {
        // Submit the form when user or status selection changes
        $('#user-filter, #status-filter').change(function () {
            var userFilterValue = $('#user-filter').val();
            var statusFilterValue = $('#status-filter').val();
            
            // Check if both filters are empty or only the status filter is selected
            if ((userFilterValue === '' && statusFilterValue === '') || (userFilterValue !== '' && statusFilterValue !== '')) {
                $('#filter-form').submit();
            } else if (statusFilterValue !== '') {
                // If only the status filter is selected, construct the URL without the user_id parameter
                var url = "{{ route('trips.index') }}?trip_status=" + statusFilterValue;
                window.location.href = url;
            } else {
                // If only the user filter is selected, submit the form with both filters
                $('#filter-form').submit();
            }
        });

        // Handle pagination clicks to maintain trip_status parameter
        $('.pagination a').click(function (e) {
            e.preventDefault();
            var pageUrl = $(this).attr('href');
            var statusFilterValue = $('#status-filter').val();
            
            if (statusFilterValue !== '') {
                var separator = pageUrl.includes('?') ? '&' : '?';
                pageUrl += separator + 'trip_status=' + statusFilterValue;
            }

            window.location.href = pageUrl;
        });
    });
    </script>

    <script>
        $(document).ready(function() {
            $('.table th').click(function() {
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
                // Update arrows
                updateArrows(table, index, this.asc);
            });

            function comparer(index) {
                return function(a, b) {
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
        });
    </script>

<script>
$(document).ready(function () {
    // Handle "Select All" checkbox
    $('#checkAll').change(function () {
        $('.checkbox').prop('checked', $(this).prop('checked'));
    });

    // Handle Pending Button click
    $('#pendingButton').click(function () {
        updateStatus(0);
    });

    // Handle Approve Button click
    $('#verifyButton').click(function () {
        updateStatus(1);
    });

    // Handle Cancel Button click
    $('#cancelButton').click(function () {
        updateStatus(2);
    });

    // Update Status function
    function updateStatus(status) {
        var tripIds = [];
        $('.checkbox:checked').each(function () {
            tripIds.push($(this).data('id'));
        });

        if (tripIds.length > 0) {
            $.ajax({
                url: "{{ route('trips.updateStatus') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    trip_ids: tripIds,
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        if (status === 1) {
                            sendNotification();
                        }
                        location.reload();
                    } else {
                        alert('Failed to update status. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('An error occurred. Please try again.');
                }
            });
        } else {
            alert('Please select at least one trip.');
        }
    }

    // Send Notification function
    function sendNotification() {
        $.ajax({
            url: "{{ route('trips.sendNotification') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    console.log('Notification sent successfully.');
                } else {
                    console.error('Failed to send notification.');
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
                alert('An error occurred while sending notification. Please try again.');
            }
        });
    }
});
</script>

@endsection

