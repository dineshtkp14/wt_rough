@extends('layouts.master')
@include('layouts.breadcrumb')


@section('content')
<div class="main-content"> 
@yield('breadcrumb')


                @if (Session::has('success'))
                        <div class="bg-success alert alert-success text-white w-50">
                        {{ Session::get('success') }}
                        </div>
                @endif

				<livewire:chequedepositlivewire/>

				@livewireScripts
				


</div>
@stop