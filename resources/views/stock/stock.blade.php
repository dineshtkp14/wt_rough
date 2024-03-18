@extends('layouts.master')
@include('layouts.breadcrumb')


@section('content')
<div class="main-content"> 
        
@yield('breadcrumb')


                @if (Session::has('success'))
                        <div class="alert alert-success bg-success text-white w-50">
                        {{ Session::get('success') }}
                        </div>
                @endif

                
        @if (Session::has('error'))
                <div class="alert alert-danger w-50">
                {{ Session::get('error') }}
                </div>
        @endif


				<livewire:stock-livewire/>
				@livewireScripts
				

                              
</div>
@stop