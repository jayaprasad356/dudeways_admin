@extends('layouts.admin')

@section('title', 'Update Settings')
@section('content-header', 'Update Settings')
@section('content')
<div class="card">
    <div class="card-body">
       
        <form action="{{ route('news.update', $news->id) }}" method="POST">
            @csrf
            @method('POST')
            <div class="form-group">
                <label for="telegram">Telegram</label>
                <input type="text" class="form-control" id="telegram" name="telegram" value="{{ $news->telegram }}" required>
            </div>
            <div class="form-group">
                <label for="instagram">Instagram</label>
                <input type="text" class="form-control" id="instagram" name="instagram" value="{{ $news->instagram }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
