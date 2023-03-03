@extends('layouts.master')
@section('content')

    <h2 class="bg-warning"> Customer Details</h2>

    <div class="cl mt-5"></div>

    <div class="container mt-5">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <div class="container">
        <a href="/daybooks/">Back</a>
        <form class="row gx-5 gy-3" action="{{ route('itemsales.store') }}" method="post">
            @csrf
            <div class="col-md-12">
                <label for="inputPassword4" class="form-label"> CustomerID</label>
                <input type="text" class="form-control @error('customerid') is-invalid @enderror" name="customerid"
                    value="{{ old('customerid') }}">
                @error('customerid')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-md-12">
                <label for="inputPassword4" class="form-label">ITEMID</label>
                <input type="text" class="form-control @error('itemid') is-invalid @enderror" name="itemid"
                    value="{{ old('itemid') }}">
                @error('itemid')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            <div class="row gx-5 gy-3 form-wrapper" id="formWrapper"></div>
            <button class="btn btn-info" id="addFieldBtn">+</button>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Sub-Total</label>
                <input type="text" class="form-control @error('subtotalf') is-invalid @enderror" name="subtotalf"
                    value="{{ old('subtotalf') }}">
                @error('remarks')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Discount</label>
                <input type="text" class="form-control @error('discountf') is-invalid @enderror" name="discountf"
                    value="{{ old('discountf') }}">
                @error('discountf')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Total</label>
                <input type="text" class="form-control @error('total') is-invalid @enderror" name="total"
                    value="{{ old('total') }}">
                @error('total')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" class="btn btn-lg btn-primary">Save</button>
            </div>
        </form>
    </div>
@stop
