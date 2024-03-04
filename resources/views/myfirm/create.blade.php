@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container">
        <a href="{{ url()->previous() }}" class="btn btn-primary mb-3">Back</a>
        <form class="row gx-5 gy-3" action="{{ route('myfirm.store') }}" method="post">
            @csrf

            <div class="col-md-6">
                <label for="firmname" class="form-label">Firm Name</label>
                <input type="text" class="form-control @error('firmname') is-invalid @enderror" 
                    name="firmname" value="{{ old('firmname') }}">
                @error('firmname')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="col-md-6">
                <label for="nickname" class="form-label">Nick Name</label>
                <input type="text" class="form-control @error('nickname') is-invalid @enderror" 
                    name="nickname" value="{{ old('nickname') }}">
                @error('nickname')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea type="text" class="form-control @error('notes') is-invalid @enderror" 
                    name="notes" rows="4" cols="50">{{ old('notes') }}</textarea>
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
