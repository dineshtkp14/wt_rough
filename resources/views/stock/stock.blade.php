@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 



<div class="container">
@yield('breadcrumb')
<table class="table">
    <thead>
         <tr>
             <th >Id </th>
             <th >Date</th>
             <th>Distributor Name</th>
             <th>Items Name</th>

             <th >Quantity</th>
             <th>Action</th>

         </tr>
    </thead>
    <tbody>
         @if ($all->count())
              @foreach ($all as $i)
                   <tr>
                      <td>{{ $i->id }}</td>
                      <td>{{ $i->date }}</td>
                       <td>{{ $i->distributorname }}</td>
                       <td>{{ $i->itemsname }}</td>
                      
                       <td>{{ $i->quantity }}</td>
                     
                       <td>
                        @if ($i->quantity <= 5 and $i->quantity >= 1)
                        <div class="span-box">
                            <span class="btn btn-warning ">Warning</span>
                        </div>
                    @elseif($i->quantity == 0)
                        <div class="span-box">
                            <span class="btn btn-danger  ">Out Of Stock</span>
                        </div>
                    @else
                        <div class="span-box">
                            <span class="btn btn-success "> Available</span>
                        </div>
                    @endif
                       </td>
                       
                   </tr>
              @endforeach
         @else
              <tr>
                   <td colspan="5">No record found</td>
              </tr>
         @endif
    </tbody>
</table>


</div>

</div>

@stop

