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
	  <form action="{{ route('oldpricecheck') }}" method="get" id="chosendatepdfform">

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
			<div class="col-md-3">
				
			</div>
			
			<div class="col-md-6">
				@if (!empty($cid))
				<a href="{{ route('cpayments.create', [
					'customerid' => $cid,
					'amount' => $allnotcash - $cts,
					'totaldueamountfornotclear' => $allnotcash - $cts,
					'cname' => 
						($cusinfoforpdfok[0]->name ?? '') . ' | ' . 
						($cusinfoforpdfok[0]->address ?? '') . ' | ' . 
						($cusinfoforpdfok[0]->phoneno ?? '')
				]) }}" class="">
				</a>
			@endif
				
			</div>
		</div>

		<div class="row">
			
			<div class="col-md-2">
				<button type="submit" class="btn btn-dark mx-2 w-100" name="">
					<i class="fas fa-search"></i> Search 
				</button>
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
			CUSTOMER ID: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->id}}</span><br>
			NAME: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->name}}</span><br>
			ADDRESS: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->address}}</span><br>
			PHONE NO: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->phoneno}}, {{$i->alternate_phoneno}}</span><br>
			EMAIL: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->email}}</span><br>
			NOTES: <span style="font-size: 1.25rem; font-weight: 500;">{{$i->remarks}}</span><br>
			
			
		</div>
	@endforeach
  </div>






  <div class="col-md-3">
	
	<span> 
		

	
		
		
	</span>
	
  </div>

</div>
	








</div>

<BR>

@if(auth()->check() && auth()->user()->email == 'dineshtkp14@gmail.com')
    <h6 class="floatleft">Total Transaction Amount: <span class="forunderline">{{ $dts }} /-</span></h6>
@endif


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


<h2> -------------------------------table finals---------------------------------------- </h2>

<div class="container">
    <div class="card ">
        <div class="card-header">
            <div class="row "> 
                <div class="col-md-6 ">     
                    <a href="" style="width: 200px; text-decoration:none" class=" text-center  h3 text-dark"> ITEMSALES  TABLE</a>
                    <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-5" style="background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>
                </div>

                <div class="col-md-6 float-end">
                    <input type="text" class="form-control float-end border-warning border border-5" 
                        placeholder="Search Here" style="width: 250px;" 
                        wire:model.debounce.500ms="searchTerm" />
                </div>
            </div>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>

                        <th>Created at </th>
                        <th>Bill No</th>
                        <th>Name</th>
                        <th>Invoice Type</th>


                        <th>Items Name</th>
                        <th>Unstocked Name</th>
                        <th>Quantity</th>
                        <th>Cost Price</th>
                        <th>Original Sell Price</th>
                        <th>Sold Price</th>
                        <th>Sub-Total</th>
                        <th>Profit</th>
                        {{-- <th>Action</th> --}}

                    </tr>
                </thead>
                <tbody>
                    @if ($cus->isNotEmpty())
                        @foreach ($cus as $item)

                        <tr @if (date('Y-m-d', strtotime($item->date)) === date('Y-m-d')) style="font-weight:bold;color:white;background:red;" @endif>

                                <td data-label="Bill No">{{ $item->date }}</td>

                                <td data-label="Bill No">{{ $item->created_at }}</td>

                                <td data-label="Bill No">{{ $item->invoiceid }}</td>
                                <td data-label="Bill No">{{ $item->customername }}</td>
                                <td data-label="Bill No">{{ $item->inv_type }}</td>


                                <td data-label="Items Name">{{ $item->itemname ? $item->itemname : '-' }}</td>
                                <td data-label="Unstocked Name">{{ $item->unstockedname ? $item->unstockedname : '-' }}</td>
                                <td data-label="Quantity">{{ $item->quantity }}</td>
                                <td data-label="cost Price">{{ $item->itemdlp}}</td>

                                <td data-label="Originl Price">{{ $item->itemprice ? $item->itemprice : '-' }}</td>
                                <td data-label="sold Price">{{ $item->price }}</td>
                                <td data-label="Sub-Total">{{ $item->subtotal }}</td>
                                {{-- <td data-label="profit">{{ ($item->price-$item->itemdlp)*$item->quantity }}</td> --}}
                                <td data-label="Sub-Total">{{ !empty($item->itemdlp) ? ($item->price - $item->itemdlp) * $item->quantity : '-' }}  </td>
                              

                                {{-- <td><a href="{{ route('itemsales.edit', $item->id) }}" class="btn" style="background:#389AF5;color:white;">EDIT</a> </td> --}}
                               

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8"><h3>No Record Found.</h3></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted">
            {{ $cus->links() }}
        </div>
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

</div>


@stop