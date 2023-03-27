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


<table id="simple_table">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>Address</th>
			<th>Email</th>
			<th>Phoneno</th>
			
            
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
			
           
            <td data-label="Remarks">{{ $i->remarks }}</td>
			<td>
			<a href="{{Route('customerinfos.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>

         
<a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
<form id="eea{{$i->id}}" action="{{ route('customerinfos.destroy',$i->id)}}" method="post">
@csrf
@method('delete')

</form>
			</td>
			
		</tr>
        @endforeach
		
    @else
     <h3>Database is Empty !! Plese Add to view List</h3>
		
     @endif
	</tbody>
</table>
<a href="{{route('pdf.convert')}}" class="btn btn-danger">convert To PDF</a>
</div>




@stop