@extends('layouts.master')
@include('layouts.breadcrumb')

</script>
@section('content')
<div class="main-content"> 
<Center><h1 class="text-danger my-5 bold"><U>VIEW Ledgerd DETAILSs</U></h1></Center>

<div class="container">
	@yield('breadcrumb')
	<div class="row">
	  <form action="{{ route('cbills.returncusbills') }}" method="get">

		<div class="row">
			<div class="col-md-5 mb-3 px-5">
				<select name="customerid" class="form-select" aria-label="Default select example">
                    <option selected>Select Customer</option>
                    @foreach ($allcus as $i)
                    
                    <option value="{{$i->id}}"> {{$i->name}}</option>
                    @endforeach
                    
                  </select>
			</div>
            <div class="col-md-2">
				<input type="submit" class="btn btn-success mx-2" name="" value="Search">
				 </form>
			</div>
            <div class="col-md-4 mt-3 mt-md-0">
				<div class="float-lg-end">
					<input class="form-control  border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
			
		
				</div>
			</div>
		</div>

		

	  </form>
	</div>


<table>
	<thead>
		<tr>
			<th>Id</th>
			
			<th>CustomerID</th>
			
			<th>Sub-Total </th>
			<th>Discount</th>
			<th>Total</th>  
            <th>Notes</th>
            <th>Date</th>
           

			
		</tr>
	</thead>
	<tbody>
        
 
                       @if($all!=null)
					   @foreach ($all as $i)
					   <tr>
						   <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Name">{{ $i->customerid }}</td>
						   <td data-label="Address">{{ $i->subtotal}}</td>
						   <td data-label="Contact No.">{{ $i->discount }}</td>
						   <td data-label="Contact No.">{{ $i->total }}</td>
			   
						   <td data-label="Amount">{{ $i->notes }}</td>
						   
						  
						   <td data-label="Remarks">{{ $i->created_at }}</td>
						   
						   
					   </tr>
					   
					   @endforeach
					   <tr>
						   <td></td>
						   <td></td>
			   
						   <td></td>
			   
						   <td></td>
						   <td></td>
			   
						   
			   
					   </tr>
					   
					  
					   @else
                       <h2>Record Not Found </h2>
					   @endif

    
	</tbody>
</table>
</div>

</div>

@stop