@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
    <div class="main-content stock-content">
        @yield('breadcrumb')

        @if (Session::has('success'))
            <div class="stock-alert stock-alert-success">
                <i class="fas fa-check-circle"></i>
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('error'))
            <div class="stock-alert stock-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ Session::get('error') }}
            </div>
        @endif

        <livewire:stock-livewire />
        @livewireScripts
    </div>
@stop
