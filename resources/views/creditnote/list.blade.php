@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')

<div class="main-content"> 

    @yield('breadcrumb')

    <div class="container">
        @if (Session::has('success'))
        <div class="alert bg-success text-white w-50">
            {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <livewire:creditnotes-salesitems-livewire/>
    @livewireScripts
</div>

@stop
