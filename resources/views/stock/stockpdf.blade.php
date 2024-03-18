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

<div class="container">
    <div class="letterhead">
        <h1>OM HARI TRADELINK  --(check Stock)--</h1>
    </div>

   <div class="address-info" style="font-size: 21px;">
    Total No Of Items: <b>{{ $cou}} </b>

    <p style="font-size: 21px;"><p>Today's date and time: {{ date('Y-m-d H:i:s') }}</p>
</p>
</div>

    <div class="invoice-info">

        <div class="container">
   

            <div class="card-body overflow-auto">
            
                <table class="table">
                    
                    <thead>
                        <tr>
                            <th>S.N</th>

                            <th>Item Id</th>
                            <th>Items Name</th>
                            <th class=" bg-dark">Quantity</th>
                           
                            <th>Unit</th>
                            <th>Item Store Area</th>
                            <th>Firm Name</th>
                            <th>MRP</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @if ($all->count())
                        @php
                          
                            $serial = ($all->currentPage() - 1) * $all->perPage() + 1;

                    @endphp
                    @foreach ($all as $i)
                        <tr>
                            <td>{{ $serial }}</td>
                            <td>{{ $i->id }}</td>
                            <td>{{ $i->itemsname }}</td>
                           
                           
                            <td><b>{{ $i->quantity }}</b></td>
                            
                           
                            <td>{{ $i->unit }}</td>
                            <td>{{ $i->item_store_area }}</td>
                            <td>{{ $i->firm_name }}</td>
                          
    
                            <td>{{ $i->mrp }} &nbsp; &nbsp; <!-- Button trigger modal --></td>
                           
                        @php $serial++; @endphp
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">No record found</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
           
        
           
        </div>
        
    
          

</body>
</html>
