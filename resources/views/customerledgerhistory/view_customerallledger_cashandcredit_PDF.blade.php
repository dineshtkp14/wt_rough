<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Details</title>
    <style>
        p {
            font-size: 20px !important;
        }
        .info {
        margin-bottom: 10px; /* Adjust the margin bottom as needed */
    }

    .info p {
        margin: 5px 0 !important; /* Adjust the margin top and bottom of each <p> element */
        padding: 0 !important; /* Remove any padding */
        font-size: 20px !important;
    }

        .letterhead {
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
            margin-bottom: 5px;
        }

        .address-info p {
            margin: 0px 0;
            font-size: 14px;
        }

        /* For table1, th, and td */
        .table1, .table1 th, .table1 td {
            border: 2px solid black;
            border-collapse: collapse;
            padding: 1px;
        }

        /* For table2, th, and td */
        .table2, .table2 th, .table2 td {
            border: 2px solid black;
            border-collapse: collapse;
            padding: 10px;
        }

        .floatleft {
            float: left;
        }

        .forunderline {
            text-decoration: underline;
            color: red;
            font-size: 40px;
            font-weight: bold;
        }

        /* Style for total transaction and due amount */
        .total-section {
            clear: both; /* Clear the float */
            margin-top: 20px; /* Adjust the top margin as needed */
            
        }

        .total-section h4,
        .total-section h2 {
            margin: 0; /* Remove default margin */
        }
    </style>
</head>
<body>
<div>
    <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
    </div>

    <div class="address-info">
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div>
</div>

@foreach ($cusinfoforpdfok as $i)
    <div class="info" style="float: left; width: 50%;">
        <p>Name: {{$i->name}}</p>
        <p>Address: {{$i->address}}</p>
        <p>Phone No: {{$i->phoneno}}, {{$i->alternate_phoneno}}</p>
        <p>Phone No: {{$i->email}}</p>
    </div>
@endforeach

<div class="floatxxright" style="float: right; width: 50%;">
   <!-- Total Transaction and Due Amount Section -->
   <div class="total-sectin">
    <br> <br>
    Total Due Amount: 
    <span class="forunderline" style="color: {{ $allnotcash - $cts < 0 ? 'red' : 'green' }}">
        {{-- {{ $allnotcash - $cts }} -/ --}}
        {{ number_format($allnotcash - $cts, 2) }}

    </span>
    
          
      
</div>
</div>
<div style="clear: both;"></div> <!-- Clear the float -->

<div class="container toptbl">
    <table class="table1">
        <thead>
        <tr>
        <?php if(isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'dineshtkp14@gmail.com'): ?>
            <th>DATE</th>
        <?php endif; ?>
            <th>DATE Np</th>
            <th>PARTICULARS</th>
            <th>VOUCHER TYPE</th>
            <th>INVOICE NO</th>
            <th>CN INVOICE NO</th>
            <th>INVOICE TYPE</th>
            <th>DEBIT</th>
            <th>CREDIT</th>
        </tr>
        </thead>
        <tbody>
        @if($all != null)
            @foreach ($all as $i)
                <tr>
                    {{-- <td>{{ \App\Support\NepaliDate::adToBsString($i->date, 'np') }}</td> --}}
                    <?php if(isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'dineshtkp14@gmail.com'): ?>
                        <td data-label="Name">{{ $i->date }}</td>
                    <?php endif; ?>

                    {{ \App\Support\NepaliDate::adToBsString($i->date ?? now()->toDateString(), 'np') }}

                    <td data-label="Address">{{ $i->particulars}}</td>
                    <td data-label="Contact No.">{{ $i->voucher_type }}</td>
                    <td data-label="Contact No.">{{ $i->invoiceid }}</td>
                    <td data-label="Contact No.">{{ $i->cninvoiceid }}</td>

                    <td data-label="Remarks"> {{ $i->invoicetype }}
                        @if($i->invoicetype == 'payment')
                            <b>CR-({{ $i->id }}) </b>
                        
                        @endif
                    </td>
                    
                    <td data-label="Amount">{{ $i->debit }}</td>
                    <td data-label="Remarks">{{ $i->credit }}</td>
                </tr>
            @endforeach
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>
                    @if($dts!=null)
                        Total(Only Credit): <h2>{{$allnotcash }}</h2></td>
                    @endif
                </td>
                <td>
                    @if($cts!=null)
                        Total: <h2>{{$cts }}</h2></td>
                    @endif
                </td>
            </tr>
        @else
            <tr>
                <td colspan="4"><h2>Record Not Found</h2></td>
            </tr>
        @endif
        </tbody>
    </table>

    <!-- Total Transaction and Due Amount Section -->
    <div class="total-section">
        <h4>Total Transcation Amount: <span>{{$dts}} /-</span></h4>
        Total Due Amount: 
        <span class="forunderline" style="color: {{ $allnotcash - $cts < 0 ? 'red' : 'green' }}">
            {{-- {{ $allnotcash - $cts }}  --}}
            {{ number_format($allnotcash - $cts, 2) }}

<span style="font-size: 16px;text-decoration:none;">
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
$number = $allnotcash - $cts;
// Convert the numerical value to words
$words = convertNumberToWords($number);

echo $words;

            @endphp
			only -/ 
			
			)
        </span>
        </span>
        
                    
            
          
    </div>
</div>
<br><br> <br>

<!-- Credit Notes Details -->
<h2 style="white-space: nowrap; text-align: center;">------------------------Credit Notes Details/ Sales Return-----------------------</h2>

<table class="table2">
    <thead>
    <tr>
        <th>Id</th>
        <th>Date</th>
        <th>Particulars</th>
        <th>Voucher Type</th>
        <th>CN Invoice ID</th>
        <th>Credit</th>
    </tr>
    </thead>
    <tbody>
    @if($creditnoteledger != null)
        @foreach ($creditnoteledger as $i)
            <tr>
                <td data-label="Id">{{ $i->id }}</td>
                <td data-label="Name">{{ $i->created_at }}</td>
                <td data-label="Address">{{ $i->particulars}}</td>
                <td data-label="Contact No.">{{ $i->voucher_type }}</td>
                <td data-label="Contact No.">{{ $i->invoiceid }}</td>
                <td data-label="Amount">{{ $i->debit }}</td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @if($debittotalcrnotes != null)
                    Total: <h3>{{$debittotalcrnotes }}</h3>
                @endif
            </td>
        </tr>
    @else
        <tr>
            <td colspan="6"><h2>Credit Notes Record Not Found</h2></td>
        </tr>
    @endif
    </tbody>

</table>
    <p style="font-size: 21px;"><p>Today's date and time: {{ date('Y-m-d H:i:s') }}</p>

</body>
</html>
