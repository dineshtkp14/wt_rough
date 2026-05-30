@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
    <div class="main-content">
        @yield('breadcrumb')

        <div class="container-fluid px-3 px-xl-4">
            <livewire:all-customer-credit-list-livewire />
        </div>

        @livewireScripts
    </div>
@stop
