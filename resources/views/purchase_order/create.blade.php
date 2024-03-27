@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')

    <div class="container">
        <form class="row gx-5 gy-3" action="{{ route('purorder.store') }}" method="post">
            @csrf
            <div class="col-md-8">
                <label for="orderlist" class="form-label">Order List  <span style="color: red;">*</span></label>
                <textarea placeholder="Enter order list" class="form-control @error('orderlist') is-invalid @enderror" name="orderlist" rows="10" style="font-size: 20px;">{{ old('orderlist') }}</textarea>
                @error('orderlist')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>


            <!-- Adjusted the column layout for the "Date" input field -->
            <div class="col-md-4 ">
                <label for="date" class="form-label text-end">Date</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" name="date" value="{{ now()->format('Y-m-d') }}">
                @error('date')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            

            <div class="col-md-6">
                <label for="notes" class="form-label">Notes</label>
                <textarea placeholder="Enter notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>
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
