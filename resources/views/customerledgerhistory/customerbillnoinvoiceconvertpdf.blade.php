<!DOCTYPE html>
<html>
<head>
    <title>Print</title>
    <script src="{{ asset('assets/js/common.js') }}"></script>

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
    <Center><h1 class="text-danger mt-5 bold"><U>OM HARI TRADELINK</U></h1></Center>

    <div class="container">
        <div class="row"></div>

        @if(isset($forinvoicetype) && !empty($forinvoicetype))
        <b style="float: right; margin-right: 100px;">Invoice Type: {{ $forinvoicetype->invoicetype }}</b>
        @endif

        @if ($cinfodetails !=null)
        @foreach($cinfodetails as $i)
        Name:  {{$i->name}}<br>
        Address:  {{$i->address}}<br>
        Email:  {{$i->email}}<br>
        ContactNo:  {{$i->phoneno}}<br>
        @endforeach
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ITEM Name</th>
                    <th scope="col">Original Price</th>
                    <th scope="col">Sold Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Total</th>
                    <th scope="col">Discount</th>
                    <th scope="col">Sub-Total</th>
                </tr>
            </thead>
            <tbody>
                @if ($allcusbyid != null)
                @foreach($allcusbyid as $i)
                <tr>
                    <td>{{$i->itemid}}</td>
                    <td>{{$i->mrp}}</td>
                    <td>{{$i->price}}</td>
                    <td>{{$i->quantity}}</td>
                    <td>{{$i->price*$i->quantity}}</td>
                    <td>{{$i->discount}}</td>
                    <td>{{$i->subtotal}}</td>
                </tr>
                @endforeach
                @endif

                @if ($allinvoices != null)
                @foreach($allinvoices as $i)
                <tr>
                    <td colspan="5"></td>
                    <td class="text-center"><b>Sub-Total:</b></td>
                    <td><b>{{$i->subtotal}}</b></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td class="text-center"><b>Extra Discount: -</b></td>
                    <td><b>{{$i->discount}}</b></td>
                </tr>
                <tr>
                    <td colspan="5" id="totalAmountWords">{{ floor($i->total) }}</td>
                    <td class="text-center"><b>Total Amount:</b></td>
                    <td><b>{{$i->total}}</b></td>
                </tr>
                <tr>
                    <td colspan="7">
                        <b> Notes: {{$i->notes}}</b>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Script to convert total amount to words -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var totalAmountElement = document.getElementById('totalAmountWords');
            var numericalValue = parseFloat(totalAmountElement.textContent.trim());
            var words = convertNumberToWords(numericalValue);
            totalAmountElement.textContent = words;
        });
    </script>
</body>
</html>
