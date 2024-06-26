@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')
<div class="container">
            @if (Session::has('success'))
            <div class="alert bg-success text-white w-50">
                {{ Session::get('success') }}
                </div>
            @endif
</div>

<div class="container">
    <a href="{{ route('chequedeposit.index') }}" class="float-end me-5 btn btn-primary btn-block btn-super-duper-bigger" style="margin-top: -40px;"><i class="fa fa-money-check-alt"></i> View Bank Deposit</a>


<form class="row gx-5 gy-3" action="{{ route('banks.store') }}" method="post">
                @csrf

                
            <div class="col-md-6"></div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Date</label>
                <input autocomplete="off" type="date" class="form-control @error('date') is-invalid @enderror" 
                    name="date" value="{{now()->format('Y-m-d')}}" id="">
                @error('date')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
             </div>
           
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Amount</label>
                    <input autocomplete="off" type="number" class="form-control @error('amount') is-invalid @enderror" 
                        name="amount" value="{{ old('amount') }}">
                    @error('amount')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Deposited By</label>
                    <input autocomplete="off" type="text" class="form-control @error('name') is-invalid @enderror" 
                        name="name" value="{{ old('name') }}">
                    @error('name')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
           

           
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Note</label>
                    <textarea type="text" class="form-control @error('remarks') is-invalid @enderror" 
                        name="remarks" value="{{ old('remarks') }}"></textarea>
                    @error('remarks')
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

