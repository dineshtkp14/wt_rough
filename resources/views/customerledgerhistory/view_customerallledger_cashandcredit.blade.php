@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 
	@yield('breadcrumb')
<div class="container">

    <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
        <div class="card-body">
            <h5 class="card-title">Customer Information</h5>
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
	  <form action="{{ route('returnchoosendatehistroycashandcredit') }}" method="get" id="chosendatepdfform">

		<div class="row">
			<div class="mb-4" style="width: 300px;">
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
			</div>
			<div class="col-md-3"></div>
			
			<div class="col-md-6">
				<a href="{{ route('cpayments.create') }}" class="float-end btn btn-md btn-primary border border-5 border-warning" target="" rel="noopener noreferrer">
					<i class="fas fa-money-bill-wave"></i> <!-- Icon for money or payment -->
					Customer Ledger Payment
				</a>

				<a href="{{ route('chequedeposit.create') }}" class=" me-5 float-end btn btn-md btn-dark border border-5 border-warning" target="" rel="noopener noreferrer">
					<i class="fas fa-money-bill-wave"></i> <!-- Icon for money or payment -->
					Cheque Deposit
				</a>
			</div>
		</div>

		<div class="row">
			<div class="col-md-3 mb-3">
				<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
				<div class="input-group">
				  <div class="input-group-text">Choose Start Date</div>
					<input type="date" name="date1" value="" class="form-control" id="inputs">
				</div>
			</div>

			<div class="col-md-3 mb-3">
				<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
				<div class="input-group">
			  		<div class="input-group-text">Choose End Date</div>
						<input type="date" name="date2" value=""class="form-control">
				</div>
			</div>
			<div class="col-md-1">
				<input type="submit" class="btn btn-success mx-2" name="" value="Search">
				 </form>
			</div>
			<div class="col-md-3 mt-3 mt-md-0">
				<div class="float-lg-end">
					<input class="form-control  border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
				</div>
			</div>

		</div>

	  </form>
	</div>

<div class="row">
  <div class="col-md-5">
	@foreach ($cusinfoforpdfok as $i)
		<div>
			<h5>Customer Id: {{$i->id}}</h5> 
			<h5>Name: {{$i->name}}</h5> 
			<h5>Address: {{$i->address}}</h5>
			<h5>Phone No: {{$i->phoneno}}</h5>
			<h5>Email: {{$i->email}}</h5>

		</div>
	@endforeach
  </div>


  <div class="col-md-3 mt-5">
	<br> <br> <br> 
	<h1 class="floatleft btn {{ $allnotcash - $cts < 0 ? 'btn-danger' : 'btn-success' }}" style="padding-right: 10px;">
		Total Due Amount: 
		<span class="forunderline fw-bold ps-2">
			{{ $allnotcash - $cts }} -/
		</span>
	</h1>
	
	
  </div>



  <div class="col-md-3">
	
	<span> 
		

		<div class="col-12 d-flex justify-content-end align-items-center pt-4">
			<a href="{{ route('pdfreturnchoosendatehistroycashandcredit.convert', ['customerid' => $cid, 'date1' => $from, 'date2' => $to]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : '' }} border border-1 border-primary" id="pdfLink" style="padding: 10px 20px; font-size: 18px;">Print
				<div class="icon-box d-flex justify-content-center align-items-center">
					<i class="fa-solid fa-print"></i>
				</div>
			</a>
		</div>
		
		
	</span>
	
  </div>

</div>
	






<table>
	<thead>
		<tr>
			<th>ID</th>
			<th>DATE</th>
			<th>PARTICULARS</th>
			<th>VOUCHER TYPE</th>
			<th>INVOICE NO</th>
            <th>INVOICE TYPE</th>
            <th>SALES RETURN </th>
            <th>CREDIT NOTES INVOICE NO</th>
			<th>DEBIT</th>  
            <th>CREDIT</th>
            <th>CN INVOICE NO</th>

		</tr>
	</thead>
	<tbody>
        
  
                    @if($all!=null)
					   @foreach ($all as $i)
					   <tr>
						   <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Name">{{ $i->date }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Contact No.">{{ $i->invoiceid }}</td>

                           <td data-label="Remarks"> {{ $i->invoicetype }}
							@if($i->invoicetype == 'payment')
								<b>CR-({{ $i->id }}) </b>
							
							@endif
						</td>
						
	                       <td data-label="Remarks">{{ $i->salesreturn }}</td>
                           <td data-label="Remarks">{{ $i->returnidforcreditnotes }}</td>
						   <td data-label="Amount">{{ $i->debit }}</td>
						   <td data-label="Remarks">{{ $i->credit }}</td> 
						   <td data-label="Remarks">{{ $i->cninvoiceid }}</td> 
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
								   Total(Only Credit): <h2>{{$allnotcash }}</h2></td>
							   @endif
						   </td>
			   
						   <td>
							   @if($cts!=null)
								   Total: <h2>{{$cts }}</h2></td>
							   @endif
						   </td>

						   <td>-</td>

			   
					   </tr>
					   
					  
					   @else
                       <h2>Record Not Found </h2>
					   @endif
    
	</tbody>
</table>


</div>

<BR>

<h5 class="floatleft">Total Transcation Amount: <span class="forunderline">{{$dts}} /-</span></h5>

<h1 class="floatleft btn btn-lg {{ $allnotcash - $cts < 0 ? 'btn-danger' : 'btn-success' }}">
    Total Due Amount: 
    <span class="forunderline">
        {{ $allnotcash - $cts }} -/
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
$number = $allnotcash - $cts;
// Convert the numerical value to words
$words = convertNumberToWords($number);

echo $words;

            @endphp
			only -/ 
			
			)

			{{$all->links()}}


<h2> --------------------------------Credit Notes Details---------------------------------------- </h2>


<table>
	<thead>
		<tr>
			<th>Id</th>
			<th>Date</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
			<th>CN Invoice NO</th>
			<th>Credit</th> 
		</tr>
	</thead>
	<tbody>
        
  
                    @if($creditnoteledger!=null)
					   @foreach ($creditnoteledger as $i)
					   <tr>
						   <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Name">{{ $i->date }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Contact No.">{{ $i->invoiceid }}</td>
						   <td data-label="Amount">{{ $i->debit }}</td>	   
					   </tr>
					   
					   @endforeach
					   <tr>
						  


						  
			   
						   
						 
						   <td>-</td>	
						   <td>-</td>

			   
						   <td>-</td>
						   <td>-</td>
						   <td>-</td>
					


			   
						   <td>
							   @if($debittotalcrnotes!=null)
								   Total: <h2>{{$debittotalcrnotes }}</h2></td>
							   @endif
						   </td>
			   
						
			   
					   </tr>
					   
					  
					   @else
                       <h2>Record Not Found </h2>
					   @endif
    
	</tbody>
</table>





</div>





<script>

	
function openPdfInNewTab(event, url) {
        event.preventDefault();
        var newTab = window.open(url, '_blank');
        newTab.focus();
    }


</script>

</div>


@stop