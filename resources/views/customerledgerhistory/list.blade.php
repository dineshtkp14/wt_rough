@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 
	@yield('breadcrumb')
<div class="container">
	
	<div class="row">
	  <form action="{{ route('clhs.returnchoosendatehistroy') }}" method="get" id="chosendatepdfform">

		<div class="row">
			<div class="col-md-5 mb-3 px-5">
				

				<select name="customerid" class="form-select" aria-label="Default select example" id="cusinfo">
                    <option selected>Select Customer</option>
                    @foreach ($allcus as $i)
                    
                    <option value="{{$i->id}}"> {{$i->name}}</option>
                    @endforeach
                    
                  </select>
				
			</div>
		</div>
<script>

</script>
		<div class="row">
			<div class="col-md-3 mb-3">
				<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
				<div class="input-group">
				  <div class="input-group-text">Choose Start Date</div>
					<input type="date" name="date1" value="" class="form-control" id="inputs">
				</div>
			</div>

			<div class="col-md-3 mb-3">
				<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
				<div class="input-group">
			  		<div class="input-group-text">Choose End Date</div>
						<input type="date" name="date2" value=""class="form-control">
				</div>
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
			
			<th>Date</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
			<th>Invoice ID</th>
			<th>Debit</th>  
            <th>Credit</th>
           

			
		</tr>
	</thead>
	<tbody>
        
   
                       @if($all!=null)
					   @foreach ($all as $i)
					   <tr>
						   <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Name">{{ $i->created_at }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Contact No.">{{ $i->invoiceid }}</td>
			   
						   <td data-label="Amount">{{ $i->debit }}</td>
						   
						  
						   <td data-label="Remarks">{{ $i->credit }}</td>
						   
						   
					   </tr>
					   
					   @endforeach
					   <tr>
						   <td></td>
						   <td></td>
			   
						   <td></td>
			   
						   <td></td>
						   <td></td>
			   
						   <td>
							   @if($dts!=null)
								   Total: <h2>{{$dts }}</h2></td>
							   @endif
						   </td>
			   
						   <td>
							   @if($cts!=null)
								   Total: <h2>{{$cts }}</h2></td>
							   @endif
						   </td>
			   
					   </tr>
					   
					  
					   @else
                       <h2>Record Not Found </h2>
					   @endif

    
	</tbody>
</table>
</div>
@if($all!=null)
<a href="{{route('clhspdf.convert')}}" class="btn btn-danger" id="pdfLink">convert To PDF</a>



@endif
<script>
	document.getElementById('pdfLink').addEventListener('click', function(e) {
        e.preventDefault(); 
		var query=window.location.search;
		var param=new URLSearchParams(query);

        var url = "{{ route('clhspdf.convert') }}?customerid=" + param.get('customerid') + "&date1=" + param.get('date1') + "&date2=" + param.get('date2');
		window.location.href = url;

    });
</script>

</div>
@stop