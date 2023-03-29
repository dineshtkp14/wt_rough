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
			<th>Id</th>
			<th>Name</th>
			<th>Address</th>
			<th>Email</th>
			<th>Phoneno</th>
			<th>Bank Details</th>
            
            <th>Remarks</th>
            <th>Action</th>

			
		</tr>
	</thead>
	<tbody>
    @if ($all->isNotEmpty())
                        @foreach ($all as $i)
		<tr>
			<td data-label="Id">{{ $i->id }}</td>
			<td data-label="Name">{{ $i->name }}</td>
			<td data-label="Address">{{ $i->address}}</td>
			<td data-label="Contact No.">{{ $i->email }}</td>
			<td data-label="Amount">{{ $i->phoneno }}</td>
			<td data-label="Paisa">{{ $i->bank_accountno}}</td>
           
            <td data-label="Remarks">{{ $i->remarks }}</td>
			<td data-label="Remarks"><button class="btn btn-success">EDIT</button><button class="btn btn-danger">DELETE</button></td>
			
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