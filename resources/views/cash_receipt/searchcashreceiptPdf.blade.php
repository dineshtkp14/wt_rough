<!DOCTYPE html>
<html>
<head>
    <title>Print</title>
    <script src="{{ asset('assets/js/common.js') }}"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h3 {
            margin: 0;
            color: #007bff;
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
            margin-top: 20px;
        }

        .info-section,
        .receipt-details {
            float: left;
            width: 50%;
            box-sizing: border-box;
            padding: 0 10px;
        }
/* //comment */
        .info-section h5,
        .receipt-details h5 {
            color: #007bff;
            margin-bottom: 10px;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            margin-bottom: 5px;
        }

        .receipt-details p {
            margin: 5px 0;
        }

        .signature-section {
            clear: both;
            margin-top: 300px;
            display: flex;
            justify-content: space-between;
        }

        .signature-section div {
            flex: 1;
            display: flex;
            align-items: baseline;
        }

        .signature-section p {
            margin: 0;
        }

        .text-right {
            text-align: right;
        }

        .top-right-info {
            position: absolute;
            top: 200px;
            right: 20px;
        }
        
        /* Added CSS to float Receiver's Signature to the right */
        .receiver-signature {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div class="letterhead">
            <h1>OM HARI TRADELINK</h1>
        </div>
    
        <div class="address-info">
            <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
            <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
        </div>
    </div>

    <div class="top-right-info">
        @if (!empty($alldetails))
            <p><strong>Date:</strong> {{ isset($alldetails[0]->date) ? $alldetails[0]->date : '' }}</p>
            <p><strong>Receipt No:</strong> {{ isset($alldetails[0]->id) ? $alldetails[0]->id : '' }}</p>
        @endif
    </div>

    <div class="info-section">
        <h5>RECEIVED FROM</h5>
        <ul class="info-list">
            @foreach($customerinfodetails as $info)
                <li><strong>Name:</strong> {{$info->name}}</li>
                <li><strong>Address:</strong> {{$info->address}}</li>
                <li><strong>Email:</strong> {{$info->email}}</li>
                <li><strong>Contact No:</strong> {{$info->phoneno}}</li>
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
                                                        $number = $data->credit ?? '' ;

                                                        // Convert the numerical value to words
                                                        $words = convertNumberToWords($number);

                                                        echo $words;
                                                    @endphp
                                                    only/-
            </p>
            <p><strong>Notes:</strong> {{$data->notes}}</p>
        @endforeach
    </div>
</div>

<div class="signature-section">
    <div>
        <p><strong>Payer's Signature:</strong> __________</p>
    </div>
    <div class="receiver-signature">
        <p><strong>Receiver's Signature:</strong> ________________</p>
    </div>
</div>

</body>
</html>
