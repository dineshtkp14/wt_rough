@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

    @yield('breadcrumb')

    <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
        <!-- ... existing card content ... -->
    </div>

    <div class="container mt-5">
        @if (Session::has('success'))
        <div class="alert bg-success text-white w-50">
            {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <div class="container">

        <form class="row gx-5 gy-3" action="{{ route('invoice.update', $invoice->id) }}" method="post">
            @csrf  
            @method('put')

            <!-- ... existing form fields ... -->

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Subtotal</label>
                <input type="text" class="form-control @error('subtotal') is-invalid @enderror" 
                    name="subtotal" value="{{ old('subtotal', $invoice->subtotal) }}">
                @error('subtotal')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Discount</label>
                <input type="text" class="form-control @error('discount') is-invalid @enderror" 
                    name="discount" value="{{ old('discount', $invoice->discount) }}">
                @error('discount')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Total</label>
                <input type="text" class="form-control @error('total') is-invalid @enderror" 
                    name="total" value="{{ old('total', $invoice->total) }}">
                @error('total')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea type="text" class="form-control @error('notes') is-invalid @enderror" 
                    name="notes">{{ old('notes', $invoice->notes) }}</textarea>
                @error('notes')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" class="btn btn-lg btn-primary">Save</button>
            </div>

        </form>
    </div>

</div>

@stop
