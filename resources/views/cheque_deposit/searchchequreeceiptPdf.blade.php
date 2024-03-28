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

    <div class="container">
        @if (Session::has('success'))
            <div class="alert bg-success text-white w-50">
                {{ Session::get('success') }}
            </div>
        @endif
        <div class="card shadow p-4">
            <!-- Shop Name -->
            <h4 class="text-center mb-4">OHT</h4>
            <!-- Cash Receipt -->

                      
              
              <div class="card-body">
                <!-- Search Receipt Form -->
                <h5 class="card-title mb-4">Search Receipt No</h5>
                <form action="{{ route('chequereceipt.search') }}" method="get" id="searchForm">
                    <div class="input-group mb-3">
                        <input type="number" autocomplete="off" class="form-control" id="receiptno" name="receiptno" placeholder="Enter Receipt No" required>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
            <!-- Print Button -->
            <div class="d-flex justify-content-end align-items-center pt-4 p-4">
                <a href="{{ route('chequereceipt.convert', ['receiptno' => $receiptno]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ isset($alldetails) && count($alldetails) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink" style="font-size: 18px;">Print
                    <div class="icon-box d-flex justify-content-center align-items-center" style="font-size: 34px;">
                        <i class="fa-solid fa-print"></i>
                    </div>
                </a>
            </div>
            <!-- CASH RECEIPT Heading -->
            <div class="text-center mb-4">
                <i class="fas fa-money-check-alt fa-3x"></i> <!-- Cheque icon -->
               <h5 class="mb-0">Cheque Receipt</h5>
             </div> 
            <!-- Date and Receipt No at Top Right -->
            <div class="d-flex justify-content-end mb-4">
                <div class="mr-3">
                    @if (!empty($alldetails))
                        <strong>Date:</strong> {{ isset($alldetails[0]->date) ? $alldetails[0]->date : '' }} <br>
                        <strong>Receipt No:</strong> {{ isset($alldetails[0]->id) ? $alldetails[0]->id : '' }}
                    @endif
                </div>
            </div>
            <!-- Cash Receipt Details -->
            @if (!empty($alldetails))
                @foreach ($alldetails as $data)
                    <div class="mb-4">
                        <!-- Customer Information -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-decoration-underline"><strong>RECEIVED FROM</strong></h6>

                                    @if (!empty($customerinfodetails))
                                        @foreach($customerinfodetails as $info)
                                            <p class="mb-1"><span>Name:</span> <span class="font-weight-bold">{{$info->name}}</span></p>
                                            <p class="mb-1"><span>Address:</span> <span class="font-weight-bold">{{$info->address}}</span></p>
                                            <p class="mb-1"><span>Email:</span> <span class="font-weight-bold">{{$info->email}}</span></p>
                                            <p class="mb-1"><span>Contact No:</span> <span class="font-weight-bold">{{$info->phoneno}}</span></p>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        @if (isset($data->date))
                                        @endif
                                        <p class="mb-1"><strong>Cheque Date:</strong> {{ $data->cheque_date ?? '' }}</p>
                                        <p class="mb-1"><strong>Bank Name:</strong> {{ $data->bank_name ?? '' }} </p>
                                        <p class="mb-1"><strong>Amount:</strong> {{ $data->amount ?? '' }} </p>
                                        <p class="mb-1"><strong>Notes :</strong> {{ $data->notes ?? '' }}</p>

                                        </div>
                                </div>
                            </div>
                            <!-- Assuming $customerinfodetails is always available -->
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
<script>

function openPdfInNewTab(event, url) {
        event.preventDefault();
        var newTab = window.open(url, '_blank');
        newTab.focus();
    }

    
</script>

</body>
</html>
