@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container">
        <a href="{{ route('itemsales.create') }}" class="btn btn-primary float-end me-5" style="margin-top: -100px; background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>
        <a href="{{ route('customerinfos.index') }}" class="btn btn-primary float-end " style="margin-top: -100px;margin-right:300px; background-color: #ef8411; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-eye"></i>  VIEW CUSTOMER NAMES</a>
        
        <form class="row gx-5 gy-3" action="{{ route('customerinfos.store') }}" method="post">
            @csrf

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label"> Name  <span style="color: red;">*</span></label>
                <input type="text" placeholder="Enter Name" class="form-control @error('name') is-invalid @enderror" 
                    name="name" value="{{ old('name') }}">
                @error('name')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Address <span style="color: red;">*</span></label>
                <input type="text" placeholder="Enter Address" class="form-control @error('address') is-invalid @enderror" 
                    name="address" value="{{ old('address') }}">
                @error('address')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                    name="email" placeholder="Enter Email" value="{{ old('email') }}">
                @error('email')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">PhoneNo (main) <span style="color: red;">*</span></label>
                <input type="text" placeholder="Enter Phone No" class="form-control @error('phoneno') is-invalid @enderror" 
                    name="phoneno" value="{{ old('phoneno') }}">
                @error('phoneno')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Alternate PhoneNo</label>
                <input type="text" placeholder="Enter Alternate Phone No" class="form-control @error('alternate_phoneno') is-invalid @enderror" 
                    name="alternate_phoneno" value="{{ old('alternate_phoneno') }}">
                @error('alternate_phoneno')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

          
            <div class="col-md-6">
                <label class="form-label">
                    Customer Type <span style="color: red;">*</span>
                </label>
                <select name="type" class="form-control @error('type') is-invalid @enderror">
                    <option value="">-- Select Type --</option>
                    <option value="shop" {{ old('type') == 'shop' ? 'selected' : '' }}>Shop</option>
                    <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>Customer</option>
                </select>
            
                @error('type')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea type="text" class="form-control @error('remarks') is-invalid @enderror" 
                    name="remarks" placeholder="Enter Notes" value="{{ old('remarks') }}" rows="4" cols="50"> </textarea>
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
