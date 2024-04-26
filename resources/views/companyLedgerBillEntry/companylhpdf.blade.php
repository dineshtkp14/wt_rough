
<!DOCTYPE html>
<html>
<head>
	<title></title>
    <style>
        table, th, td {
            border: 1px solid rgb(1, 9, 1);
            border-collapse: collapse;
            padding: 15px;
            
        }
		.printed-info {
           
            font-size: 14px;
            color: #888;
        }

        .floatleft{
            float: right;
        }
        .forunderline{
            text-decoration: underline;
            color: Red;
        }

		body {
            font-family: Arial, sans-serif;
            margin: 0 !important;
            padding: 0 !important;
        }
        * {
            margin-top: 0 !important; /* Set top margin to 0 for all elements */
        }
        .container {
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }
        p{
            font-size: 16px !important;;
        }
        .letterhead {
            /* background-color: black; */
            color: black;
            padding: 20px;
            text-align: center;
        }
        .letterhead h1 {
            margin: 0;
            font-size: 30px;
            text-decoration: underline;
        }
        .address-info {
            text-align: center;
            margin-top: 20px;
        }

        .firstdiv{
            float: right;
        }
        .address-info p {
            margin: 5px 0;
            font-size: 14px;
        }
       

    </style>
</head>
<body>
    <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
    </div>

    <div class="address-info">
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div>



    <Center><h4 class="text-danger my-5 bold">({{$from}}  To  {{$to}})</h4></Center>

    <h1></h1>



    @foreach ($xx as $i)
<span>
    Company Id: {{$i->id}}<br> 
    Name: {{$i->name}}<br> 
     Address: {{$i->address}}<br>
    Phone No: {{$i->phoneno}}<br>
    Email: {{$i->email}}<br>
  
</span>



    @endforeach

<div class="container">
	
	

   




<table>
	<thead>
		<tr>
			<th>Idrr</th>
			
			<th>Date</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
			<th>Bill No</th>
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
						   <td data-label="Contact No.">{{ $i->voucher_no }}</td>
			   
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
<h2 class="floatleft">
    Total Due Amount : 
    <span class="forunderline" style="color: {{  $cts -$dts < 0 ? 'red' : 'green' }}">
        {{$dts - $cts }} /-
    </span>
</h2>
</div>

<div class="printed-info">
	Printed on: {{ now()->format('Y-m-d H:i:s') }}
</div>
</body>
</html>

