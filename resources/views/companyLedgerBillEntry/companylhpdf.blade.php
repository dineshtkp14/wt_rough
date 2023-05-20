
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
    <Center><h2 class="text-danger my-5 bold">OM HARI TRADELINKk compamy ledger </h2></Center>
<Center><h3 class="text-danger my-5 bold"><U>Ledger Details</U></h1></Center>


    <Center><h4 class="text-danger my-5 bold">({{$fromdate}}  To  {{$todate}})</h4></Center>

    <h1></h1>



    @foreach ($xx as $i)


    <h3>Name: {{$i->name}}</h3> 
    <h3> Address: {{$i->address}}</h3>
    <h3>Phone No: {{$i->phoneno}}</h3>


    @endforeach

<div class="container">
	
	

   




<table>
	<thead>
		<tr>
			<th>Idrr</th>
			
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
								   Total: <h3>{{$dts }}</h3></td>
							   @endif
						   </td>
			   
						   <td>
							   @if($cts!=null)
								   Total: <h3>{{$cts }}</h3></td>
							   @endif
						   </td>
			   
					   </tr>

					  
					  
					   @else
                       <h2>Record Not Found </h2>
					   @endif

    
	</tbody>
</table>
<h2 class="floatleft">Total Due Amount : <span class="forunderline">{{$dts -$cts }} /-</span>  </h2>
</div>

</body>
</html>

