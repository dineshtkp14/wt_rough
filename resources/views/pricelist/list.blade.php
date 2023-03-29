@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
   

<div class="main-content"> 
        @yield('breadcrumb')

<livewire:pricelistliveware/>


        @livewireScripts
</div>
@stop