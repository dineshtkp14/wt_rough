@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 
   
@yield('breadcrumb')
<div class="container">
    <form class="row gx-5 gy-3" action="{{route('purorder.update',$purchaseOrder->id)}}" method="post">
        @csrf  
                    @method('put')

        <div class="col-md-6">
            <label for="date" class="form-label">Date  <span style="color: red;">*</span></label>
            <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ old('date',$purchaseOrder->date) }}">
            @error('date')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-8">
            <label for="orderlist" class="form-label">Order List  <span style="color: red;">*</span></label>
            <textarea placeholder="Enter notes" class="form-control @error('orderlist') is-invalid @enderror" name="orderlist" rows="6">{{ old('orderlist', $purchaseOrder->notes) }}</textarea>

            @error('orderlist')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <div class="col-md-6">
            <label for="notes" class="form-label">Notes</label>
            <textarea placeholder="Enter notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $purchaseOrder->notes) }}</textarea>
            @error('notes')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>

        <!-- Existing form fields -->

        <div class="d-grid gap-2 pt-2 pb-4">
            <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Save</button>
        </div>
    </form>

    </div>

</div>
@stop
