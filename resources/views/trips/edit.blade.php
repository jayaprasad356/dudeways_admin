@extends('layouts.admin')

@section('title', 'Update Trips')
@section('content-header', 'Update Trips')
@section('content-actions')
    <a href="{{route('trips.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Trip</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('trips.update', $trips) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')


                <div class="form-group">
                    <label for="user_id">User ID</label>
                    <input type="number" name="user_id" class="form-control @error('user_id') is-invalid @enderror"
                           id="user_id"
                           placeholder="User ID" value="{{ old('user_id', $trips->user_id) }}">
                    @error('user_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <button type="button" class="btn btn-primary" onclick="toggleUserListModal()">Select User</button>

                <div class="form-group">
                    <br>
                        <label for="planning">Planning</label>
                        <select name="planning" class="form-control @error('planning') is-invalid @enderror" id="profession">
                            <option value=''>--select--</option>
                            <option value='Road Trip' {{ old('planning', $trips->planning) == 'Road Trip' ? 'selected' : '' }}>Road Trip</option>
                            <option value='Adventure Trip' {{ old('planning', $trips->planning) == 'Adventure Trip' ? 'selected' : '' }}>Adventure Trip</option>
                            <option value='Explore Cities' {{ old('planning', $trips->planning) == 'Explore Cities' ? 'selected' : '' }}>Explore Cities</option>
                            <option value='Airport Flyover' {{ old('planning', $trips->planning) == 'Airport Flyover' ? 'selected' : '' }}>Airport Flyover</option>
                        </select>
                        @error('planning')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    
                <div class="form-group">
                    <label for="from_location">From Location</label>
                    <input type="text" name="from_location" class="form-control @error('from_location') is-invalid @enderror"
                           id="from_location"
                           placeholder="From Location" value="{{ old('from_location', $trips->from_location) }}">
                    @error('from_location')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="to_location">To Location</label>
                    <input type="text" name="to_location" class="form-control @error('to_location') is-invalid @enderror"
                           id="to_location"
                           placeholder="To Location" value="{{ old('to_location', $trips->to_location) }}">
                    @error('to_location')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="meetup_location">Meetup Location</label>
                    <input type="text" name="meetup_location" class="form-control @error('meetup_location') is-invalid @enderror"
                           id="meetup_location"
                           placeholder="Meetup Location" value="{{ old('meetup_location', $trips->meetup_location) }}">
                    @error('meetup_location')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="date">From Date</label>
                    <input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror" id="mobile"
                           placeholder="From Date" value="{{ old('from_date', $trips->from_date) }}">
                    @error('from_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="date">To Date</label>
                    <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror" id="mobile"
                           placeholder="To Date" value="{{ old('to_date', $trips->to_date) }}">
                    @error('to_date')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name_of_your_trip">Name Of Your Trip</label>
                    <input type="text" name="name_of_your_trip" class="form-control @error('name_of_your_trip') is-invalid @enderror"
                           id="name_of_your_trip"
                           placeholder="Name Of Your Trip" value="{{ old('name_of_your_trip', $trips->name_of_your_trip) }}">
                    @error('name_of_your_trip')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="description_of_your_trip">Description Of Your Trip</label>
                    <textarea name="description_of_your_trip" class="form-control @error('description_of_your_trip') is-invalid @enderror"
                            id="description_of_your_trip" rows="3" placeholder="Description Of Your Trip">{{ old('description_of_your_trip', $trips->description_of_your_trip) }}</textarea>
                    @error('description_of_your_trip')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <button class="btn btn-success btn-block btn-lg" type="submit">Save Changes</button>
            </form>
        </div>
    </div>
    <div id="userListModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close" onclick="toggleUserListModal()">&times;</span>
        <h2>User List</h2>
        <!-- Search input -->
        <input type="text" id="searchInput" oninput="searchUsers()" placeholder="Search...">
        <div class="table-responsive">
            <table class="table table-bordered" id="userTable">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="selected_user_id" value="{{ $user->id }}" onclick="selectUser(this)" {{ $user->id == $trips->user_id ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->mobile }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    @endforeach
                </tbody>
              
                </table>
            </div>
           <!-- Pagination -->
<nav aria-label="User List Pagination">
    <ul class="pagination justify-content-center">
        <!-- Previous button -->
        <li class="page-item">
            <button class="page-link" onclick="prevPage()" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </button>
        </li>
        
        <!-- Next button -->
        <li class="page-item">
            <button class="page-link" onclick="nextPage()" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </button>
        </li>
    </ul>
</nav>

        </div>
    </div>
</div>

@endsection
@section('js')
    <!-- Include any additional JavaScript if needed -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Define variables for pagination
        var currentPage = 1;
        var itemsPerPage = 10; // Change this value as needed
        var userListRows = $('#userTable tbody tr');

        // Function to toggle the user list modal
        function toggleUserListModal() {
            $('.modal').toggle(); // Toggle the modal
        }

        // Function to filter user list based on search input
        function searchUsers() {
            var searchText = $('#searchInput').val().toLowerCase();
            $('#userTable tbody tr').each(function() {
                var id = $(this).find('td:eq(1)').text().toLowerCase();
                var name = $(this).find('td:eq(2)').text().toLowerCase();
                var mobile = $(this).find('td:eq(3)').text().toLowerCase();
                var email = $(this).find('td:eq(4)').text().toLowerCase();
                if (id.includes(searchText) || name.includes(searchText) || mobile.includes(searchText) || email.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Function to handle checkbox click and update user_id input
        function selectUser(checkbox) {
    // Deselect all checkboxes
    $('input[name="selected_user_id"]').prop('checked', false);
    // Select only the clicked checkbox
    $(checkbox).prop('checked', true);
    // Set its value to the user_id input field
    $('#user_id').val(checkbox.value);
}
        // Function to show the specified page of users
        function showPage(page) {
            var startIndex = (page - 1) * itemsPerPage;
            var endIndex = startIndex + itemsPerPage;
            userListRows.hide().slice(startIndex, endIndex).show();
        }

        // Function to go to the previous page
        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        }

        // Function to go to the next page
        function nextPage() {
            if (currentPage < Math.ceil(userListRows.length / itemsPerPage)) {
                currentPage++;
                showPage(currentPage);
            }
        }

        // Show the first page initially
        showPage(currentPage);
    </script>
    @endsection