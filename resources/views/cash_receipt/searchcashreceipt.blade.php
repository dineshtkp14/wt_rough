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
            <h4 class="text-center mb-4">OHT</h4>

            <!-- Cash Receipt -->
            <h5 class="text-center mb-4">Cash Receipt</h5>

            <div class="card-body">
                <h5 class="card-title mb-4">Search Receipt No</h5>
                <form action="{{ route('cashreceipt.search') }}" method="get" id="searchForm">
                    <div class="input-group mb-3">
                        <input type="number" autocomplete="off" class="form-control" id="receiptno" name="receiptno" placeholder="Enter Receipt No" required>
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>

            <!-- Print Button -->
            <div class="col-12 d-flex justify-content-end align-items-center pt-4 p-4">
                <a href="{{ route('cashreceipt.convert', ['receiptno' => $receiptno]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ isset($alldetails) && count($alldetails) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink" style="font-size: 18px;">Print
                    <div class="icon-box d-flex justify-content-center align-items-center" style="font-size: 34px;">
                        <i class="fa-solid fa-print"></i>
                    </div>
                </a>
            </div>

            <!-- Date -->
            <div class="text-right mb-4">
                @if (!empty($alldetails))
                    <strong>Date:</strong> {{ isset($alldetails[0]->date) ? $alldetails[0]->date : '' }}
                @endif
            </div>

            <!-- Cash Receipt Details -->
            @if (!empty($alldetails))
                @foreach ($alldetails as $data)
                    <div class="mb-4">
                        <!-- Customer Information -->
                        <div class="mb-4">
                            <h6><strong>Customer Information</strong></h6>
                            <!-- Assuming $customerinfodetails is always available -->
                            @if (!empty($customerinfodetails))
                                @foreach($customerinfodetails as $info)
                                    <p class="mb-1"><strong>Name:</strong> <span class="font-weight-bold">{{$info->name}}</span></p>
                                    <p class="mb-1"><strong>Address:</strong> <span class="font-weight-bold">{{$info->address}}</span></p>
                                    <p class="mb-1"><strong>Email:</strong> <span class="font-weight-bold">{{$info->email}}</span></p>
                                    <p class="mb-1"><strong>Contact No:</strong> <span class="font-weight-bold">{{$info->phoneno}}</span></p>
                                @endforeach
                            @endif
                        </div>

                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span><strong>Receipt No:</strong> {{ $data->id }}</span>
                        </div>

                        <!-- Receipt Details -->
                        <div class="mb-4">
                            @if (isset($data->date))
                                <p class="mb-1"><strong>Date:</strong> {{ $data->date }}</p>
                            @endif
                            <p class="mb-1"><strong>Particulars:</strong> {{ $data->particulars ?? '' }}</p>
                            <p class="mb-1"><strong>Voucher Type:</strong> {{ $data->voucher_type ?? '' }}</p>
                            <p class="mb-1"><strong>Amount:</strong> {{ $data->credit ?? '' }}</p>
                            <!-- Amount In Words: Use your conversion function here -->
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
@stop

