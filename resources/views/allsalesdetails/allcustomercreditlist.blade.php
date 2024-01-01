
@extends('layouts.master')
@include('layouts.breadcrumb')


@section('content')
<div class="main-content"> 
    @yield('breadcrumb')



    {{-- <livewire:allsalesdetailslivewire/> --}}

        <livewire:allcustomercreditlistlivewire/>

    

    @livewireScripts


</div>
@stop