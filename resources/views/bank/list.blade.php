@extends('layouts.master')

@section('content')

<h2 class="text-center mt-5">View Banks</h2>
<h4>Total amount of choosen Date: {{$totalsum}}</h4>
<div class="container">
	<div class="row float-end">
<div class="col-12 float-end ">

        <input class="form-control  border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
    
</div>
</div>

<a href="/banks/create">Back</a>

	<form action="{{ route('banks.index') }}" method="get">
	<input type="date" name="date1" value="">
	<input type="date" name="date2" value="">
	<input type="submit" name="" value="Submit">

</form>



<br>
<br>


<table>
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
</div>



@stop