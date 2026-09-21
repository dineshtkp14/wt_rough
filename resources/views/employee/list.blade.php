@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
@section('page-css')
<style>
    .employee-full-page {
        width: 100%;
        max-width: none;
    }

    .employee-full-page > .container {
        width: 100%;
        max-width: none;
        padding-left: 12px;
        padding-right: 12px;
    }

    .employee-full-page .card {
        width: 100%;
    }

    .employee-full-page .card-body {
        overflow-x: auto;
    }

    .employee-full-page table {
        width: 100%;
        min-width: 1050px;
    }
</style>
@endsection

<div class="main-content employee-full-page">

        @yield('breadcrumb')

        @if (auth()->check() && auth()->user()->email !== 'dineshtkp14@gmail.com')
             <script> window.location.href = "{{ route('login') }}";   </script>
        @endif
                @if (Session::has('success'))
                        <div class="alert alert-success w-50">
                        {{ Session::get('success') }}
                        </div>
                @endif

<livewire:employeelivewire/>

@livewireScripts

</div>

</div>

@stop
