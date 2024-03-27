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
    {{-- <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
    </div>

    <div class="address-info">
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div> --}}

    <div class="invoice-info">

        <div class="container">
            <div class="card shadow p-4">
                <!-- Shop Name -->
                <h4 class="text-center mb-4">OHT</h4>
    
                /<h5 class="text-center mb-4">Cash Receipt</h5>
    
    
                <!-- Date -->
                <div class="text-right mb-4">
                    <strong>Date:</strong> {{ $alldetails[0]->date }} <!-- Assuming date is same for all details -->
                </div>
    
                <!-- Cash Receipt -->
    
                @if (isset($alldetails) && count($alldetails) > 0)
                @foreach ($alldetails as $data)
                <div class="mb-4">
                    <!-- Customer Information -->
                    <div class="mb-4">
                        <h6><strong>Customer Information</strong></h6>
                        <!-- Assuming $customerinfodetails is always available -->
                        @foreach($customerinfodetails as $info)
                        <p class="mb-1"><strong>Name:</strong> <span class="font-weight-bold">{{$info->name}}</span></p>
                        <p class="mb-1"><strong>Address:</strong> <span class="font-weight-bold">{{$info->address}}</span></p>
                        <p class="mb-1"><strong>Email:</strong> <span class="font-weight-bold">{{$info->email}}</span></p>
                        <p class="mb-1"><strong>Contact No:</strong> <span class="font-weight-bold">{{$info->phoneno}}</span></p>
                        @endforeach
                    </div>
    
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span><strong>Receipt No:</strong> {{ $data->id }}</span>
                    </div>
    
                    <!-- Receipt Details -->
                    <div class="mb-4">
                        <p class="mb-1"><strong>Date:</strong> {{ $data->date }}</p>
    
                        <p class="mb-1"><strong>Particulars:</strong> {{ $data->particulars }}</p>
                        <p class="mb-1"><strong>Voucher Type:</strong> {{ $data->voucher_type }}</p>
                        <p class="mb-1"><strong>Amount:</strong> {{ $data->credit }}</p>
                        <p class="mb-1"><strong>Amount In Words:</strong>   @php
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
                                $number = $data->credit;
    
                                // Convert the numerical value to words
                                $words = convertNumberToWords($number);
    
                                echo $words;
                            @endphp
                            only/-
    
                            </p>
                        <p class="mb-1"><strong>Added By:</strong> {{ $data->added_by }}</p>
                    </div>
    
                    <!-- Signatures -->
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Payer's Signature:</strong> _______________________</p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p><strong>Receiver's Signature:</strong> _______________________</p>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>

    
    
</div>
<script>

function openPdfInNewTab(event, url) {
        event.preventDefault();
        var newTab = window.open(url, '_blank');
        newTab.focus();
    }

    
</script>

</body>
</html>
