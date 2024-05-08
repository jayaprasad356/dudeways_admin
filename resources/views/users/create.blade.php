@extends('layouts.admin')

@section('title', 'Create users')
@section('content-header', 'Create users')
@section('content-actions')
    <a href="{{route('users.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Users</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="name" value="{{ old('name') }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <input type="number" name="mobile" class="form-control @error('mobile') is-invalid @enderror" id="mobile"
                           placeholder="mobile Number" value="{{ old('mobile') }}">
                    @error('mobile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email"
                           placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="text" name="age" class="form-control @error('age') is-invalid @enderror" id="age"
                           placeholder="age" value="{{ old('age') }}">
                    @error('age')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                 </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <input type="text" name="gender" class="form-control @error('gender') is-invalid @enderror" id="gender"
                           placeholder="gender" value="{{ old('gender') }}">
                    @error('gender')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="profession">Profession</label>
                    <select name="profession" class="form-control @error('profession') is-invalid @enderror" id="profession">
                        <option value=''>--select--</option>
                        <option value='engineer' {{ old('profession') == 'engineer' ? 'selected' : '' }}>engineer</option>
                        <option value='chef' {{ old('profession') == 'chef' ? 'selected' : '' }}>chef</option>
                        <option value='electrician' {{ old('profession') == 'electrician' ? 'selected' : '' }}>electrician</option>
                        <option value='physician' {{ old('profession') == 'physician' ? 'selected' : '' }}>physician</option>
                        <option value='dentist' {{ old('profession') == 'dentist' ? 'selected' : '' }}>dentist</option>
                    </select>
                    @error('profession')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
    <label for="referred_by">Referred By</label>
    <input type="text" name="referred_by" class="form-control @error('referred_by') is-invalid @enderror" id="referred_by"
           placeholder="referred_by" value="{{ old('referred_by') }}">
    @error('referred_by')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>


                 <div class="form-group">
                    <label for="profile">Profile</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="profile" id="profile" onchange="updateProfileLabel(this)">
                        <label class="custom-file-label" for="profile" id="profile-label">Choose File</label>
                    </div>
                    @error('profile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

              

                <button class="btn btn-success btn-block btn-lg" type="submit">Submit</button>
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

        function updateProfileLabel(input) {
            var fileName = input.files[0].name;
            var label = $(input).siblings('.custom-file-label');
            label.text(fileName);
        }
    </script>
@endsection
