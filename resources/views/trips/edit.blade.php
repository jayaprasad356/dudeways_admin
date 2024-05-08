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
                    <label for="location">Location</label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                           id="name"
                           placeholder="location" value="{{ old('location', $trips->location) }}">
                    @error('location')
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
@endsection

@section('js')
    <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
