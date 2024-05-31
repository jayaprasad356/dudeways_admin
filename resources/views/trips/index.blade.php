@extends('layouts.admin')

@section('title', 'Trips Management')
@section('content-header', 'Trips Management')
@section('content-actions')
    <a href="{{route('trips.create')}}" class="btn btn-success"><i class="fas fa-plus"></i> Add New trips</a>
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
        <!-- User Filter Dropdown -->
        <form id="user-filter-form" action="{{ route('trips.index') }}" method="GET" class="form-inline">
            <div class="form-group">
                <label for="user-filter" class="mr-2">Filter by Users:</label>
                <select name="user_id" id="user-filter" class="form-control">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if($user->id == request()->input('user_id')) selected @endif>{{ $user->name }}</option>
                    @endforeach
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
                    <td>
                            <a href="{{ route('trips.edit', $trip) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-danger btn-delete" data-url="{{route('trips.destroy', $trip)}}"><i class="fas fa-trash"></i></button>
                        </td>
                        <td>{{$trip->id}}</td>
                        <td>
    @if(Str::startsWith($trip->trip_image, ['http://', 'https://']))
        <a href="{{ asset('storage/app/public/trips/' . $trip->trip_image) }}" data-lightbox="trip_image-{{ $trip->id }}">
        <img class="customer-img img-thumbnail img-fluid" src="{{ $trip->trip_image }}" alt=""
            style="max-width: 100px; max-height: 100px;">
    @else 
    <a href="{{ asset('storage/app/public/trips/' . $trip->trip_image) }}" data-lightbox="trip_image-{{ $trip->id }}">
        <img class="customer-img img-thumbnail img-fluid" src="{{ asset('storage/app/public/trips/' . $trip->trip_image) }}" alt=""
            style="max-width: 100px; max-height: 100px;">
    @endif
</td>
                        <td>{{ optional($trip->users)->name }}</td> <!-- Display user name safely -->
                        <td>{{$trip->trip_type}}</td>
                        <td>{{$trip->location}}</td>
                        <td>{{$trip->from_date}}</td>
                        <td>{{$trip->to_date}}</td>
                        <td>{{$trip->trip_title}}</td>
                        <td>{{$trip->trip_description}}</td>
                        <td>
                    @if ($trip->trip_status === 1)
                        <span class="badge badge-success">Approved</span>
                    @elseif ($trip->trip_status === 0)
                        <span class="badge badge-primary">Pending</span>
                    @elseif ($trip->trip_status === 2)
                        <span class="badge badge-danger">Cancelled</span>
                    @endif
                </td>
                        <td>{{$trip->trip_datetime}}</td>
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
            // Submit the form when user selection changes
            $('#user-filter').change(function () {
                if ($(this).val() !== '') {
                    $('#user-filter-form').submit();
                } else {
                    window.location.href = "{{ route('trips.index') }}";
                }
            });
        });
            </script>
            <script>

        $(document).ready(function () {
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
            })
        })
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
@endsection