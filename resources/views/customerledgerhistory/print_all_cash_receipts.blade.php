<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Cash Receipts - {{ $customer->name ?? 'Customer' }}</title>
    <style>
        @page { size: A5 landscape; margin: 15px; }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            position: relative;
        }

        .header {
            text-align: center;
            margin-top: 0;
            padding-top: 0;
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: 2px solid #007bff;
        }

        .letterhead h1 {
            margin-top: 0;
            margin-bottom: 3px;
            font-size: 28px;
            color: #000;
            letter-spacing: 1px;
            font-weight: bold;
        }

        .header h3 {
            margin: 0;
            color: #007bff;
        }

        .letterhead {
            color: black;
            text-align: center;
        }

        .address-info {
            font-size: 16px;
            line-height: 1.2;
            margin-top: 3px;
            margin-bottom: 0;
            padding: 0;
            color: #333;
        }

        .address-info p {
            margin: 2px 0;
            padding: 0;
        }

        .cashrecipttext {
            font-size: 22px;
            font-weight: bold;
            font-family: Georgia, serif;
            color: #007bff;
            text-transform: uppercase;
            text-decoration: underline;
            margin-top: 8px;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .top-right-info {
            position: absolute;
            top: 10px;
            right: 25px;
            font-size: 18px;
            text-align: right;
            line-height: 1.3;
            padding: 5px 10px;
        }

        .info-section,
        .receipt-details-section {
            width: 50%;
            box-sizing: border-box;
            padding: 0 10px;
            float: left;
            margin-top: 10px;
        }

        .info-section h5,
        .receipt-details-section h5 {
            color: #007bff;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            margin-bottom: 3px;
            font-size: 18px;
        }

        .receipt-details-section p {
            margin: 3px 0;
            font-size: 18px;
        }

        .signature-section {
            clear: both;
            margin-top: 40px;
            overflow: hidden;
            font-size: 16px;
        }

        .signature-section .left {
            float: left;
            width: 50%;
        }

        .signature-section .right {
            float: right;
            width: 50%;
            text-align: right;
        }

        .signature-section p {
            margin: 0;
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
            pointer-events: none;
        }

        .total-due-box {
            background-color: black;
            color: white;
            border: 1px solid black;
            padding: 8px;
            font-size: 16px;
            margin-top: 80px;
            page-break-inside: avoid;
        }

        .total-due-amount {
            font-size: 40px;
            font-weight: bold;
        }

        .footer-printed {
            font-size: 12px !important;
            margin-bottom: 5px;
        }

        .receipt-counter {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @foreach($receipts as $index => $receipt)
    <div class="page">
        <div class="watermark">OHT</div>

        <div class="header">
            <div class="letterhead">
                <h1>OM HARI TRADELINK</h1>
            </div>

            <div class="address-info">
                <p>Address: Tikapur, Kailali (In front of Tikapur Police Station)</p>
                <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
            </div>
            <p class="cashrecipttext">Cash Receipt</p>
        </div>

        <div class="top-right-info">
            <p><strong>Receipt No:</strong> {{ $receipt->id }}</p>
            <p><strong>Date:</strong> {{ $receipt->date }}</p>
            <p><strong>Miti (BS):</strong> {{ \App\Support\NepaliDate::adToBsString($receipt->date ?? now()->toDateString(), 'en') }}</p>
        </div>

        <div class="info-section">
            <h5>RECEIVED FROM</h5>
            <ul class="info-list">
                <li><strong>Name:</strong> {{ $customer->name ?? 'N/A' }}</li>
                <li><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</li>
                <li><strong>Email:</strong> {{ $customer->email ?? '' }}</li>
                <li><strong>Contact No:</strong> {{ $customer->phoneno ?? 'N/A' }}, {{ $customer->alternate_phoneno ?? '' }}</li>
            </ul>
        </div>

        <div class="receipt-details-section">
            <h5>Receipt Details</h5>
            <p><strong>Particulars:</strong> {{ $receipt->particulars ?? 'Payment Received' }}</p>
            <p><strong>Voucher Type:</strong> {{ $receipt->voucher_type ?? 'Cash' }}</p>
            <p><strong>Amount:</strong> {{ $receipt->credit ?? '0.00' }}/-</p>
            <p><strong>Amount In Words:</strong>
                @if (!function_exists('convertNumberToWordsAllReceipts'))
                @php
                function convertNumberToWordsAllReceipts($num) {
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
                        $words .= convertNumberToWordsAllReceipts(floor($num / 10000000)) . " Crore ";
                        $num %= 10000000;
                    }

                    if ($num >= 100000) {
                        $words .= convertNumberToWordsAllReceipts(floor($num / 100000)) . " Lakh ";
                        $num %= 100000;
                    }

                    if ($num >= 1000) {
                        $words .= convertNumberToWordsAllReceipts(floor($num / 1000)) . " Thousand ";
                        $num %= 1000;
                    }

                    if ($num >= 100) {
                        $words .= convertNumberToWordsAllReceipts(floor($num / 100)) . " Hundred ";
                        $num %= 100;
                    }

                    if ($num >= 20) {
                        $words .= $tens[floor($num / 10)] . " ";
                        $num %= 10;
                    }

                    if ($num > 0) {
                        $words .= $ones[(int)$num] . " ";
                    }

                    return $words;
                }
                @endphp
                @endif
                {{ convertNumberToWordsAllReceipts($receipt->credit ?? 0) }} only/-
            </p>
            <p><strong>Notes:</strong> {{ $receipt->notes ?? '' }}</p>
        </div>

        <div class="signature-section">
            <div class="left">
                <p><strong>Payer's Signature:</strong> __________</p>
            </div>
            <div class="right">
                <p><strong>Receiver's Signature:</strong> ________________</p>
            </div>
        </div>

        <p class="footer-printed">Printed Time and Date: <span style="color: #4b4b4b;">{{ now()->format('Y-m-d H:i:s') }}</span></p>

        <p class="receipt-counter">Receipt {{ $index + 1 }} of {{ count($receipts) }}</p>

        <div class="total-due-box">
            Total Due Amount:
            <span class="total-due-amount ps-2">
                {{ number_format($receipt->totaldueamount ?? 0, 2) }}
            </span> -/
            <span style="font-size: 16px;"> (as of the date and time: {{ now()->format('Y-m-d H:i:s') }})</span>
        </div>

    </div>
    @if(!$loop->last)
    <div style="page-break-after: always;"></div>
    @endif
    @endforeach
</body>
</html>
