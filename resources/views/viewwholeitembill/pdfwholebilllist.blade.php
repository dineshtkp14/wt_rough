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
        }
        th, td {
            border: 1px solid #000; /* Set border color to black */
            padding: 10px;
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
        @if ($companyall !=null)
            @foreach($companyall as $i)
        <h1>   {{$i->name}}</h1>
        @endforeach
        @endif
    </div>

    <div class="address-info">
        @if ($companyall !=null)
            @foreach($companyall as $i)
          <p>Address: {{$i->address}}</p>
        <p>Contact No: {{$i->phoneno}}</p>
        @endforeach
        @endif
        @if ($all !=null)
            @foreach($all as $i)
        <p style="float: right;">Date: {{$i->date}}</p>

        
        @endforeach
        @endif
    </div>

    <div class="invoice-info">

        <div class="row">
            
       
            <div class="seconddiv"> 
                        @if ($companyall !=null)
                            @foreach($companyall as $i)
                            <p>Company Id: {{$i->id}}</p>
                                <p>Name: {{$i->name}}</p>
                                <p>Address: {{$i->address}}</p>
                                <p>Email: {{$i->email}}</p>
                                <p>Contact No: {{$i->phoneno}}</p>
                            @endforeach
                        @endif

                        <p>Invoice Id: {{$billNo}}</p>

                       
            </div>
        </div>
    </div>

    {{-- <div class="col-md-9">
        <div>
            <h4>Bill Number: {{ $billNo }}</h4>
            <h4>Company Name: {{ $companyName }}</h4>
        </div>
    </div> --}}
</div>
</div>



<div class="container">
<div class="card customer-card mb-4" id="customerCard" style="display: none;">
    <div class="card-body">
        <h5 class="card-title">Customer Info</h5>
        <!-- Customer info placeholders -->
    </div>
    <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
        <i class="fas fa-user"></i>
    </div>
</div>

@if ($all->isEmpty())
   <h3>No items found. !!!!</h3>
@else
    <table>
        <thead>
            <tr>
                <th>ITEM ID</th>
                <th>ITEM Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Cost Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($all as $i)
                <tr>
                    <td>{{$i->id}}</td>
                    <td>{{$i->itemsname}}</td>
                    <td>{{$i->quantity}}</td>
                    <td>{{$i->unit}}</td>
                    <td>{{$i->costprice}}</td>
                    <td>{{$i->total}}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5"></td>
                <td><b>Total-: {{ $totalSum }}</b></td>

            </tr>
          
        </tbody>
    </table>
@endif

   

    <p style="margin-top: 20px; font-size: 14px; text-align: center;">Notes:  Goods once sold won't be returned</p>
</div>

<script>

</script>
</body>
</html>
