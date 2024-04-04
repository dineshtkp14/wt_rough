

@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

        @yield('breadcrumb')

               

                

<livewire:customer-paymenthistrylivewire/>

@livewireScripts

</div>

</div>

@stop