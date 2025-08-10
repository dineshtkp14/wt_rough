@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 
	@yield('breadcrumb')
<div class="container">
	
    <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
        <div class="card-body">
            <h5 class="card-title">Customer Info</h5>
            <p>
                <span>ID: </span><span id="customerId">...</span>
            </p>
            <p class="card-text">
                <span>Name: </span><span id="customerName">...</span>
            </p>
            <p>
                <span>Addres: </span><span id="customerAddress">...</span>
            </p>
            <p>
                <span>E-mail: </span><span id="customerEmail">...</span>
            </p>
            <p>
                <span>PhoneNo: </span><span id="customerPhone">...</span>
            </p>
        </div>

        <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
            <i class="fas fa-user"></i>
        </div>
    </div>
	


	<div class="row">
		<div class="col-md-6 border border-5 border-success p-3">
			<div class="row">
				<form action="{{ route('clhs.returnchoosendatehistroy') }}" method="get" id="chosendatepdfform">

					<div class="col-md-12 ">
							<div class="search-box">
								<input id="customerIdInput" name="customerid" hidden>
			
								<input type="text" class="search-input @error('customerid') is-invalid @enderror" placeholder="Search Customer"
								id="searchCustomerInput" data-api="customer_search" autocomplete="off">
									@error('customerid')
										<p class="invalid-feedback m-0" style="position: absolute; bottom: -24px; left: 0;">{{ $message }}</p>
									@enderror  
									
								<i class="fas fa-search search-icon"> </i>
								<div class="result-wrapper" id="customerResultWrapper" style="display: none;">
									<div class="result-box d-flex justify-content-start align-items-center"
										id="customerLoadingResultBox">
										<i class="fas fa-spinner" id="spinnerIcon"> </i>
										<h1 class="m-0 px-2"> Loading</h1>
									</div>
			
									<div class="result-box d-flex justify-content-start align-items-center d-none"
										id="customerNotFoundResultBox">
										<i class="fas fa-triangle-exclamation"> </i>
										<h1 class="m-0 px-2"> Record Not Found</h1>
									</div>
			
									<div id="customerResultList">
									</div>
								</div>
								
							</div>
						<div class="row mt-3">
							<div class="col-md-6 ">
								<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
								<div class="input-group">
								<div class="input-group-text">Choose Start Date</div>
									<input type="date" name="date1" value="" class="form-control" id="inputs">
								</div>
							</div>
							
							<div class="col-md-6 ">
								<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
								<div class="input-group">
									<div class="input-group-text">Choose End Date</div>
										<input type="date" name="date2" value=""class="form-control">
								</div>
							</div>

							<div class="col-md-12 mt-3 ">
								<button type="submit" class="btn btn-dark w-100 py-3 h4">
									<span class="h4"><i class="fas fa-search mx-2"> </i>Search </span> 
								</button>								
							 </form>
							</div>

						</div>
					</div>
			</div>
			</form>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-8">
						@foreach ($cusinfobyid as $i)
								<div class="fw-bold">
									Name: {{$i->name}}<br>
									Address: {{$i->address}}<br>
									Phone No: {{$i->phoneno}}<br>
									Alternate Phoneno: {{$i->phoneno}}<br>
									Email: {{$i->email}}<br>
								</div>
						@endforeach

						<h1 class="mt-2 floatleft btn {{ $dts - $cts < 0 ? 'btn-danger' : 'btn-success' }}" style="padding-right: 10px;">
							Total Due Amount: okoko
							<span class="forunderline fw-bold ps-2">
								{{-- {{ $dts - $cts }} -/ --}}
								{{ number_format($dts - $cts, 2) }} -/

							</span>
						</h1>
				</div>
				<div class="col-md-4">
					<a href="{{ route('clhspdf.convert', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : '' }} border border-1 border-primary" id="pdfLink" style="padding: 10px 20px; font-size: 18px;">Print
						<div class="icon-box d-flex justify-content-center align-items-center">
							<i class="fa-solid fa-print"></i>
						</div>
					</a>
				</div>

			</div>
		</div>
		<div class="col-md-8"></div>
		<div class="col-md-4 mb-2">
			<input class="form-control  border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
		</div>
	</div>




