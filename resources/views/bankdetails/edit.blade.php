@extends('layouts.admin')

@section('title', 'Update Bank Details')
@section('content-header', 'Update Bank Details')



@section('content')
<div class="card">
    <div class="card-body">
      

    <form action="{{ route('bankdetails.update', $bankdetails) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

             <div class="form-group">
                <label for="user_id">User ID</label>
                <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $bankdetails->user_id }}" readonly>
            </div>

            <div class="form-group">
                <label for="account_holder_name">Account Holder Name</label>
                <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" value="{{ $bankdetails->account_holder_name }}" required>
            </div>

            <div class="form-group">
                <label for="account_number">Account Number</label>
                <input type="number" class="form-control" id="account_number" name="account_number" value="{{ $bankdetails->account_number }}" required>
            </div>

            <div class="form-group">
                <label for="ifsc_code">IFSC Code</label>
                <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ $bankdetails->ifsc_code }}" required>
            </div>

            <div class="form-group">
                <label for="bank_name">Bank Name</label>
                <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ $bankdetails->bank_name }}" required>
            </div>

            <div class="form-group">
                <label for="branch_name">Branch Name</label>
                <input type="text" class="form-control" id="branch_name" name="branch_name" value="{{ $bankdetails->branch_name }}" required>
            </div>
        

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
<script src="//cdn.ckeditor.com/4.21.0/full-all/ckeditor.js"></script>
<script>
    // Replace CKEditor for privacy_policy and terms_conditions textareas
    document.addEventListener('DOMContentLoaded', function () {
        CKEDITOR.replace('privacy_policy', {
            extraPlugins: 'colorbutton'
        });
        CKEDITOR.replace('terms_conditions', {
            extraPlugins: 'colorbutton'
        });
        CKEDITOR.replace('refund_policy', {
            extraPlugins: 'colorbutton'
        });
    });
</script>
@endsection
