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
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <div class="container">

        <form class="row gx-5 gy-3" action="{{ route('itemsales.update', $all->id) }}" method="post">
            @csrf  
            @method('put')

            <!-- ... existing form fields ... -->

             <!-- New fields for item -->
             <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Item ID</label>
                <input type="text" class="form-control @error('itemid') is-invalid @enderror" 
                    name="itemid" value="{{ old('itemid', $all->itemid) }}">
                @error('itemid')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Unstocked Name</label>
                <input type="text" class="form-control @error('unstockedname') is-invalid @enderror" 
                    name="unstockedname" value="{{ old('unstockedname', $all->unstockedname) }}">
                @error('unstockedname')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>


            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Quantity</label>
                <input type="text" class="form-control @error('quantity') is-invalid @enderror" 
                    name="quantity" value="{{ old('quantity', $all->quantity) }}">
                @error('quantity')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Price</label>
                <input type="text" class="form-control @error('price') is-invalid @enderror" 
                    name="price" value="{{ old('price', $all->price) }}">
                @error('price')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Discount</label>
                <input type="text" class="form-control @error('discount') is-invalid @enderror" 
                    name="discount" value="{{ old('discount', $all->discount) }}">
                @error('discount')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>


            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Subtotal</label>
                <input type="text" class="form-control @error('subtotal') is-invalid @enderror" 
                    name="subtotal" value="{{ old('subtotal', $all->subtotal) }}">
                @error('subtotal')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

       


            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea type="text" class="form-control @error('notes') is-invalid @enderror" 
                    name="notes">{{ old('notes', $all->notes) }}</textarea>
                @error('notes')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

           

            
        
            <!-- End of new fields for item -->

            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" class="btn btn-lg btn-primary">Save</button>
            </div>

        </form>
    </div>

</div>

@stop
