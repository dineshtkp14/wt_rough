@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')

<div class="main-content"> 

    @yield('breadcrumb')

    <div class="container">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <livewire:item-saleslivewire/>
    @livewireScripts
</div>

@stop
