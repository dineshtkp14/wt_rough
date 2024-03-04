@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

@yield('breadcrumb')

<div class="container"  style="margin-top: -80px;">>

<form class="row gx-5 gy-3" action="{{ route('transfergoods.update', $all->id) }}" method="post">
    @csrf
    @method('PUT')            

    <div class="col-md-6"></div>
    <div class="col-md-6">
        <label for="date" class="form-label">Date</label>
        <input type="date" class="form-control @error('date') is-invalid @enderror" 
            name="date" value="{{ old('date', $all->date) }}">
        @error('date')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="itemid" class="form-label">Item Name</label>
        <input type="text" class="form-control @error('itemid') is-invalid @enderror" 
            name="itemid" value="{{ old('itemid', $all->itemid) }}">
        @error('itemid')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>

    
    <div class="col-md-6">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="text" class="form-control @error('quantity') is-invalid @enderror" 
            name="quantity" value="{{ old('quantity', $all->quantity) }}">
        @error('quantity')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>


    <div class="col-md-6">
        <label for="shiftArea" class="form-label">Shift Area</label>
        <input type="text" class="form-control @error('shiftArea') is-invalid @enderror" 
            name="shiftArea" value="{{ old('shiftArea', $all->shiftArea) }}">
        @error('shiftArea')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="shiftBy" class="form-label">Shift By</label>
        <input type="text" class="form-control @error('shiftBy') is-invalid @enderror" 
            name="shiftBy" value="{{ old('shiftBy', $all->shiftBy) }}">
        @error('shiftBy')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>

    <div class="col-md-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" 
            name="notes" rows="4">{{ old('notes', $all->notes) }}</textarea>
        @error('notes')
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
