@extends('layouts.master')

@section('content')
<div class="main-content"> 

<Center><h1 class="text-danger mt-5 bold"><U>BANK DEPOSIT</U></h1></Center>
<div class="cl mt-5"></div>
<div class="container mt-5">
            @if (Session::has('success'))
                <div class="alert alert-success w-50">
                    {{ Session::get('success') }}
                </div>
            @endif
</div>

<div class="container">
<a href="/banks/">Back</a>


<form class="row gx-5 gy-3" action="{{ route('banks.store') }}" method="post">
                @csrf

                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Date</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                        name="date" value="{{now()->format('Y-m-d')}}" id="">
                    @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
           
           
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Amount</label>
                    <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                        name="amount" value="{{ old('amount') }}">
                    @error('amount')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Deposited By</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        name="name" value="{{ old('name') }}">
                    @error('name')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
           

           
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Remarks</label>
                    <input type="text" class="form-control @error('remarks') is-invalid @enderror" 
                        name="remarks" value="{{ old('remarks') }}">
                    @error('remarks')
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

