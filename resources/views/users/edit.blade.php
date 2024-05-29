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
                    <label for="unique_name">Unique Name</label>
                    <input type="text" name="unique_name" class="form-control @error('unique_name') is-invalid @enderror"
                           id="unique_name"
                           placeholder="Unique Name" value="{{ old('unique_name', $users->unique_name) }}">
                    @error('unique_name')
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
                    <label for="state">State</label>
                    <input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
                           id="state"
                           placeholder="state" value="{{ old('state', $users->state) }}">
                    @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
                           id="city"
                           placeholder="city" value="{{ old('city', $users->city) }}">
                    @error('city')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                        <label for="profession">Profession</label>
                        <select name="profession" class="form-control @error('profession') is-invalid @enderror" id="profession">
                            <option value=''>--select--</option>
                            <option value='engineer' {{ old('profession', $users->profession) == 'engineer' ? 'selected' : '' }}>engineer</option>
                            <option value='chef' {{ old('profession', $users->profession) == 'chef' ? 'selected' : '' }}>chef</option>
                            <option value='electrician' {{ old('profession', $users->profession) == 'electrician' ? 'selected' : '' }}>electrician</option>
                            <option value='physician' {{ old('profession', $users->profession) == 'physician' ? 'selected' : '' }}>physician</option>
                            <option value='dentist' {{ old('profession', $users->profession) == 'dentist' ? 'selected' : '' }}>dentist</option>
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
    <label for="profile">New Profile</label>
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

<div class="form-group">
    <label for="verified">Verified</label>
    <div class="custom-control custom-switch">
        <input type="hidden" name="verified" value="0"> <!-- Hidden input to ensure a value is always submitted -->
        <input type="checkbox" name="verified" class="custom-control-input @error('verified') is-invalid @enderror" id="verified" value="1" {{ old('verified', $users->verified) == '1' ? 'checked' : '' }}>
        <label class="custom-control-label" for="verified"></label>
    </div>
    @error('verified')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>

<div class="form-group">
    <label for="online_status">Online Status</label>
    <div class="custom-control custom-switch">
        <input type="hidden" name="online_status" value="0"> <!-- Hidden input to ensure a value is always submitted -->
        <input type="checkbox" name="online_status" class="custom-control-input @error('online_status') is-invalid @enderror" id="online_status" value="1" {{ old('online_status', $users->online_status) == '1' ? 'checked' : '' }}>
        <label class="custom-control-label" for="online_status"></label>
    </div>
    @error('online_status')
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
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('profile');
    const fileInputLabel = fileInput.nextElementSibling;

    fileInput.addEventListener('change', function () {
        const fileName = this.files[0].name;
        fileInputLabel.textContent = fileName;
    });
});
</script>
@endsection
