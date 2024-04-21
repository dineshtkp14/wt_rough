@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container">
        @if (Session::has('success'))
            <div class="alert bg-success text-white w-50">
                {{ Session::get('success') }}
            </div>
        @endif
        <div class="card shadow p-4">
            <!-- Shop Name -->
          
            <!-- Cash Receipt -->
           
            
            <div class="card-body">
                <!-- Search Receipt Form -->
                <h5 class="card-title mb-4">Search Receipt No</h5>
                <form action="{{ route('cashreceipt.search') }}" method="get" id="searchForm">
                    <div class="input-group mb-3">
                        <input type="number" autocomplete="off" class="form-control" id="receiptno" name="receiptno" placeholder="Enter Receipt No" required>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
            <!-- Print Button -->
            <div class="d-flex justify-content-end align-items-center pt-4 p-4">
                <a href="{{ route('cashreceipt.convert', ['receiptno' => $receiptno]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ isset($alldetails) && count($alldetails) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink" style="font-size: 18px;">Print
                    <div class="icon-box d-flex justify-content-center align-items-center" style="font-size: 34px;">
                        <i class="fa-solid fa-print"></i>
                    </div>
                </a>
            </div>
            <!-- CASH RECEIPT Heading -->
            <h3 class="text-center"><strong>CASH RECEIPT</strong></h3>
            @if (Session::has('success'))

            
            <form action="{{ route('cpayments.destroy', isset($alldetails[0]->id) ? $alldetails[0]->id : '') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> <!-- Font Awesome trash icon -->
                    Delete
                </button>
            </form>
            

            @endif
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
                                        <p class="mb-1"><strong>Particulars:</strong> {{ $data->particulars ?? '' }}</p>
                                        <p class="mb-1"><strong>Voucher Type:</strong> {{ $data->voucher_type ?? '' }}</p>
                                        <p class="mb-1"><strong>Amount:</strong> {{ $data->credit ?? '' }}  /- </p>
                                        <p class="mb-1"><strong>In Words:</strong>
                                               
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
                                                    only /-
                                        </p>  
                                        <p class="mb-1"><strong>Notes:</strong> {{ $data->notes ?? '' }}   </p>
  

                                        </div>
                                </div>
                            </div>
                            <!-- Assuming $customerinfodetails is always available -->
                        </div>
                        <!-- Signatures -->
                        <div class="row">
                            <div class="col-md-6">
                                {{-- <p><strong>Payer's Signature:</strong> _______________________</p> --}}
                            </div>
                            <div class="col-md-6 text-right">
                                <p><strong>Receiver's Signature:</strong> _______________________</p>
                                    @if(auth()->check())
                                        <span class="ms-5" style="margin-top: -10px !important;">{{ auth()->user()->name }}</span>
                                    @endif

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


     // Attach click event listener to the edit button
     document.getElementById('editButton').addEventListener('click', function(event) {
        // Prevent the default behavior of the link
        event.preventDefault();

        // Make an AJAX request to set the session
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/set_session', true); // Replace '/set_session' with your actual endpoint
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Session set successfully, redirect the user
                window.location.href = "{{ route('cpayments.index') }}";
            } else {
                // Handle errors if needed
                console.error('Error setting session:', xhr.statusText);
            }
        };
        xhr.onerror = function() {
            // Handle errors if needed
            console.error('Network error while setting session');
        };
        xhr.send(JSON.stringify({ /* Any data you want to send to the server */ }));
    });
</script>
@stop
