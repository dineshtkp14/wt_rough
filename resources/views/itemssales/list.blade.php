@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')

<div class="main-content"> 

@yield('breadcrumb')
<div class="container">
	<div class="row float-end">
<div class="col-12 float-end ">

        <input class="form-control  border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
    
</div>
</div>


<table>
	<thead>
		<tr>
		
			<th>Bill No</th>
			<th>Item Id</th>
			<th>Unstocked Name</th>
			<th>Quantity</th>
			
            
            <th>Price</th>
            <th>Discount</th>
			<th>Sub-Total</th>
			


			
		</tr>
	</thead>
	<tbody>
    @if ($all->isNotEmpty())
                        @foreach ($all as $i)
		<tr>
			<td data-label="Name">{{ $i->invoiceid }}</td>
			<td data-label="Address">{{ $i->itemid}}</td>
			<td data-label="Contact No.">{{ $i->unstockedname }}</td>
			<td data-label="Amount">{{ $i->quantity }}</td>
			
           
            <td data-label="Remarks">{{ $i->price }}</td>
            <td data-label="Remarks">{{ $i->discount }}</td>
            <td data-label="Remarks">{{ $i->subtotal }}</td>


			
		</tr>
        @endforeach
		
    @else
     <h3>Database is Empty !! Plese Add to view List</h3>
		
     @endif
	</tbody>
</table>
</div>


</div>
@stop