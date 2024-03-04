@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

@yield('breadcrumb')


<div class="container">

<form class="row gx-5 gy-3" action="{{ route('myfirm.update', $all->id) }}" method="post">
    @csrf
    @method('put')            

    <div class="col-md-6">
        <label for="firmname" class="form-label">Firm Name</label>
        <input type="text" class="form-control @error('firmname') is-invalid @enderror" 
            name="firmname" value="{{ old('firmname', $all->firm_name) }}">
        @error('firmname')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>
    
    <div class="col-md-6">
        <label for="nickname" class="form-label">Nick Name</label>
        <input type="text" class="form-control @error('nickname') is-invalid @enderror" 
            name="nickname" value="{{ old('nickname', $all->nick_name) }}">
        @error('nickname')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
    </div>

    <div class="col-md-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea type="text" class="form-control @error('notes') is-invalid @enderror" 
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
