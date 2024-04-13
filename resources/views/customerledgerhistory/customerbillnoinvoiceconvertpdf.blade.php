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
        .seconddiv{
            margin-top: -30px !important;
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
        .forbillandpan{
            margin-top: -70px !important;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
    </div>

    <div class="address-info">
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div>

    <div class="invoice-info">

        <div class="row">
            <div class="firstdiv"> 
                        @if(isset($forinvoicetype) && !empty($forinvoicetype))
                        <p>Invoice Type: {{ $forinvoicetype->invoicetype }}</p>
                        <p>Date: {{ $forinvoicetype->date }}</p>
                        @endif
            </div>
       
       <div class="forbillandpan">
            Invoice No: {{$invoiceid}} <br>
            PAN No. 601064191
       </div>
          
            <div class="seconddiv"> 
              
                        @if ($cinfodetails !=null)
                            @foreach($cinfodetails as $i)
                                <p>Name: {{$i->name}}</p>
                                <p>Address: {{$i->address}}</p>
                                <p>Email: {{$i->email}}</p>
                                <p>Contact No: {{$i->phoneno}}</p>
                            @endforeach

                          
                        @endif

                      

                        @if ($allinvoices !=null)
                            @foreach($allinvoices as $i)
                                <p>Customer Id: {{$i->customerid}}</p>
                            @endforeach
                        @endif
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Serial No.</th>
                <th>ITEM ID</th>
                <th>ITEM Name</th>
              
                <th>Quantity</th>
                <th>Unit</th>
                <th>Sold Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $serialNo = 1; @endphp
            @if ($allcusbyid != null)
                @foreach($allcusbyid as $i)
                    <tr>
                        <td>{{ $serialNo++ }}</td>
                        <td>{{$i->itemidorg}}</td>
                        
                        <td>{{$i->itemid}}</td>
                       
                        <td>{{$i->quantity}}</td>
                        <td>{{$i->unit}}</td>
                        <td>{{$i->price}}</td>
                        <td>{{$i->subtotal}}</td>
                    </tr>
                @endforeach
            @endif

            @if ($allinvoices != null)
                @foreach($allinvoices as $i)
                    <tr>
                        <td colspan="5"></td>
                        <td class="text-right"><b>Sub-Total:</b></td>
                        <td><b>{{$i->subtotal}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="5"></td>
                        <td class="text-right"><b>Extra Discount:</b></td>
                        <td><b>{{$i->discount}}</b></td>
                    </tr>
                    <tr>
                        
                        <td colspan="5" class="">Amount in Words: 
                        @php
                            function convertNumberToWords($num) {
                                $ones = array(
                                    "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                                    "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"
                                );
                                $tens = array(
                                    "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
                                );

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
                            $number = $i->total;

                            // Convert the numerical value to words
                            $words = convertNumberToWords($number);

                            echo $words;
                        @endphp
                        only/-
                        </td>
                        <td class="text-right"><b>Total Amount:</b></td>
                        <td colspan="" id="totalAmountWords">{{$i->total}}</td>

                    </tr>
                    
                    <tr>
                        <td colspan="7" class="notes"><b>Notes:</b> {{$i->notes}}</td>
                    </tr>
                @endforeach

                
            @endif
        </tbody>
    </table>


    <br>@if ($allinvoices !=null)
    @foreach($allinvoices as $i)
      
      <p>Bill Created_by: {{$i->added_by}} </p>
    @endforeach
@endif
    <p style="margin-top: 20px; font-size: 14px; text-align: center;">Notes:  Goods once sold won't be returned</p>
</div>

<script>

        
</script>

</body>
</html>
