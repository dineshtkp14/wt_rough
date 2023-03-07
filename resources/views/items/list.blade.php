@extends('layouts.master')

@section('content')

<h2 class="text-center mt-5">View Suppliers Details</h2>

<div class="container">
	<div class="row float-end">
<div class="col-12 float-end ">

        <input class="form-control  border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
    
</div>
</div>

<a href="/disinfos/create">Back</a>


<table>
	<thead>
		<tr>
			<th>Id</th>
			<th>Billno</th>
			<th>Distributors name</th>
			<th>date</th>
			<th>itemsname</th>
			<th>Quantity</th>
            
            <th>DLP</th>
            <th>MRP</th>
			<th>Total</th>
            <th>FinalTotal</th>
			 <th>Action</th>

			
		</tr>
	</thead>
	<tbody>
    @if ($all->isNotEmpty())
                        @foreach ($all as $i)
		<tr>
			<td data-label="Id">{{ $i->id }}</td>
			<td data-label="Name">{{ $i->billno }}</td>
			<td data-label="Address">{{ $i->distributorname}}</td>
			<td data-label="Contact No.">{{ $i->date }}</td>
			<td data-label="Amount">{{ $i->itemsname }}</td>
			<td data-label="Paisa">{{ $i->quantity}}</td>
           
            <td data-label="Remarks">{{ $i->dlp }}</td>
            <td data-label="Remarks">{{ $i->mrp }}</td>
            <td data-label="Remarks">{{ $i->total }}</td>
            <td data-label="Remarks">{{ $i->finaltotal }}</td>


			<td data-label="Remarks"><button class="btn btn-success">EDIT</button><button class="btn btn-danger">DELETE</button></td>
			
		</tr>
        @endforeach
		
    @else
     <h3>Database is Empty !! Plese Add to view List</h3>
		
     @endif
	</tbody>
</table>
</div>



@stop