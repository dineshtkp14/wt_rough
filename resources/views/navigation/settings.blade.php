@extends('layouts.master')

@section('content')
<div class="main-content">
    <div class="container-fluid py-4">
        <h1 class="fw-bold text-primary">ON/OFF NAV BAR</h1>
        <p class="text-muted">Choose which optional pages appear in your side navigation. This does not delete data or block direct page access.</p>
        @if(session('success'))
            <div class="alert alert-success fw-bold">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('navigation.settings.save') }}" class="card shadow-sm p-4" style="max-width:900px;">
            @csrf
            @foreach($options as $key => $label)
                <label class="d-flex align-items-center justify-content-between border rounded p-3 mb-3">
                    <span class="fw-bold fs-5">{{ $label }}</span>
                    <input type="hidden" name="nav[{{ $key }}]" value="0">
                    <input type="checkbox" name="nav[{{ $key }}]" value="1" @checked($visibility[$key] ?? true) style="width:24px;height:24px;">
                </label>
            @endforeach
            <button type="submit" class="btn btn-primary btn-lg fw-bold">SAVE NAV BAR SETTINGS</button>
        </form>
    </div>
</div>
@endsection
