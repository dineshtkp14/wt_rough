<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Invoices - {{ $customer->name ?? 'Customer' }}</title>
    <style>
        @page { size: A5 portrait; margin: 5mm; }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 16px;
            margin: 0;
            padding: 0;
        }
        
        .page { 
            width: 100%;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            page-break-after: always;
            position: relative;
        }
        
        .page:last-child {
            page-break-after: avoid;
        }
        
        .watermark {
            position: fixed;
            top: 45%;
            left: 35%;
            transform: rotate(-45deg);
            font-size: 120px;
            opacity: 0.1;
            color: gray;
            pointer-events: none;
        }
        
        .letterhead {
            text-align: center;
            color: #000;
            margin-bottom: 8px;
        }
        
        .letterhead h1 {
            margin: 0 0 5px;
            font-size: 34px;
            text-decoration: underline;
        }
        
        .address-info {
            font-size: 15px;
            text-align: center;
            margin-bottom: 12px;
        }
        
        .address-info p {
            margin: 2px 0;
        }
        
        .invoice-info {
            font-size: 16px;
            margin-top: 14px;
        }
        
        .firstdiv {
            float: right;
            text-align: right;
            width: 50%;
        }
        
        .firstdiv p {
            margin: 2px 0;
            font-size: 15px;
        }
        
        .forbillandpan {
            float: left;
            width: 50%;
            line-height: 1.2;
        }
        
        .invoice-no {
            font-size: 22px;
            font-weight: 800;
        }
        
        .invoice-no .num {
            font-weight: 800;
        }
        
        .pan-line {
            font-size: 14px;
            margin-top: 0;
        }
        
        .seconddiv {
            clear: both;
            padding-top: 12px;
        }
        
        .seconddiv p {
            margin: 3px 0;
            font-size: 16px;
        }
        
        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 15px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px;
            height: 28px;
            line-height: 1.3;
            vertical-align: middle;
            text-align: center;
        }
        
        th {
            font-weight: 700;
            font-size: 15px;
        }
        
        .text-right {
            text-align: right;
            padding-right: 3px;
        }
        
        .text-left {
            text-align: left;
            padding-left: 3px;
        }
        
        .notes {
            margin-top: 12px;
            font-size: 15px;
            line-height: 1.4;
        }
        
        .footer-info {
            margin-top: 16px;
            font-size: 14px;
        }
        
        .footer-info p {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    @foreach($invoiceData as $index => $data)
    <div class="page">
        <div class="watermark">OHT</div>
        
        <div class="letterhead">
            <h1>OM HARI TRADELINK</h1>
        </div>
        
        <div class="address-info">
            <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
            <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
        </div>
        
        <div class="invoice-info clearfix">
            <div class="firstdiv">
                @if(isset($data['invoice']->inv_type))
                    @if($data['invoice']->inv_type == 'credit')
                        <p style="background:#000;color:#fff;padding:4px 8px;font-size:10px;display:inline-block;">Type: {{ $data['invoice']->inv_type }}</p>
                    @else
                        <p>Type: {{ $data['invoice']->inv_type }}</p>
                    @endif
                @endif
                <p>Date: {{ $data['invoice']->inv_date }}</p>
                <p>Miti: {{ \App\Support\NepaliDate::adToBsString($data['invoice']->inv_date ?? now()->toDateString(), 'en') }}</p>
            </div>
            
            <div class="forbillandpan">
                <div class="invoice-no">INVOICE NO: <span class="num">{{ $data['invoice']->id }}</span></div>
                @if($data['invoice']->total < 19900)
                    <div class="pan-line">PAN No. 608641838</div>
                @endif
            </div>
        </div>
        
        <div class="seconddiv forfontsizebll">
            <p>Name: {{ $customer->name ?? 'N/A' }}</p>
            <p>Address: {{ $customer->address ?? 'N/A' }}</p>
            <p>Email: {{ $customer->email ?? '' }}</p>
            <p>Contact No: {{ $customer->phoneno ?? 'N/A' }}, {{ $customer->alternate_phoneno ?? '' }}</p>
            <p>Customer Id: {{ $customer->id ?? 'N/A' }}</p>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>#</th>
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
                @foreach($data['items'] as $item)
                <tr>
                    <td>{{ $serialNo++ }}</td>
                    <td>{{ $item->itemidorg ?? '-' }}</td>
                    <td class="text-left">{{ $item->itemname ?? $item->unstockedname ?? 'N/A' }}</td>
                    <td>{{ $item->nos ?? $item->quantity ?? 1 }}</td>
                    <td>{{ $item->unit ?? 'pcs' }}</td>
                    <td>{{ $item->price ?? 0 }}</td>
                    <td>{{ $item->subtotal ?? 0 }}</td>
                </tr>
                @endforeach
                
                <tr>
                    <td colspan="5"></td>
                    <td class="text-right"><b>Sub-Total:</b></td>
                    <td><b>{{ $data['invoice']->subtotal ?? $data['invoice']->total ?? 0 }}</b></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-left" style="font-size: 9px;">
                        # Goods once sold won't be returned
                    </td>
                    <td class="text-right">E-Discount:</td>
                    <td>{{ $data['invoice']->discount ?? 0 }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-left" style="font-size: 10px;">
                        <b>Amount in Words: </b>
                        @php
                        if (!function_exists('convertNumToWords')) {
                            function convertNumToWords($num) {
                                $ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
                                $tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
                                if ($num == 0) return "Zero";
                                $words = "";
                                if ($num >= 10000000) { $words .= convertNumToWords(floor($num/10000000))." Crore "; $num %= 10000000; }
                                if ($num >= 100000)   { $words .= convertNumToWords(floor($num/100000))." Lakh ";  $num %= 100000; }
                                if ($num >= 1000)     { $words .= convertNumToWords(floor($num/1000))." Thousand "; $num %= 1000; }
                                if ($num >= 100)      { $words .= convertNumToWords(floor($num/100))." Hundred ";  $num %= 100; }
                                if ($num >= 20)       { $words .= $tens[floor($num/10)]." "; $num %= 10; }
                                if ($num > 0)         { $words .= $ones[(int)$num]." "; }
                                return trim($words);
                            }
                        }
                        echo convertNumToWords($data['invoice']->total ?? 0) . " only/-";
                        @endphp
                    </td>
                    <td class="text-right"><b>Total Amount:</b></td>
                    <td><b>{{ $data['invoice']->total ?? 0 }}</b></td>
                </tr>
                @if($data['invoice']->notes)
                <tr>
                    <td colspan="7" class="text-left"><b>Notes:</b> {{ $data['invoice']->notes }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        
        <div class="footer-info">
            <p>Bill Created_by: {{ $data['invoice']->added_by ?? 'System' }}</p>
            <p style="font-size: 9px; color: #666;">Printed Time and Date: {{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>
    @endforeach
    
    @if(count($invoiceData) == 0)
    <div style="text-align: center; padding: 40px; color: #666;">
        No invoices found for this customer.
    </div>
    @endif
</body>
</html>
