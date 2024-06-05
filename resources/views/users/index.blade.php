@extends('layouts.admin')

@section('title', 'User Management')
@section('content-header', 'User Management')
@section('content-actions')
    <a href="{{route('users.create')}}" class="btn btn-success"><i class="fas fa-plus"></i> Add New Users</a>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
@endsection
@section('content')
<div class="card">
    <div class="card-body">
    <div class="row mb-4">
        <div class="ml-auto">
        <form action="{{ route('users.index') }}" method="GET">
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
                    <th>Introduction <i class="fas fa-sort"></i></th>
                    <th>Verified <i class="fas fa-sort"></i></th>
                    <th>Online Status <i class="fas fa-sort"></i></th>
                    <th>Dummy <i class="fas fa-sort"></i></th>
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
                        <td>{{$user->profession}}</td>
                        <td>{{$user->refer_code}}</td>
                        <td>{{$user->referred_by}}</td>
                        <td>{{$user->points}}</td>
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