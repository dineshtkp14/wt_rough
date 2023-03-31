@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')


<div class="container">

	
<div class="row">


	  
	<div class="col-md-3 mb-3">
		<form action="{{ route('banks.index') }}" method="get">
			<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
		<div class="input-group">
		  <div class="input-group-text">Choose Start Date</div>
			<input type="date" name="date1" value="" class="form-control" id="inputs">
		</div>
	</div>
	<div class="col-md-3  mb-3">
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
	@if($totalsum!=null)
	<h4>Total Sum of choosen Date: <span class="text-success h1">{{$totalsum}}</span> /-</h4>

   @endif

   

</div>


	


<br>


<table id="example">
	<thead>
		<tr>
			<th>Id</th>
			<th>Deposited By</th>
			
			<th>Amount</th>
			
            <th>Date</th>
            <th>Remarks</th>
			
		</tr>
	</thead>
	<tbody>
    @if ($custo->isNotEmpty())
                        @foreach ($custo as $i)
		<tr>
			<td data-label="Id">{{ $i->id }}</td>
			<td data-label="Name">{{ $i->name }}</td>
			
			<td data-label="Amount">{{ $i->amount }}</td>
			
            <td data-label="Date">{{ $i->date }}</td>
            <td data-label="Remarks">{{ $i->remarks }}</td>
			
		</tr>
        @endforeach
		
    @else
     <h3>No Record Found</h3>
		
     @endif
	</tbody>
</table>
<br><br>
<a href="" class="btn btn-danger" id="pdfLink">Print To PDF</a>

<script>
	document.getElementById('pdfLink').addEventListener('click', function(e) {
        e.preventDefault(); 
		var query=window.location.search;
		var param=new URLSearchParams(query);

        var url = "{{ route('banks.convert') }}?&date1=" + param.get('date1') + "&date2=" + param.get('date2');
		window.location.href = url;
		
		

    });
</script>

</div>

</div>

@stop