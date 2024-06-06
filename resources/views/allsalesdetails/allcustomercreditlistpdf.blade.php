<!DOCTYPE html>
<html>
<head>
    <title>Print</title>
    <script src="{{ asset('assets/js/common.js') }}"></script>

    <style>
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
        .letterhead {
            background-color: black;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .letterhead h1 {
            margin: 0;
            font-size: 24px;
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
        .invoice-info {
            margin-top: 20px;
        }
        .invoice-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000; /* Set border color to black */
            padding: 6px;
        }
        th {
            background-color: white; /* Set background color to white */
        }
        .text-right {
            text-align: right;
        }
        .notes {
            margin-top: 20px;
            max-height: 100px;
            overflow: hidden;
            font-size: 14px;
        }
    </style>
</head>
<body>

    @php
    $totalNegativeDebitCreditDifference = $all->filter(function($item) {
        return $item->debit_credit_difference < 0;
    })->sum('debit_credit_difference');
@endphp

<div class="container">
    <div style="text-align: center;">
        <h3>ALL CREDIT DUE LIST</h3>
        <P>Printed Date and Time: {{ date('Y-m-d H:i:s') }}</P>
    </div>
<div class="card">
<div class="card-header">

    {{-- <span class="me-5 fw-bold">Total Whole Credit: <span class="h4 text-success">{{$totalDebitCreditDifferencewhole}} </span> </span> --}}

    <span>Total Credit of This Page: <span class="h6"><b>{{ $totalDebitCreditDifference  }}</b></span><i class="fas fa-arrow-down"></i> </span>
    {{-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Advance Payment: <span class="h3"><b>{{ $totalNegativeDebitCreditDifference }}</b></span> --}}
 




</div>
<div class="card-body">
<table>
    <thead> 
        <tr>
            <th>S.N</th>

            <th>Customer Id</th>
            <th>Customer Name</th>
            <th>Address</th>
            <th>Total Due Amount</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <tbody>
            @php
            $sn = ($all->currentPage() - 1) * $all->perPage() + 1;
        @endphp
            @if (!$all->isEmpty())
            @foreach ($all as $key => $i)
                    @if ($i->debit_credit_difference != 0)
                        <tr> 
                            <td>{{ $sn++ }}</td>
                            <td data-label="Customer Id"><b>{{ $i->customerid }}</b></td>
                            <td data-label="Customer Id"><b>{{ $i->cname }}</b>  &nbsp; ({{ $i->cphoneno }})</td>
                            <td data-label="Customer Id"><b>{{ $i->address }}</b></td>
                            <td data-label="Invoice Id"><b>{{ $i->debit_credit_difference }}</b></td>
                            <td data-label="Total Due Amount"><b>{{ $i->latest_date  }}</b></td>

                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="3"><h3>No Record Found #!!!!</h3></td>
                </tr>
            @endif
        </tbody>
        
    </tbody>
</table>
</div>
<div class="card-footer text-muted" >

</div>
</div>
</div>

    
          

</body>
</html>
