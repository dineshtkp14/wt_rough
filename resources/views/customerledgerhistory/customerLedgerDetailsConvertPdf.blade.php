<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        /* Add your CSS styles here */
        /* Example: */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .letterhead {
            text-align: center;
            margin-bottom: 20px;
        }
        .letterhead h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
            text-decoration: underline;
        }
        .address-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .address-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black; /* Update border to black */
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-due {
            font-size: 20px;
            color: #ff5733; /* Adjust color as needed */
            margin-top: 20px;
        }
        .printed-info {
            font-size: 12px;
            color: #888;
            text-align: right;
        }
        @page{
            margin:40px;
        }
        .left-side {
            float: left;
            width: 50%;
        }
        .right-side {
            float: right;
            width: 50%;
            text-align: right;
        }
        .makedown{
            margin-top: 12%;
        }
    </style>
</head>
<body>
    <div class="container">
       
        <div class="letterhead">
            <h1>OM HARI TRADELINK</h1>
            <CENTER>CUSTOMER LEDGER </CENTER>
        </div>
        <div class="address-info">
            <p style="font-size: 16px;">Address: Tikapur, Kailali (in front of Tikapur Police Station)</p> <!-- Decrease font size -->
            <p style="font-size: 16px;">Mobile No: 9860378262, 9848448624, 9812656284</p> <!-- Decrease font size -->
        </div>

	
      
    <Center><h4 class="text-danger my-5 bold">DATE:  ({{$fromdate}}  To  {{$todate}})</h4></Center>

    <h1></h1>

<div class="left-side">
    @foreach ($cusinfobyid as $i)
    Name: {{$i->name}}<br> 
     Address: {{$i->address}}<br>
    Phone No: {{$i->phoneno}}<br>
    Alternate Phoneno: {{$i->phoneno}}<br>
    Email: {{$i->email}}<br>
    @endforeach
</div>


<div class="right-side">

    Total Due Amount: 
    <span class="forunderline" style="color: {{ $dts - $cts < 0 ? 'red' : 'green' }}">
        {{-- {{ $dts - $cts }} -/ --}}
        {{ number_format($dts - $cts, 2) }} -/

    </span>
    {{-- <h2 class="floatleft">Total Due Amount: <span class="forunderline">{{ $dts - $cts }} /-</span></h2> --}}
</div>

<div class="container">
	

   <div class="makedown">




<table>
	<thead>
		<tr>
            <th>#</th>
			
			<th>Date</th>
			<th>Created_at</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
			<th>Invoice Type</th>

			<th>Invoice No</th>
			<th>Debit</th>  
            <th>Credit</th>
           

			
		</tr>
	</thead>
	<tbody>
        @php $serial = 1 @endphp <!-- Initialize serial number variable -->

  
        @if($all!=null)
        @foreach ($all as $i)
        <tr>
         <td>{{ $serial++ }}</td> <!-- Increment and display serial number -->

					 
						 
                           <td data-label="Name">{{ $i->date }}</td>
						   <td data-label="Name">{{ $i->created_at }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
                           <td data-label="Remarks"> {{ $i->invoicetype }}
                            @if($i->invoicetype == 'payment')
                                <b>CR-({{ $i->id }}) </b>
                            
                            @endif
                        </td>
                        
						   <td data-label="Contact No."><b>{{ $i->invoiceid }}</b></td>
			   
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
</div>
{{-- <h2 class="floatleft">Total Due Amount: <span class="forunderline">{{ $dts - $cts }} /-</span></h2> --}}



Total Due Amount: 
<span class="forunderline" style="color: {{ $dts - $cts < 0 ? 'red' : 'green' }}">
    {{-- {{ $dts - $cts }} -/ --}}
    	{{ number_format($dts - $cts, 2) }} -/

</span>
(
@php
              function convertNumberToWords($num) {
    $ones = array(
        "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
        "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"
    );
    $tens = array(
        "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
    );

    // Handle negative numbers
    if ($num < 0) {
        return "Minus " . convertNumberToWords(abs($num));
    }

    if ($num == 0) {
        return "Zero";
    }

    $words = "";

    if ($num >= 10000000) {
        $words .= convertNumberToWords(floor($num / 10000000)) . " Crore ";
        $num %= 10000000;
    }

    if ($num >= 100000) {
        $words .= convertNumberToWords(floor($num / 100000)) . " Lakh ";
        $num %= 100000;
    }

    if ($num >= 1000) {
        $words .= convertNumberToWords(floor($num / 1000)) . " Thousand ";
        $num %= 1000;
    }

    if ($num >= 100) {
        $words .= convertNumberToWords(floor($num / 100)) . " Hundred ";
        $num %= 100;
    }

    if ($num >= 20) {
        $words .= $tens[floor($num / 10)] . " ";
        $num %= 10;
    }

    if ($num > 0) {
        $words .= $ones[$num] . " ";
    }

    return $words;
}

// Retrieve the numerical value from your data
$number = $dts - $cts;
// Convert the numerical value to words
$words = convertNumberToWords($number);

echo $words;

            @endphp
			only -/ 
			
			)

</div>


</body>
</html>

