@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
   
@yield('breadcrumb')

<div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
    <div class="card-body">
        <h5 class="card-title">Company Info</h5>
        <p>
            <span>ID: </span><span id="customerId">...</span>
        </p>
        <p class="card-text">
            <span>Name: </span><span id="customerName">...</span>
        </p>
        <p>
            <span>Addres: </span><span id="customerAddress">...</span>
        </p>
        <p>
            <span>E-mail: </span><span id="customerEmail">...</span>
        </p>
        <p>
            <span>PhoneNo: </span><span id="customerPhone">...</span>
        </p>
    </div>

    <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
        <i class="fas fa-user"></i>
    </div>
</div>


<div class="container mt-5">
    @if (Session::has('success'))
    <div class="alert bg-success text-white w-50">
        {{ Session::get('success') }}
    </div>
    @endif
</div>

<div class="container" style="margin-top: -80px;">
    <form class="row gx-5 gy-3 " action="{{ route('transfergoods.store') }}" method="post">
        @csrf
        <div class="col-md-6 ">
            <a href="{{ route('transfergoods.index') }}" class="btn btn-primary">
                <i class="fas fa-list"></i> View List
            </a>
            
                    </div>

        <div class="col-md-6 float-end">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                name="date" value="{{ now()->format('Y-m-d') }}">
            @error('date')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="itemid" class="form-label">Item ID</label>
            <input type="text" class="form-control @error('itemid') is-invalid @enderror" 
                name="itemid" value="{{ old('itemid') }}" autocomplete="off">
            @error('itemid')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="text" class="form-control @error('quantity') is-invalid @enderror" 
                name="quantity" value="{{ old('quantity') }}">
            @error('quantity')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        

        <div class="col-md-6">
            <label for="shiftArea" class="form-label">Shift Area</label>
            <input type="text" class="form-control @error('shiftArea') is-invalid @enderror" 
                name="shiftArea" value="{{ old('shiftArea') }}">
            @error('shiftArea')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="shiftBy" class="form-label">Shift By</label>
            <input type="text" class="form-control @error('shiftBy') is-invalid @enderror" 
                name="shiftBy" value="{{ old('shiftBy') }}">
            @error('shiftBy')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-12">
            <label for="notes" class="form-label">Notes</label>
            <textarea type="text" class="form-control @error('notes') is-invalid @enderror" 
                name="notes">{{ old('notes') }}</textarea>
            @error('notes')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <div class="d-grid gap-2 pt-2 pb-4">
            <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Save</button>
        </div>
    </form>
</div>

</div>

<script>
    $(document).ready(function () {
        $('form').submit(function () {
            // Disable the submit button
            $('#submitBtn').prop('disabled', true);
        });
    });
</script>

@stop
