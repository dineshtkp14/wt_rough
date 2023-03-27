@extends('layouts.master')

@section('content')


<Center><h1 class="text-danger mt-5 bold"><U>Customer Ledger Payments </U></h1></Center>
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

<form class="row gx-5 gy-3" action="{{ route('cpayments.store') }}" method="post">
                @csrf

               
           
            <input type="date" name="date" class="form-control">
           
                <select name="customerid" class="form-select" aria-label="Default select example">
                    <option selected>Select Customer</option>
                    @foreach ($all as $i)
                    
                    <option value="{{$i->id}}"> {{$i->name}}</option>
                    @endforeach
                    
                  </select>
            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Particulars</label>
                    <input type="text" class="form-control @error('particulars') is-invalid @enderror" 
                        name="particulars" value="{{ old('particulars') }}">
                    @error('particulars')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Voucher Type</label>
                    <input type="text" class="form-control @error('vt') is-invalid @enderror" 
                        name="vt" value="{{ old('vt') }}">
                    @error('vt')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Amount</label>
                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                        name="credit" value="{{ old('amount') }}">
                    @error('amount')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

           

           

            <div class="d-grid gap-2 pt-2 pb-4">
                    <button type="submit" class="btn btn-lg btn-primary">Save</button>
            </div>
</form>
</div>



@stop

