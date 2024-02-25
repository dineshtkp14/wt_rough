@extends('layouts.master')
@include('layouts.breadcrumb')


@section('content')
<div class="main-content"> 
        
@yield('breadcrumb')


               


				<livewire:trackcreditnotes-livewire/>
				@livewireScripts
				

                              
</div>
@stop