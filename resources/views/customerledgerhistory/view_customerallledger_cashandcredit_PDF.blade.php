<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Details</title>
    <style>
       /* For table1, th, and td */
.table1, .table1 th, .table1 td {
    border: 2px solid black;
    border-collapse: collapse;
    padding: 1px;
}

/* For table2, th, and td */
.table2, .table2 th, .table2 td {
    border: 5px solid black;
    border-collapse: collapse;
    padding: 10px;
}

        .floatleft {
            float: right;
        }
        .forunderline {
            text-decoration: underline;
            color: red; /* Note: 'red' instead of 'Red' */
        }
    </style>
</head>
<body>
    <div>
        <center>
            <h2 class="text-danger my-5 bold">OM HARI TRADELINK</h2>
        </center>
        <center>
            <h3 class="text-danger my-5 bold"><u>Ledger Details</u></h3>
        </center>
        <center>
            {{-- <h4 class="text-danger my-5 bold">({{$fromdate}}  To  {{$todate}})</h4> --}}
        </center>
    </div>

    @foreach ($cusinfoforpdfok as $i)
        <div>
            <h3>Name: {{$i->name}}</h3> 
            <h3>Address: {{$i->address}}</h3>
            <h3>Phone No: {{$i->phoneno}}</h3>
            <h3>Phone No: {{$i->email}}</h3>

        </div>
    @endforeach

    <div class="container toptbl">
        <table class="table1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>DATE</th>
                    <th>PARTICULARS</th>
                    <th>VOUCHER TYPE</th>
                    <th>INVOICE NO</th>
                    <th>INVOICE TYPE</th>
                    <th>SALES RETURN </th>
                    <th>CREDIT NOTES INVOICE NO</th>
                    <th>DEBIT</th>  
                    <th>CREDIT</th>
                </tr>
            </thead>
            <tbody>
                @if($all != null)
                    @foreach ($all as $i)
                        <tr>
                            <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Name">{{ $i->created_at }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Contact No.">{{ $i->invoiceid }}</td>
                           <td data-label="Remarks">{{ $i->invoicetype }}</td>
	                       <td data-label="Remarks">{{ $i->salesreturn }}</td>
                           <td data-label="Remarks">{{ $i->returnidforcreditnotes }}</td>
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
                        <td>-</td>
                        <td>-</td>

            
                        <td>
                            @if($dts!=null)
                                Total(only cash): <h2>{{$allnotcash }}</h2></td>
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
                        <td colspan="7"><h2>Record Not Found</h2></td>
                    </tr>
                @endif
            </tbody>
        </table>
        
<h4 class="floatleft">Total Transcation Amount: <span class="forunderline">{{$dts}} /-</span></h4><br><br>

<h1 class="floatleft">Total Due Amount: <span class="forunderline">{{$allnotcash - $cts}} /-</span></h1>


    </div>
<br><br> <br>
    <!-- Credit Notes Details -->
    <h2>--------------------------------Credit Notes Details-------------------------</h2>
    <table class="table2">
        <thead>
            <tr>
                <th>Id</th>
                <th>Date</th>
                <th>Particulars</th>
                <th>Voucher Type</th>
                <th>Invoice ID</th>
                <th>Debit</th>  
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

</body>
</html>
