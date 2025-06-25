@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

@yield('breadcrumb')


<div class="container">

<form class="row gx-5 gy-3" action="{{route('customerinfos.update',$cus->id)}}" method="post">
                @csrf
                @method('put')            

          <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                        name="name" placeholder="Enter Name" value="{{ old('name',$cus->name) }}">
                    @error('name')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Address</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" 
                        name="address" value="{{ old('address',$cus->address) }}">
                    @error('address')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Email</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" 
                        name="email" value="{{ old('email',$cus->email) }}">
                    @error('email')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">PhoneNo</label>
                    <input type="text" class="form-control @error('phoneno') is-invalid @enderror" 
                        name="phoneno" value="{{ old('phoneno',$cus->phoneno) }}">
                    @error('phoneno')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Alternate PhoneNo</label>
                <input type="text" class="form-control @error('alternate_phoneno') is-invalid @enderror" 
                    name="alternate_phoneno" value="{{ old('alternate_phoneno',$cus->alternate_phoneno) }}">
                @error('alternate_phoneno')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

           

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Remarks</label>
                    <textarea type="text" class="form-control @error('remarks') is-invalid @enderror" 
                        name="remarks" value="{{ old('remarks',$cus->remarks) }}" rows="4" cols="50">{{ old('remarks',$cus->remarks) }}</textarea>
                    @error('remarks')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                    <button type="submit" class="btn btn-lg btn-primary">Update</button>
            </div>
</form>
</div>

</div>

@stop

