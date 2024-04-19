@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container">

        <a href="{{ route('expenses.index') }}" class="btn btn-primary ms-5" style="background-color: #1100ff; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;">
            <i class="fas fa-calendar"></i> <!-- Font Awesome icon for calendar -->
            VIEW EXPENSES 
        </a>

        <form class="row gx-5 gy-3" action="{{ route('expenses.store') }}" method="post">
            @csrf
    
           
            
            <div class="col-md-6">
                <label for="particulars" class="form-label">Particulars  <span style="color: red;">*</span></label>
                <textarea  autocomplete="off" class="form-control @error('particulars') is-invalid @enderror" 
                           name="particulars" rows="4">{{ old('particulars') }}</textarea>
                @error('particulars')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-md-6 float-end">
                <label for="date" class="form-label">Date  <span style="color: red;">*</span></label>
                <input autocomplete="off" type="date" class="form-control @error('date') is-invalid @enderror" 
                       name="date" value="{{ old('date') ?: date('Y-m-d') }}">
                @error('date')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="billno" class="form-label">Bill Number  <span style="color: red;">*</span></label>
                <input autocomplete="off" type="text" class="form-control @error('billno') is-invalid @enderror" 
                    name="billno" value="{{ old('billno') }}">
                @error('billno')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="col-md-6">
                <label for="amount" class="form-label">Amount  <span style="color: red;">*</span></label>
                <input autocomplete="off" type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                    name="amount" value="{{ old('amount') }}">
                @error('amount')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
    
            <div class="col-md-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" 
                          name="notes" rows="4">{{ old('notes') }}</textarea>
                @error('notes')
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
