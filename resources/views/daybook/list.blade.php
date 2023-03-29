@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
@yield('breadcrumb')

<livewire:daybooklivewire/>


        @livewireScripts


</div>
@stop