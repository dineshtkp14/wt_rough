@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 
    @yield('breadcrumb')
    <div class="container">
        
        <div class="card customer-card mb-4" id="customerCard" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Customer Info</h5>
                <p><span>ID:</span> <span id="customerId">...</span></p>
                <p class="card-text"><span>Name:</span> <span id="customerName">...</span></p>
                <p><span>Address:</span> <span id="customerAddress">...</span></p>
                <p><span>E-mail:</span> <span id="customerEmail">...</span></p>
                <p><span>PhoneNo:</span> <span id="customerPhone">...</span></p>
            </div>
            <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                <i class="fas fa-user"></i>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4">
            @foreach ($allcus as $i)
                <div>
                    <h5>Company Id: {{$i->id}}</h5> 
                    <h5>Name: {{$i->name}}</h5> 
                    <h5>Address: {{$i->address}}</h5>
                    <h5>Phone No: {{$i->phoneno}}</h5>
                    <h5>Email: {{$i->email}}</h5>
                    <h5>Email: {{$i->notes}}</h5>


                </div>
	        @endforeach
            </div>
            <div class="col-md-4">
                <h2 class="floatlft">
                    @php
                    $totaldue = $dts - $cts;
                @endphp
                
                @if($totaldue !== null && !empty($totaldue))
                <button class="btn btn-lg {{ $cts - $dts < 0 ? 'btn-danger' : 'btn-success' }}">
                    <span class="forunderline">Total Due Amount: <strong>{{ $totaldue }}</strong>/-</span>
                    </button>
                @endif
                </h2>
            </div>

           
                <div class="col-md-2 mb-4 " style="margin-top: -50px;">
                    <a href="{{ route('companyLedgerspay.create') }}" class="float-end btn btn-md btn-primary border border-5 border-warning" target="" rel="noopener noreferrer">
                        <i class="fas fa-money-bill-wave"></i> Company Ledger Payment
                    </a>  
                </div>
                <div class="col-md-2 mb-4 " style="margin-top: -50px;">
                    <a href="{{ route('companybillentry.create') }}" class="float-end btn btn-md btn-primary border border-5 border-danger me-3" target="" rel="noopener noreferrer">
                        <i class="fas fa-money-bill-wave"></i> Company Bill Entry
                    </a> 
                </div>
            
        


            <form action="{{ route('companyledgerdetails.returnchoosendatehistroy') }}" method="get" id="chosendatepdfform">
                <div class="row mb-3">
                    <div class="mb-4 col-md-4">
                        <div class="search-box">
                            <input id="customerIdInput" name="companyid" hidden>
                            <input required type="text" class="search-input @error('companyid') is-invalid @enderror" placeholder="Search company" id="searchCustomerInput" data-api="company_search" autocomplete="off">
                            @error('companyid')
                                <p class="invalid-feedback m-0">{{ $message }}</p>
                            @enderror
                            <i class="fas fa-search search-icon"></i>
                            <div class="result-wrapper" id="customerResultWrapper" style="display: none;">
                                <div class="result-box d-flex justify-content-start align-items-center" id="customerLoadingResultBox">
                                    <i class="fas fa-spinner" id="spinnerIcon"></i>
                                    <h1 class="m-0 px-2">Loading</h1>
                                </div>
                                <div class="result-box d-flex justify-content-start align-items-center d-none" id="customerNotFoundResultBox">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <h1 class="m-0 px-2">Record Not Found</h1>
                                </div>
                                <div id="customerResultList"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="visually-hidden" for="startDateInput">Choose Start Date</label>
                        <div class="input-group">
                            <div class="input-group-text">Start Date</div>
                            <input type="date" name="date1" class="form-control" id="startDateInput">
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="visually-hidden" for="endDateInput">Choose End Date</label>
                        <div class="input-group">
                            <div class="input-group-text">End Date</div>
                            <input type="date" name="date2" class="form-control" id="endDateInput">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <button type="submit" class="btn btn-dark w-100 p-3" style="font-size: 19px;">
                            <i class="fas fa-search"></i> <b>Search</b>
                        </button>
                    </div>
                    
                    <div class="col-md-2"> 
                        <a href="{{ route('companyledgerdetails.convert', ['companyid' => $companyid, 'date1' => $from, 'date2' => $to]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : '' }} border border-1 border-primary" id="pdfLink">
                            Print
                            <div class="icon-box d-flex justify-content-center align-items-center">
                                <i class="fa-solid fa-print"></i>
                            </div>
                            </a>
                    </div>
                    <div class="col-md-2 ">
                        <input autocomplete="off" class="form-control border-2 border-warning" id="filterInput" type="text" placeholder="Search Here">
                    </div>


                </div>
            </form>
        </div>

        

    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Date</th>
                <th>Date</th>

                <th>Particulars</th>
                <th>Voucher Type</th>
                <th>Bill No</th>
                <th>Debit</th>
                <th>Credit</th>
            </tr>
        </thead>
        <tbody>
            @if($all != null)
                @foreach ($all as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>{{ $i->date }}</td>
                       <td>{{ \App\Support\NepaliDate::adToBsString($forinvoicetype->date ?? now()->toDateString(), 'en') }}</td>

                        <td>{{ $i->particulars }}</td>
                        <td>{{ $i->voucher_type }}</td>
                        <td>{{ $i->voucher_no }}</td>
                        <td>{{ $i->debit }}</td>
                        <td>{{ $i->credit }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>@if($dts != null) Total: <strong>{{ $dts }}</strong></td> @endif
                    <td>@if($cts != null) Total: <strong>{{ $cts }}</strong></td> @endif
                </tr>
            @else
                <tr><td colspan="7">Record Not Found</td></tr>
            @endif
        </tbody>
    </table>

    <div class="col-12 d-flex justify-content-end align-items-center pt-4">
        <a href="{{ route('companyledgerdetails.convert', ['companyid' => $companyid, 'date1' => $from, 'date2' => $to]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : '' }} border border-1 border-primary" id="pdfLink">
            Print
            <div class="icon-box d-flex justify-content-center align-items-center">
                <i class="fa-solid fa-print"></i>
            </div>
        </a>
    </div>

    <h2 class="floatleft">
        <button class="btn btn-lg {{ $cts - $dts < 0 ? 'btn-danger' : 'btn-success' }}">
            <span class="forunderline">Total Due Amount: <strong>{{ $dts - $cts }} /-</strong>
             <span style="font-size: 14px;">
                ( 
               @php
              function convertNumberToWords($num) {
    $ones = array(
        "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
        "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"
    );
    $tens = array(
        "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
    );

    // Handle negative numbers
    if ($num < 0) {
        return "Minus " . convertNumberToWords(abs($num));
    }

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
$number = $dts - $cts;
// Convert the numerical value to words
$words = convertNumberToWords($number);

echo $words;

            @endphp
			only -/ )
			
        </span>
                
                </span>
        </button>
    </h2>

    <script>
        function openPdfInNewTab(event, url) {
            event.preventDefault();
            var newTab = window.open(url, '_blank');
            newTab.focus();
        }
    </script>

</div>
@stop
