@extends('layouts.admin')

@section('title', 'Update Settings')
@section('content-header', 'Update Settings')

@section('content')
<div class="card">
    <div class="card-body">
  
        <form action="{{ route('news.update') }}" method="post">
            @method('PUT')
            @csrf

            <div class="form-group">
                <label for="telegram">Telegram</label>
                <input type="text" name="telegram" class="form-control @error('telegram') is-invalid @enderror" id="telegram" placeholder="telegram" value="{{ old('telegram', $news->telegram) }}">
                @error('telegram')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="whatsapp">Whatsapp</label>
                <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp" placeholder="whatsapp" value="{{ old('whatsapp', $news->whatsapp) }}">
                @error('whatsapp')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <button type="submit" class="btn btn-success btn-block btn-lg"><i class="fas fa-check"></i> Submit Changes</button>
        </form>
    </div>
</div>
@endsection
