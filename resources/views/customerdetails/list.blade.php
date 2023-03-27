@extends('layouts.master')

@section('content')

<Center><h1 class="text-danger mt-5 bold"><U>VIEW Ledger DETAILS</U></h1></Center>

<div class="container">
	<div class="row">
		<form action="{{ route('cpayments.index') }}" method="get">
			<div class="row">
			<div class="col-md-5">
				
				<select name="customerid" class="form-select" aria-label="Default select example">
					<option selected>Select Customer</option>
					@foreach ($allcus as $i)
					
					<option value="{{$i->id}}"> {{$i->name}}</option>
					@endforeach
					
				  </select>
				
			</div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
				<div class="input-group">
				  <div class="input-group-text">Choose Start Date</div>
					<input type="date" name="date1" value="" class="form-control" id="inputs">
				</div>
			</div>
			<div class="col-md-3">
				<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
				<div class="input-group">
				  <div class="input-group-text">Choose End Date</div>
				<input type="date" name="date2" value=""class="form-control">
				</div>
			</div>
			<div class="col-md-3">
				<input type="submit" class="btn btn-success mx-2" name="" value="Search">
			</div>
			<div class="col-md-3 mt-3 mt-md-0">
				<div class="float-lg-end">
					<input class="form-control  border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
			
		
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
		
    @if ($all !=null)
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
		
    @else
     <h3>Database is Empty !! No Record Found</h3>
		
     @endif
	</tbody>
</table>
</div>



@stop