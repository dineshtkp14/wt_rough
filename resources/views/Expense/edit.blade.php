@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container">
        <a href="{{ url()->previous() }}" class="btn btn-primary mb-3">Back</a>
        <form class="row gx-5 gy-3" action="{{ route('expenses.update', $expense->id) }}" method="post">
            @csrf
            @method('PUT')

            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" 
                    name="date" value="{{ old('date', $expense->date) }}">
                @error('date')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label for="particulars" class="form-label">Particulars</label>
                <input type="text" class="form-control @error('particulars') is-invalid @enderror" 
                    name="particulars" value="{{ old('particulars', $expense->particulars) }}">
                @error('particulars')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="billno" class="form-label">Bill Number</label>
                <input type="text" class="form-control @error('billno') is-invalid @enderror" 
                    name="billno" value="{{ old('billno', $expense->billno) }}">
                @error('billno')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" 
                    name="amount" value="{{ old('amount', $expense->amount) }}">
                @error('amount')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea type="text" class="form-control @error('notes') is-invalid @enderror" 
                    name="notes" rows="4">{{ old('notes', $expense->notes) }}</textarea>
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
