@extends('layouts.master')

@section('content')
<div class="main-content">
    <div class="container d-flex justify-content-center align-items-center" style="min-height:65vh;">
        <div class="card shadow-sm border-0" style="max-width:460px;width:100%;border-radius:16px;">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="fa-solid fa-lock" style="font-size:42px;color:#173f7a;"></i>
                    <h2 class="mt-3 mb-2" style="color:#173f7a;font-weight:800;">{{ $pageTitle ?? 'Credit List Protected' }}</h2>
                    <p class="text-muted mb-0">{{ $pageDescription ?? 'Enter the password to view the customer credit list.' }}</p>
                </div>

                <form method="POST" action="{{ $unlockRoute ?? route('customer.creditlist.unlock') }}">
                    @csrf
                    <label for="creditListPassword" class="form-label fw-bold">Password</label>
                    <input type="password" id="creditListPassword" name="password"
                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                        autocomplete="current-password" autofocus required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-4 fw-bold">
                        <i class="fa-solid fa-unlock me-2"></i>Unlock Credit List
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
