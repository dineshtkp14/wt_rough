@extends('layouts.master')

@section('content')
<div class="main-content"> 
<h2 class="text-center mt-5">View Suppliers Detaildds</h2>

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
			<th>Bill No</th>
			<th>Distributors Name</th>
			<th>Date</th>
			<th>Items Name</th>
			<th>Quantity</th>
            
            <th>Cost Rate</th>
            <th>MRP</th>
			<th>Total</th>
          
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
           

			<td data-label="action">
				<a href="{{Route('items.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                          
                                   
                          <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                          <form id="eea{{$i->id}}" action="{{ route('items.destroy',$i->id)}}" method="post">
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
</div>

</div>

@stop