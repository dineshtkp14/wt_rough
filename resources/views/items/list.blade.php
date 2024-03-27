@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

        @yield('breadcrumb')

                @if (Session::has('success'))
                        <div class=" bg-success  text-white alert alert-success w-50">
                        {{ Session::get('success') }}
                        </div>
                @endif

<livewire:itemslivewire/>

@livewireScripts

</div>

</div>

@stop