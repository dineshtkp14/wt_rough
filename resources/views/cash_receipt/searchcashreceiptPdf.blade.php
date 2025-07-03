<!DOCTYPE html>
<html>
<head>
    <title>Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 10px !important;
        }

        .letterhead {
            color: black;
            text-align: center;
        }

        .letterhead h1 {
            margin: 0;
            font-size: 34px;
        }

        .address-info {
            text-align: center;
            margin-top: 10px;
            font-size: 20px;
        }

        .info-section,
        .receipt-details {
            width: 50%;
            box-sizing: border-box;
            padding: 0 10px;
            float: left;
        }

        .info-section h5,
        .receipt-details h5 {
            color: #007bff;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            margin-bottom: 5px;
            font-size: 20px;
            word-break: break-word;
        }

        .receipt-details p {
            margin: 5px 0;
            font-size: 18px;
            word-break: break-word;
        }

        .signature-section {
            clear: both;
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
            font-size: 18px;
        }

        .signature-section div {
            flex: 1;
            display: flex;
            align-items: baseline;
        }

        .signature-section p {
            margin: 0;
        }

        .top-right-info {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 20px;
        }

        @page {
            size: A5 landscape;
            margin: 30px;
        }

        .cashrecipttext {
            font-size: 24px;
            font-weight: bold;
            font-family: Fantasy;
            text-decoration: underline;
        }

        .receiver-signature {
            text-align: right;
        }

        .watermark {
            position: fixed;
            top: 45%;
            left: 35%;
            transform: rotate(-45deg);
            font-size: 148px;
            opacity: 0.1;
            color: gray;
        }

        .container {
            page-break-inside: avoid;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="watermark">OHT</div>

    <div class="header">
        <div class="letterhead">
            <h1>OM HARI TRADELINK</h1>
        </div>

        <div class="address-info">
            <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
            <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
            <p class="cashrecipttext">CASH RECEIPT</p>
        </div>
    </div>

    <div class="top-right-info">
        @if (!empty($alldetails))
            <p>Receipt No: <strong>{{ $alldetails[0]->id ?? '' }}</strong></p>
            <p><strong>Date:</strong> {{ $alldetails[0]->date ?? '' }}</p>
        @endif
    </div>

    <div class="info-section">
        <h5>RECEIVED FROM</h5>
        <ul class="info-list">
            @foreach($customerinfodetails as $info)
                <li><strong>Name:</strong> {{$info->name}}</li>
                <li><strong>Address:</strong> {{$info->address}}</li>
                <li><strong>Email:</strong> {{$info->email}}</li>
                <li><strong>Contact No:</strong> {{$info->phoneno}}, {{$info->alternate_phoneno}}</li>
            @endforeach
        </ul>
    </div>

    <div class="receipt-details">
        <h5>Receipt Details</h5>
        @foreach ($alldetails as $data)
            <p><strong>Particulars:</strong> {{$data->particulars}}</p>
            <p><strong>Voucher Type:</strong> {{$data->voucher_type}}</p>
            <p><strong>Amount:</strong> {{$data->credit}}/-</p>
            <p><strong>Amount In Words:</strong>
                @php
                    function convertNumberToWords($num) {
                        $ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                        "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
                        $tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
                        if ($num == 0) return "Zero";
                        $words = "";
                        if ($num >= 10000000) { $words .= convertNumberToWords(floor($num / 10000000)) . " Crore "; $num %= 10000000; }
                        if ($num >= 100000) { $words .= convertNumberToWords(floor($num / 100000)) . " Lakh "; $num %= 100000; }
                        if ($num >= 1000) { $words .= convertNumberToWords(floor($num / 1000)) . " Thousand "; $num %= 1000; }
                        if ($num >= 100) { $words .= convertNumberToWords(floor($num / 100)) . " Hundred "; $num %= 100; }
                        if ($num >= 20) { $words .= $tens[floor($num / 10)] . " "; $num %= 10; }
                        if ($num > 0) { $words .= $ones[$num] . " "; }
                        return trim($words);
                    }
                    echo convertNumberToWords($data->credit);
                @endphp only/-
            </p>
            <p><strong>Notes:</strong> {{$data->notes}}</p>
        @endforeach
    </div>

    <div class="signature-section">
        <div>
            <p><strong>Payer's Signature:</strong> __________</p>
        </div>
        <div class="receiver-signature">
            <p><strong>Receiver's Signature:</strong> ________________</p>
        </div>
    </div>

    <p style="font-size: 14px !important; margin-top:30px;">
        Printed Time and Date: 
        <span style="color: #4b4b4b; font-size: 14px;"><?php echo date("Y-m-d H:i:s"); ?></span>
    </p>

    <div style="margin-top: 30px; background-color: black; color: white; border: 1px solid black; padding: 10px;">
        Total Due Amount:
        <span style="
            font-size: 40px;
            font-weight: bold;
            border: 2px solid white;
            padding: 5px 15px;
            display: inline-block;
        ">
            {{ number_format($dts - $cts, 2) }}
        </span> -/
        <span style="font-size: 16px;">
            ( as of the date and time: <?php echo date("Y-m-d H:i:s"); ?> )
        </span>
    </div>
</div>

</body>
</html>
