@extends('layouts.master')

@section('content')


<h2 class="bg-warning"> Add items</h2>
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

<form class="row gx-5 gy-3" action="{{ route('items.store') }}" method="post">
                @csrf

               
           
            
           
           
                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Bill No</label>
                    <input type="text" class="form-control @error('billno') is-invalid @enderror" 
                        name="billno" value="{{ old('billno') }}">
                    @error('billno')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Distributor Name</label>
                    <input type="text" class="form-control @error('disname') is-invalid @enderror" 
                        name="disname" value="{{ old('disname') }}">
                    @error('disname')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Date</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                        name="date" value="{{now()->format('Y-m-d')}}" id="">
                    @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
           

            <h2 class="">--------------  + -----</h2>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">ITEMS Name</label>
                    <input type="text" class="form-control @error('itemsname') is-invalid @enderror" 
                        name="itemsname" value="{{ old('itemsname') }}">
                    @error('email')
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
                    <label for="inputPassword4" class="form-label">DLP</label>
                    <input type="bank_accountno" class="form-control @error('dlp') is-invalid @enderror" 
                        name="dlp" value="{{ old('dlp') }}">
                    @error('dlp')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">MRP</label>
                    <input type="text" class="form-control @error('mrp') is-invalid @enderror" 
                        name="mrp" value="{{ old('mrp') }}">
                    @error('mrp')
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
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Final Total</label>
                    <input type="text" class="form-control @error('finaltotal') is-invalid @enderror" 
                        name="finaltotal" value="{{ old('finaltotal') }}">
                    @error('remarks')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                    <button type="submit" class="btn btn-lg btn-primary">Save</button>
            </div>
</form>
</div>



@stop