<table>
	<thead>
		<tr>
			<th>#</th>

			<th>Id</th>
			
			<th>Date</th>
			<th>Created_at</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
			<th>Invoice Type</th>

			<th>Invoice No</th>
			<th>Debit</th>  
            <th>Credit</th>
           

			
		</tr>
	</thead>
	<tbody>
		@php $serial = 1 @endphp <!-- Initialize serial number variable -->

  
                       @if($all!=null)
					   @foreach ($all as $i)
					   <tr>
						<td>{{ $serial++ }}</td> <!-- Increment and display serial number -->

						   <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Id">{{ $i->date }}</td>

						   <td data-label="Name">{{ $i->created_at }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Remarks"> {{ $i->invoicetype }}
							@if($i->invoicetype == 'payment')
								<b>CR-({{ $i->id }}) </b>
							
							@endif
						</td>
						
						   <td data-label="Contact No.">{{ $i->invoiceid }}</td>
			   
						   <td data-label="Amount">{{ $i->debit }}</td>
						   
						  
						   <td data-label="Remarks">{{ $i->credit }}</td>
						   
						   
					   </tr>
					   
					   @endforeach
					   <tr>
						   <td>-</td>
						   <td>-</td>
			   
						   <td>-</td>
			   
						   <td>-</td>
						   <td>-</td>
						   <td>-</td>
						   <td>-</td>
						   <td>-</td>


			   
						   <td>
							   @if($dts!=null)
								   Total: <h2>{{$dts }}</h2></td>
							   @endif
						   </td>
			   
						   <td>
							   @if($cts!=null)
								   Total: <h2>{{$cts }}</h2></td>
							   @endif
						   </td>
			   
					   </tr>
					   
					  
					   @else
                       <h2>Record Not Found </h2>
					   @endif

    
	</tbody>
</table>

{{-- <div class="col-12 d-flex justify-content-end align-items-center pt-4">
	<a href="{{route('clhspdf.convert')}}" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : ''  }}" id="pdfLink">convert To PDF
	<div class="icon-box d-flex justify-content-center align-items-center">
	<i class="fa-solid fa-download"></i>
	</div>
	</a>
</div> --}}
</div>

{{-- <h2 class="floatleft">Total Due Amount: <span class="forunderline">{{ $dts - $cts }} /-</span></h2> --}}

<h1 class="floatleft btn {{ $dts - $cts < 0 ? 'btn-danger' : 'btn-success' }}" style="padding-right: 10px;">
    Total Due Amount: 
    <span class="forunderline fw-bold ps-2">
        {{-- {{ $dts - $cts }} -/ --}}
		{{ number_format($dts - $cts, 2) }} -/

    </span>
</h1>
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
			only -/ 
			
			)
{{-- //print --}}
<div class="col-12 d-flex justify-content-end align-items-center pt-4">
	<a href="{{ route('clhspdf.convert', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : '' }} border border-1 border-primary" id="pdfLink" style="padding: 10px 20px; font-size: 18px;">Print
		<div class="icon-box d-flex justify-content-center align-items-center">
			<i class="fa-solid fa-print"></i>
		</div>
	</a>
</div>

{{-- <script>
	document.getElementById('pdfLink').addEventListener('click', function(e) {
        e.preventDefault(); 
		var query=window.location.search;
		var param=new URLSearchParams(query);

        var url = "{{ route('clhspdf.convert') }}?customerid=" + param.get('customerid') + "&date1=" + param.get('date1') + "&date2=" + param.get('date2');
		window.location.href = url;

    });
</script> --}}

</div>
<script>
   

	function openPdfInNewTab(event, url) {
        event.preventDefault();
        var newTab = window.open(url, '_blank');
        newTab.focus();
    }

</script>


@stop