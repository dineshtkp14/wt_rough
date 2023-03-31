@extends('layouts.master')
@include('layouts.breadcrumb')


@section('content')
<div class="main-content"> 
    @yield('breadcrumb')


<div class="container">



<table>
	<thead>
		<tr>
			<th>Id</th>
			
			<th>Date</th>
			<th>Customer Name</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
            <th>Amount</th>
           

			
		</tr>
	</thead>
	<tbody>
		
    @if ($all !=null)
                        @foreach ($all as $i)
		<tr>
			<td data-label="Id">{{ $i->id }}</td>
			<td data-label="Name">{{ $i->created_at }}</td>
			<td data-label="Name">{{ $i->customerid }}</td>
			<td data-label="Address">{{ $i->particulars}}</td>
			<td data-label="Contact No.">{{ $i->voucher_type }}</td>
			
           
            <td data-label="Remarks">{{ $i->credit }}</td>
			
			
		</tr>
        @endforeach
		
    @else
     <h3>Database is Empty !! No Record Found</h3>
		
     @endif
	</tbody>
</table>
</div>


</div>
@stop