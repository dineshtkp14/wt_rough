@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')


<div class="container">
    <div class="row">
        <div class="col-md-8"> </div>
        <div class="col-md-4">
            <a href="{{ route('items.create') }}" target="" rel="noopener noreferrer" class="btn btn-lg btn-primary mb-3">
                <i class="fas fa-plus"></i> Add New Items
            </a>
            <a href="{{ route('items.index') }}" target="" rel="noopener noreferrer" class="btn btn-lg btn-primary mb-3 float-end">
                <i class="fas fa-eye"></i> View Items
            </a>
        </div>
        
    
<form class="row gx-5 gy-3" action="{{ route('companys.store') }}" method="post">
                @csrf

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Suppliers / Company Name  <span style="color: red;">*</span></label>
                    <input  autocomplete="off" type="text" class="form-control @error('name') is-invalid @enderror" 
                        name="name" value="{{ old('name') }}">
                    @error('name')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Address  <span style="color: red;">*</span></label>
                    <input  autocomplete="off" type="text" class="form-control @error('address') is-invalid @enderror" 
                        name="address" value="{{ old('address') }}">
                    @error('address')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Email</label>
                    <input  autocomplete="off" type="email" class="form-control @error('email') is-invalid @enderror" 
                        name="email" value="{{ old('email') }}">
                    @error('email')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">PhoneNo  <span style="color: red;">*</span></label>
                    <input  autocomplete="off" type="text" class="form-control @error('phoneno') is-invalid @enderror" 
                        name="phoneno" value="{{ old('phoneno') }}">
                    @error('phoneno')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

           

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Notes</label>
                    <textarea   autocomplete="off" placeholder="enter notes"  class="form-control @error('notes') is-invalid @enderror" name="notes" value="{{ old('notes') }}"  rows="3"></textarea>
                       
                    @error('notes')
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

