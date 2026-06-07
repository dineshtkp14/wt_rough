@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content" style="width: 100% !important; max-width: 100% !important;">
    @yield('breadcrumb')

    <div style="width: 100% !important; max-width: 100% !important; padding: 0 15px;">
        @if (Session::has('success'))
            <div class="alert bg-success text-white w-50">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::has('payment_whatsapp_url'))
            <div class="payment-share-panel">
                <div>
                    <strong>Payment message ready</strong>
                    <span>{{ Session::get('payment_whatsapp_message') }}</span>
                </div>
                <a href="{{ Session::get('payment_whatsapp_url') }}" target="_blank" class="payment-share-btn">
                    <i class="fa-brands fa-whatsapp"></i>
                    Send WhatsApp
                </a>
            </div>
        @endif
        <div class="card shadow p-4" style="width: 100% !important; max-width: 100% !important;">
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

                @if (Session::has('payment_sms_message'))
                    <div class="payment-sms-inline payment-sms-inline-{{ Session::get('payment_sms_status', 'success') }}" id="paymentSmsInlineMessage">
                        <strong>{{ Session::get('payment_sms_message') }}</strong>

                        @if (Session::get('payment_sms_status') === 'success' && Session::has('payment_sms_sent_text'))
                            <div class="payment-sms-inline-preview">
                                <span>SMS message sent</span>
                                <p>{{ Session::get('payment_sms_sent_text') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <!-- Print Buttons -->
            <div class="d-flex justify-content-end align-items-center pt-4 p-4 gap-4">
                <a href="{{ route('cashreceipt.convert', ['receiptno' => $receiptno]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ isset($alldetails) && count($alldetails) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink" style="font-size: 18px;">Print
                    <div class="icon-box d-flex justify-content-center align-items-center" style="font-size: 34px;">
                        <i class="fa-solid fa-print"></i>
                    </div>
                </a>
                @if(!empty($customerinfodetails) && isset($customerinfodetails[0]->id))
                <a href="{{ route('customer.printallcashreceipts', ['customerid' => $customerinfodetails[0]->id]) }}" onclick="openPdfInNewTab(event, this.href); return false;" style="font-size: 18px; margin-left: 20px;">Print All Receipts
                    <div class="icon-box d-flex justify-content-center align-items-center" style="font-size: 34px;">
                        <i class="fa-solid fa-print"></i>
                    </div>
                </a>
                @endif
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
                                <div class="col-md-6" style="max-width: 50%;">
                                    <h6 class="text-decoration-underline"><strong>RECEIVED FROM</strong></h6>

                                    @if (!empty($customerinfodetails))
                                        @foreach($customerinfodetails as $info)
                                            <p class="mb-1"><span>Name:</span> <span class="font-weight-bold">{{$info->name}}</span></p>
                                            <p class="mb-1"><span>Address:</span> <span class="font-weight-bold">{{$info->address}}</span></p>
                                            <p class="mb-1"><span>Email:</span> <span class="font-weight-bold">{{$info->email}}</span></p>
                                            <p class="mb-1"><span>Contact No:</span> <span class="font-weight-bold">{{$info->phoneno}}, {{$info->alternate_phoneno}}</span></p>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-md-6" style="max-width: 50%;">
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
                                                                $words .= $ones[(int)$num] . " ";
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
                            <div class="col-md-6 text-right" style="max-width: 50%;">
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

    document.addEventListener('DOMContentLoaded', function () {
        var smsMessage = document.getElementById('paymentSmsInlineMessage');

        if (smsMessage) {
            setTimeout(function () {
                smsMessage.classList.add('is-hiding');

                setTimeout(function () {
                    smsMessage.remove();
                }, 350);
            }, 10000);
        }
    });


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

<style>
    .payment-share-panel {
        align-items: center;
        background: #ecfdf5;
        border: 1px solid #86efac;
        border-radius: 8px;
        display: flex;
        gap: 14px;
        justify-content: space-between;
        margin-bottom: 14px;
        padding: 12px 14px;
    }

    .payment-share-panel strong,
    .payment-share-panel span {
        display: block;
    }

    .payment-share-panel strong {
        color: #14532d;
        font-size: 16px;
        font-weight: 900;
    }

    .payment-share-panel span {
        color: #166534;
        font-size: 14px;
        font-weight: 700;
        margin-top: 3px;
    }

    .payment-share-btn {
        align-items: center;
        background: #16a34a;
        border-radius: 8px;
        color: #ffffff !important;
        display: inline-flex;
        flex: 0 0 auto;
        font-weight: 900;
        gap: 7px;
        min-height: 42px;
        padding: 0 14px;
        text-decoration: none !important;
    }

    .payment-sms-inline {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        color: #111827;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
        margin-top: 8px;
        padding: 8px 10px;
        transition: opacity .35s ease, transform .35s ease;
    }

    .payment-sms-inline.is-hiding {
        opacity: 0;
        transform: translateY(-4px);
    }

    .payment-sms-inline-success {
        background: #ecfdf5;
        border-color: #86efac;
    }

    .payment-sms-inline-warning {
        background: #fffbeb;
        border-color: #fbbf24;
    }

    .payment-sms-inline-danger {
        background: #fef2f2;
        border-color: #fca5a5;
    }

    .payment-sms-inline-preview {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        margin-top: 8px;
        padding: 8px;
    }

    .payment-sms-inline-preview span,
    .payment-sms-inline-preview p {
        display: block;
    }

    .payment-sms-inline-preview span {
        color: #334155;
        font-size: 10px;
        font-weight: 900;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .payment-sms-inline-preview p {
        color: #0f172a;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.35;
        margin: 0;
    }

    @media (max-width: 700px) {
        .payment-share-panel {
            align-items: stretch;
            flex-direction: column;
        }

        .payment-share-btn {
            justify-content: center;
            width: 100%;
        }
    }
</style>
@stop
