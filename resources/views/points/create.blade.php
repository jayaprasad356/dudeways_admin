@extends('layouts.admin')

@section('title', 'Create points')
@section('content-header', 'Create points')
@section('content-actions')
    <a href="{{route('points.index')}}" class="btn btn-success"><i class="fas fa-back"></i>Back To Points</a>
@endsection
@section('content')

    <div class="card">
        <div class="card-body">

            <form action="{{ route('points.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="points">Points</label>
                    <input type="number" name="points" class="form-control @error('points') is-invalid @enderror"
                           id="points"
                           placeholder="points" value="{{ old('points') }}">
                    @error('points')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="discount_points">Discount Points</label>
                    <input type="number" name="discount_points" class="form-control @error('discount_points') is-invalid @enderror" id="mobile"
                           placeholder="Discount Points" value="{{ old('discount_points') }}">
                    @error('discount_points')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            
                <div class="form-group">
                    <label for="offer_percent">Offer Percent</label>
                    <input type="number" name="offer_percent" class="form-control @error('offer_percent') is-invalid @enderror" id="mobile"
                           placeholder="Offer Percent" value="{{ old('offer_percent') }}">
                    @error('offer_percent')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                           id="price"
                           placeholder="price" value="{{ old('price') }}">
                    @error('price')
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
