
@extends('layouts.master')
@include('layouts.breadcrumb')


@section('content')
<div class="main-content"> 
    @yield('breadcrumb')




        <livewire:allcustomer-creditduelistlivewire/>


    @livewireScripts


</div>
@stop