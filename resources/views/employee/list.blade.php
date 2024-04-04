@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

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