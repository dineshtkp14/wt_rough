@extends('layouts.master')

@section('content')

<div class="main-content"> 
<Center><h1 class="text-danger mt-5 bold"><U>Add New Price List </U></h1></Center>
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

<form class="row gx-5 gy-3" action="{{ route('pricelists.store') }}" method="post">
                @csrf

               
           
            
           
           
            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Item Name</label>
                    <input type="text" class="form-control @error('itemname') is-invalid @enderror" 
                        name="itemname" value="{{ old('itemname') }}">
                    @error('itemname')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">$Cost Price</label>
                    <input type="text" class="form-control @error('costprice') is-invalid @enderror" 
                        name="costprice" value="{{ old('costprice') }}">
                    @error('costprice')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">$Sale Price</label>
                    <input type="text" class="form-control @error('saleprice') is-invalid @enderror" 
                        name="saleprice" value="{{ old('saleprice') }}">
                    @error('saleprice')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">WholeSale Price</label>
                    <input type="number" class="form-control @error('wholesaleprice') is-invalid @enderror" 
                        name="wholesaleprice" value="{{ old('wholesaleprice') }}">
                    @error('wholesaleprice')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

           

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Note</label>
                    <input type="text" class="form-control @error('note') is-invalid @enderror" 
                        name="note" value="{{ old('note') }}">
                    @error('note')
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

