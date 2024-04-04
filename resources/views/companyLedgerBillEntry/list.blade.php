@extends('layouts.master')
@include('layouts.breadcrumb')


@section('content')
<div class="main-content"> 
@yield('breadcrumb')


              

				<livewire:bill-entrylivewire/>

				@livewireScripts
				


</div>
@stop