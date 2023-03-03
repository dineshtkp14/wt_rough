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

               
           
            
           
           
            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> CustomerID</label>
                    <input type="text" class="form-control @error('customerid') is-invalid @enderror" 
                        name="customerid" value="{{ old('customerid') }}">
                    @error('customerid')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <<button type="">+</button>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">ITEMID</label>
                    <input type="text" class="form-control @error('itemid') is-invalid @enderror" 
                        name="itemid" value="{{ old('itemid') }}">
                    @error('itemid')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">UnstockedNAme</label>
                    <input type="unstockedname" class="form-control @error('unstockedname') is-invalid @enderror" 
                        name="unstockedname" value="{{ old('unstockedname') }}">
                    @error('unstockedname')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Quantity</label>
                    <input type="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                        name="quantity" value="{{ old('quantity') }}">
                    @error('quantity')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

           

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Price</label>
                    <input type="text" class="form-control @error('price') is-invalid @enderror" 
                        name="price" value="{{ old('price') }}">
                    @error('price')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Discount</label>
                    <input type="text" class="form-control @error('discount') is-invalid @enderror" 
                        name="discount" value="{{ old('discount') }}">
                    @error('discount')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Sub-Total</label>
                    <input type="text" class="form-control @error('subtotal') is-invalid @enderror" 
                        name="subtotal" value="{{ old('subtotal') }}">
                    @error('remarks')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Sub-Totalf</label>
                    <input type="text" class="form-control @error('subtotalf') is-invalid @enderror" 
                        name="subtotalf" value="{{ old('subtotalf') }}">
                    @error('remarks')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Discountf</label>
                    <input type="text" class="form-control @error('discountf') is-invalid @enderror" 
                        name="discountf" value="{{ old('discountf') }}">
                    @error('discountf')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Total</label>
                    <input type="text" class="form-control @error('total') is-invalid @enderror" 
                        name="total" value="{{ old('total') }}">
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

