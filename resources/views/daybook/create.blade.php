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


<form class="row gx-5 gy-3" action="{{ route('daybooks.store') }}" method="post">
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
                    <label for="inputPassword4" class="form-label">Paisa</label>
                    
                        <select name="modeofpay"  class="form-control @error('modeofpay') is-invalid @enderror">
                            <option value="jamma">Jamma</option>
                            <option value="sapati">Sapati</option>
                           
                        </select>
                    @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
           
           
            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        name="name" value="{{ old('name') }}">
                    @error('name')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Address</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                        name="address" value="{{ old('address') }}">
                    @error('address')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Contact No</label>
                    <input type="text" class="form-control @error('contactno') is-invalid @enderror" 
                        name="contactno" value="{{ old('contactno') }}">
                    @error('contactno')
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
                    <label for="inputPassword4" class="form-label">Remarks</label>
                    <textarea type="text" class="form-control @error('remarks') is-invalid @enderror" 
                        name="remarks" value="{{ old('remarks') }}"> </textarea>
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

