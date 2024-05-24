@extends('layouts.admin')

@section('title', 'Update users')
@section('content-header', 'Update users')
@section('content-actions')
    <a href="{{ route('users.index') }}" class="btn btn-success"><i class="fas fa-back"></i> Back To Users</a>
@endsection

@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('users.update', $users) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <a href="{{ route('users.add_points', $users->id) }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Points</a>
                <div class="form-group">
                    <br>
                    <label for="name">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           placeholder="Name" value="{{ old('name', $users->name) }}">
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <input type="number" name="mobile" class="form-control @error('mobile') is-invalid @enderror" id="mobile"
                           placeholder="mobile" value="{{ old('mobile', $users->mobile) }}">
                    @error('mobile')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" id="email"
                           placeholder="Email" value="{{ old('email', $users->email) }}">
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" name="age" class="form-control @error('age') is-invalid @enderror"
                           id="age"
                           placeholder="age" value="{{ old('age', $users->age) }}">
                    @error('age')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <input type="text" name="gender" class="form-control @error('gender') is-invalid @enderror"
                           id="gender"
                           placeholder="gender" value="{{ old('gender', $users->gender) }}">
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
                            <option value='engineer' {{ old('profession', $users->profession) == 'engineer' ? 'selected' : '' }}>Engineer</option>
                            <option value='chef' {{ old('profession', $users->profession) == 'chef' ? 'selected' : '' }}>Chef</option>
                            <option value='electrician' {{ old('profession', $users->profession) == 'electrician' ? 'selected' : '' }}>Electrician</option>
                            <option value='physician' {{ old('profession', $users->profession) == 'physician' ? 'selected' : '' }}>Physician</option>
                            <option value='dentist' {{ old('profession', $users->profession) == 'dentist' ? 'selected' : '' }}>Dentist</option>
                        </select>
                        @error('profession')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                <div class="form-group">
                    <label for="referred_by">Referred By</label>
                    <input type="text" name="referred_by" class="form-control @error('referred_by') is-invalid @enderror"
                           id="referred_by"
                           placeholder="referred_by" value="{{ old('referred_by', $users->referred_by) }}">
                    @error('referred_by')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="refer_code">Refer Code</label>
                    <input type="text" name="refer_code" class="form-control @error('refer_code') is-invalid @enderror"
                           id="refer_code"
                           placeholder="refer_code" value="{{ old('refer_code', $users->refer_code) }}">
                    @error('refer_code')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
            <span>Current Profile:</span>
            <img src="{{ asset('storage/app/public/users/' . $users->profile) }}" alt="{{ $users->name }}" style="max-width: 100px; max-height: 100px;">
            <br>
            <label for="image">New Profile</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="profile" id="profile">
                    <label class="custom-file-label" for="profile">Choose file</label>
                    @if($users->profile)
                        <input type="hidden" name="existing_profile" value="{{ $users->profile }}">
                    @endif
                </div>
                @error('profile')
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
