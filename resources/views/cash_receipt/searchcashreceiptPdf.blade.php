<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Print</title>
    
    <style>
        body {
            font-family: 'noto', sans-serif;
            margin: 0;
            padding: 0;
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
    margin-top: -30px;   /* Move up more */
    margin-bottom: 5px;
    font-size: 32px;
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
    font-size: 20px;
    line-height: 1.3;
    margin-top: 5px;
    margin-bottom: 0;
    padding: 0;
    color: #333;
}

.address-info p {
    margin: 2px 0; /* Balanced vertical spacing */
    padding: 0;
   
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
            font-size: 20px; /* Adjusted font size */
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            margin-bottom: 5px;
            font-size: 20px; /* Adjusted font size */
        }

        .receipt-details p {
            margin: 5px 0;
            font-size: 20px; /* Adjusted font size */
        }

        .signature-section {
            clear: both;
            margin-top: 250px; 
            display: flex;
            justify-content: space-between;
            font-size: 18px; /* Adjusted font size */
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
    top: -31px;
    right: 25px;
    font-size: 24px;
    text-align: right;
    line-hight: 1.5;
   
    padding: 10px 15px;
   
}

        @page {
            size: A5 landscape;
            margin: 30; /* Set margin to 0 for the page */
        }

        .cashrecipttext {
    font-size: 24px;
    font-weight: bold;
    font-family: Georgia, serif;
    color: #007bff;
    text-transform: uppercase;
    text-decoration: underline;
    margin-top: 12px;
    letter-spacing: 1px;
}

        /* Added CSS to float Receiver's Signature to the right */
        .receiver-signature {
            text-align: right;
        }
        .watermark {
            position: fixed;
            top: 45%; /* Adjust the vertical position */
            left: 35%; /* Adjust the horizontal position */
            transform: rotate(-45deg); /* Rotate the text */
            font-size: 148px;
            opacity: 0.1; /* Adjust the opacity */
            color: gray; /* Adjust the color */
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
                <p>Address: Tikapur, Kailali (In front of Tikapur Police Station)</p>
                <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
                
            </div>
            <p class="cashrecipttext">Cash Receipt</p>
        </div>
    
        <div class="top-right-info">
            @if (!empty($alldetails))
                <p><strong>Receipt No:</strong> {{ isset($alldetails[0]->id) ? $alldetails[0]->id : '' }}</p>
                <p><strong>Date:</strong> {{ isset($alldetails[0]->date) ? $alldetails[0]->date : '' }}</p>
            @endif
        </div>

    <div class="info-section">
        <h5>RECEIVED FROM</h5>
        <ul class="info-list">
            @foreach($customerinfodetails as $info)
                <li><strong>Name:</strong> {{$info->name}}</li>
                <li><strong>Address:</strong> {{$info->address}}</li>
                <li><strong>Email:</strong> {{$info->email}}</li>
                <li><strong>Contact No:</strong> {{$info->phoneno}} , {{$info->alternate_phoneno}}</li>
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
                                                                $words .= $ones[(int)$num] . " ";
                                                             }

                                            return $words;
                                        }

                                        // Retrieve the numerical value from your data
                                        $number = $data->credit;

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
<p style="font-size: 14px !important; margin-top:50px;">Printed Time and Date: <span style="color: #4b4b4b; font-size: 14px;"><?php echo date("Y-m-d H:i:s"); ?></span></p>

        <div style="margin-top: 100px; background-color: black;color:white; border: 1px solid black; padding: 10px;">
            Total Due Amount(बाकी): 

            <p style="font-family: 'noto'">This is a font test for Nepali: बाकी रकम</p>
            <p style="font-family: 'noto', Devanagari, sans-serif;">
                This is a font test for Nepali: बाकी रकम
            </p>
            <span class="forunderline fw-bold ps-2">
                {{-- {{ $dts - $cts }} -/ --}}
                <span style="
                font-size: 55px; font-weight: bold;
                
            ">
                {{ number_format($dts - $cts, 2) }}
            </span> -/
            
                            </span>
            <span style="font-size: 16px;"> ( as of the date and time: <?php echo date("Y-m-d H:i:s"); ?>) </span>
        </div>
</body>
</html>
