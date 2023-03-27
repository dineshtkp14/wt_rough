
<!DOCTYPE html>
<html>
<head>
	<title></title>
    <style>
        table, th, td {
            border: 1px solid green;
            border-collapse: collapse;
            padding: 15px;
            
        }
        .floatleft{
            float: right;
        }
        .forunderline{
            text-decoration: underline;
            color: Red;
        }

    </style>
</head>
<body>

<div class="container">


www

{{$totalsum}}
    @if($totalsum!=null)
	<h4>Total Sum of choosen Date: <span class="text-success h1">{{$totalsum}}</span> /-</h4>

   @endif




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


</div>


</body>
</html>