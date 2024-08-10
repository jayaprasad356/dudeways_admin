@extends('layouts.admin')

@section('title', 'Update Verifications')
@section('content-header', 'Update Verifications')
@section('content-actions')
    <a href="{{route('verifications.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Verifications</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('verifications.update', $verifications) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="user_id">User Name</label>
                    <input type="text" name="user_id" class="form-control @error('user_id') is-invalid @enderror"
                           id="user_id"
                           placeholder="User name" value="{{ $users->firstWhere('id', $verifications->user_id)->name ?? 'No user selected' }}" readonly>
                    @error('user_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

                <div class="form-group">
                    <label for="plan_id">Plan Name</label>
                    <input type="text" name="plan_id" class="form-control @error('plan_id') is-invalid @enderror"
                           id="plan_id"
                           placeholder="Plan name" value="{{ $plans->firstWhere('id', $verifications->plan_id)->plan_name ?? 'No plan selected' }}" readonly>
                    @error('plan_id')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror 
                </div>

                <div class="form-group">
                        <span>Selfie Image:</span>
                        <img src="{{ asset('storage/app/public/verification/' . $verifications->selfie_image) }}" alt="{{ $verifications->name }}" style="max-width: 100px; max-height: 100px;">
                        <br>
                        @error('selfie_image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <span>Back Image:</span>
                        <img src="{{ asset('storage/app/public/verification/' . $verifications->back_image) }}" alt="{{ $verifications->name }}" style="max-width: 100px; max-height: 100px;">
                        <br>
                        @error('back_image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <span>Front Image:</span>
                        <img src="{{ asset('storage/app/public/verification/' . $verifications->front_image) }}" alt="{{ $verifications->name }}" style="max-width: 100px; max-height: 100px;">
                        <br>
                        @error('front_image')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
    <label for="status">Status</label>
    <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
        <label class="btn btn-outline-success {{ old('status', $verifications->status) === 1 ? 'active' : '' }}">
            <input type="radio" name="status" id="status_activated" value="1" {{ old('status', $verifications->status) === 1 ? 'checked' : '' }}> Approved
        </label>
        <label class="btn btn-outline-primary {{ old('status', $verifications->status) === 0 ? 'active' : '' }}">
            <input type="radio" name="status" id="status_pending" value="0" {{ old('status', $verifications->status) === 0 ? 'checked' : '' }}> Pending
        </label>
        <label class="btn btn-outline-danger {{ old('status', $verifications->status) === 2 ? 'active' : '' }}">
            <input type="radio" name="status" id="status_cancelled" value="2" {{ old('status', $verifications->status) === 2 ? 'checked' : '' }}> Cancelled
        </label>
    </div>
    @error('status')
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
    @enderror
</div>

<div class="form-group">
    <label for="payment_status">Payment Status</label>
    <div class="btn-group btn-group-toggle d-block" data-toggle="buttons">
        <label class="btn btn-outline-success {{ old('payment_status', $verifications->payment_status) === 1 ? 'active' : '' }}">
            <input type="radio" name="payment_status" id="payment_status_activated" value="1" {{ old('payment_status', $verifications->payment_status) === 1 ? 'checked' : '' }}> Paid
        </label>
        <label class="btn btn-outline-primary {{ old('payment_status', $verifications->payment_status) === 0 ? 'active' : '' }}">
            <input type="radio" name="payment_status" id="payment_status_pending" value="0" {{ old('payment_status', $verifications->payment_status) === 0 ? 'checked' : '' }}> Pending
        </label>
        <label class="btn btn-outline-danger {{ old('payment_status', $verifications->payment_status) === 2 ? 'active' : '' }}">
            <input type="radio" name="payment_status" id="payment_status_cancelled" value="2" {{ old('payment_status', $verifications->payment_status) === 2 ? 'checked' : '' }}> Cancelled
        </label>
    </div>
    @error('payment_status')
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
