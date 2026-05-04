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
				]) }}" class="float-end btn btn-md btn-danger border border-5 border-warning">
					<i class="fas fa-money-bill-wave"></i>
					<b class="h5">CUSTOMER LEDGER PAYMENT</b>
				</a>
			@endif
				<a href="{{ route('chequedeposit.create') }}" class=" me-5 float-end btn btn-md btn-primary border border-5 border-danger" target="" rel="noopener noreferrer">
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


  <div class="col-md-3 mt-5">
	<br> <br> <br> 
	<h1 class="floatleft btn {{ $allnotcash - $cts < 0 ? 'btn-danger' : 'btn-success' }}" style="padding-right: 10px;">
		Total Due Amount: 
		<span class="forunderline fw-bold ps-2">
			{{-- {{ $allnotcash - $cts }} -/ --}}


			{{ number_format($allnotcash - $cts, 2) }} -/

		</span>
	</h1>
	
	
  </div>



  <div class="col-md-3">
	
	<span> 
		

		<div class="col-12 d-flex justify-content-end align-items-center pt-4 gap-3">
			<a href="{{ route('pdfreturnchoosendatehistroycashandcredit.convert', ['customerid' => $cid, 'date1' => $from, 'date2' => $to]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : '' }} border border-1 border-primary" id="pdfLink" style="padding: 10px 20px; font-size: 18px;">Print
				<div class="icon-box d-flex justify-content-center align-items-center">
					<i class="fa-solid fa-print"></i>
				</div>
			</a>
			<a href="{{ route('print.all.customer.invoices', ['customerid' => $cid, 'date1' => $from, 'date2' => $to]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : '' }} btn btn-md btn-success" style="padding: 10px 20px; font-size: 18px;">
				<i class="fa-solid fa-file-invoice"></i> Print All Invoices
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

			<th>Miti</th>
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
					   <tr @if($i->date == now()->toDateString()) style="background:red; color:white;" @endif>

						   <td data-label="Id">{{ $i->id }}</td>

						   {{-- <td class="ad-date"
						   data-ad="{{ \Carbon\Carbon::parse($i->date)->format('Y-m-d') }}"
						   data-lang="np"></td> --}}

						   {{-- <td data-label="Name">{{ $i->date }}</td> --}}
						   
						   	<td data-label="Name">{{ $i->date }}</td>
							

						  {{-- type en for englisg date np for nepali date --}}
						  <td data-label="date" class="label-nep">{{ \App\Support\NepaliDate::adToBsString($i->date ?? now()->toDateString(), 'en') }} </td>  

						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Contact No."><b>{{ $i->invoiceid }}

							@if(!empty($i->invoiceid))
							<button onclick="openInvoiceModal({{ $i->invoiceid }})" class="btn btn-sm bg-info text-white">View</button>
							@endif
							</td>

                           <td data-label="Remarks"> {{ $i->invoicetype }}
							@if($i->invoicetype == 'payment')
								<b>CR-({{ $i->id }}) </b>

								@if(!empty($i->invoicetype == 'payment'))
								<button onclick="openPaymentModal({{ $i->id }})" class="btn btn-sm bg-info text-white">View</button>
								@endif


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

@if(auth()->check() && auth()->user()->email == 'dineshtkp14@gmail.com')
    <h6 class="floatleft">Total Transaction Amount: <span class="forunderline">{{ $dts }} /-</span></h6>
@endif

<h1 class="floatleft btn btn-lg {{ $allnotcash - $cts < 0 ? 'btn-danger' : 'btn-success' }}">
    Total Due Amounttt: 
    <span class="forunderline">
        {{-- {{ $allnotcash - $cts }} -/ --}}
		{{ number_format($allnotcash - $cts, 2) }}

		
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





<!-- Invoice Modal -->
<div id="invoiceModal" class="invoice-modal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h3>Invoice Details</h3>
            <button class="invoice-modal-close" onclick="closeInvoiceModal()">&times;</button>
        </div>
        <div class="invoice-modal-body" id="invoiceModalBody"></div>
        <div class="invoice-modal-footer">
            <a id="invoicePrintLink" href="#" target="_blank" class="btn-print"><i class="fas fa-print"></i> Print PDF</a>
            <button class="btn-close-modal" onclick="closeInvoiceModal()">Close</button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="payment-modal">
    <div class="payment-modal-content">
        <div class="payment-modal-header">
            <h3>Payment Details</h3>
            <button class="payment-modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="payment-modal-body" id="paymentModalBody"></div>
        <div class="payment-modal-footer">
            <a id="paymentPrintLink" href="#" target="_blank" class="btn-print"><i class="fas fa-print"></i> Print Receipt</a>
            <button class="btn-close-modal" onclick="closePaymentModal()">Close</button>
        </div>
    </div>
</div>

<style>
.invoice-modal, .payment-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); overflow: auto; }
.invoice-modal-content, .payment-modal-content { background-color: #fff; margin: 20px auto; width: 90%; max-width: 900px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); }
.invoice-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; border-radius: 8px 8px 0 0; }
.payment-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 8px 8px 0 0; }
.invoice-modal-header h3, .payment-modal-header h3 { margin: 0; font-size: 1.25rem; }
.invoice-modal-close, .payment-modal-close { background: none; border: none; color: white; font-size: 28px; cursor: pointer; line-height: 1; }
.invoice-modal-body, .payment-modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
.invoice-modal-footer, .payment-modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 15px 20px; border-top: 1px solid #e5e7eb; background: #f9fafb; border-radius: 0 0 8px 8px; }
.btn-print, .btn-close-modal { padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-print { background: #f97316; color: white; border: none; }
.btn-print:hover { background: #ea580c; }
.btn-close-modal { background: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }
.invoice-display { font-family: 'Noto Sans', Arial, sans-serif; color: #1f2937; }
.invoice-display .inv-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 0.875rem; }
.invoice-display .inv-meta-right { text-align: right; }
.invoice-display .inv-badge { display: inline-block; background: #1f2937; color: white; padding: 4px 12px; font-size: 0.75rem; text-transform: uppercase; }
.invoice-display table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.875rem; }
.invoice-display th, .invoice-display td { border: 1px solid #1f2937; padding: 8px 10px; text-align: left; }
.invoice-display th { background: #f97316; color: white; font-weight: 600; }
.invoice-display .text-right { text-align: right; }
.invoice-display .total-row { font-weight: 700; background: #f9fafb; }
.payment-display { font-family: 'Noto Sans', Arial, sans-serif; color: #1f2937; }
.payment-display .receipt-header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 15px; margin-bottom: 20px; }
.payment-display .receipt-header h2 { font-size: 1.5rem; margin: 0; text-transform: uppercase; letter-spacing: 1px; color: #10b981; }
.payment-display .receipt-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 0.875rem; }
.payment-display .receipt-meta-right { text-align: right; }
.payment-display .receipt-badge { display: inline-block; background: #10b981; color: white; padding: 4px 12px; font-size: 0.75rem; text-transform: uppercase; border-radius: 4px; }
.payment-display .amount-box { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
.payment-display .amount-value { font-size: 2rem; font-weight: 700; }
.payment-display .payment-details { background: #f9fafb; padding: 15px; border-radius: 8px; margin-top: 20px; }
.payment-display .payment-details-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
.payment-display .payment-details-label { font-weight: 600; color: #4b5563; }
</style>

<script>
function openPdfInNewTab(event, url) {
    event.preventDefault();
    var newTab = window.open(url, '_blank');
    newTab.focus();
}

function openInvoiceModal(invoiceId) {
    const modal = document.getElementById('invoiceModal');
    const body = document.getElementById('invoiceModalBody');
    const printLink = document.getElementById('invoicePrintLink');
    printLink.href = '{{ url("billno/pdf/convert") }}?invoiceid=' + invoiceId;
    body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading invoice...</p></div>';
    modal.style.display = 'block';
    fetch('{{ route("api.invoice.data") }}?invoiceid=' + invoiceId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
    .then(data => {
        if (data.error) throw new Error(data.error);
        let html = '<div class="invoice-display">';
        html += '<div class="inv-meta">';
        html += '<div>';
        html += '<div style="margin-bottom: 8px;"><strong>INVOICE NO: ' + data.invoice_id + '</strong></div>';
        html += '<div style="margin-bottom: 4px;"><strong>Name:</strong> ' + (data.customer.name || 'N/A') + '</div>';
        html += '<div style="margin-bottom: 4px;"><strong>Address:</strong> ' + (data.customer.address || 'N/A') + '</div>';
        if (data.customer.phoneno) html += '<div style="margin-bottom: 4px;"><strong>Contact:</strong> ' + data.customer.phoneno + '</div>';
        html += '<div><strong>Customer Id:</strong> ' + (data.customer.id || 'N/A') + '</div>';
        html += '</div>';
        html += '<div class="inv-meta-right">';
        html += '<span class="inv-badge">INVOICE TYPE: ' + (data.type || 'credit').toUpperCase() + '</span>';
        html += '<div style="margin-top: 15px;">';
        html += '<div style="margin-bottom: 4px;"><strong>Date:</strong> ' + data.date + '</div>';
        html += '<div><strong>Miti:</strong> ' + (data.nepali_date || '') + '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '<table><thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Price</th><th>Amount</th></tr></thead><tbody>';
        data.items.forEach((item, i) => {
            html += '<tr><td>' + (i+1) + '</td><td>' + (item.item_name || '') + '</td><td>' + (item.quantity || '') + '</td><td>' + (item.price || '') + '</td><td>' + (item.subtotal || '') + '</td></tr>';
        });
        html += '<tr class="total-row"><td colspan="3"></td><td class="text-right"><strong>Total:</strong></td><td><strong>Rs ' + parseFloat(data.total || 0).toFixed(2) + '</strong></td></tr>';
        html += '</tbody></table>';
        html += '<div class="footer-info" style="margin-top: 15px; font-size: 0.875rem; color: #6b7280;"><p>Bill Created by: ' + (data.added_by || 'System') + '</p></div>';
        html += '</div>';
        body.innerHTML = html;
    })
    .catch(error => {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;"><i class="fas fa-exclamation-circle fa-2x"></i><p>Error: ' + error.message + '</p></div>';
    });
}
function closeInvoiceModal() { document.getElementById('invoiceModal').style.display = 'none'; }

function openPaymentModal(paymentId) {
    const modal = document.getElementById('paymentModal');
    const body = document.getElementById('paymentModalBody');
    const printLink = document.getElementById('paymentPrintLink');
    printLink.href = '{{ route("cashreceipt.convert") }}?receiptno=' + paymentId;
    body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading payment...</p></div>';
    modal.style.display = 'block';
    fetch('{{ route("api.payment.data") }}?paymentid=' + paymentId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
    .then(data => {
        if (data.error) throw new Error(data.error);
        let html = '<div class="payment-display">';
        html += '<div class="receipt-header"><h2>Payment Receipt</h2></div>';
        html += '<div class="receipt-meta">';
        html += '<div>';
        html += '<div style="margin-bottom: 4px;"><strong>Receipt No:</strong> ' + data.receipt_no + '</div>';
        html += '<div style="margin-bottom: 4px;"><strong>Customer:</strong> ' + (data.customer.name || 'N/A') + '</div>';
        html += '<div><strong>Address:</strong> ' + (data.customer.address || 'N/A') + '</div>';
        html += '</div>';
        html += '<div class="receipt-meta-right">';
        html += '<span class="receipt-badge">' + data.mode.toUpperCase() + '</span>';
        html += '<div style="margin-top: 15px;">';
        html += '<div style="margin-bottom: 4px;"><strong>Date:</strong> ' + data.date + '</div>';
        html += '<div><strong>Miti:</strong> ' + (data.nepali_date || '') + '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '<div class="amount-box"><div>Amount Received</div><div class="amount-value">Rs ' + parseFloat(data.amount || 0).toFixed(2) + '</div></div>';
        html += '<div class="payment-details">';
        html += '<div class="payment-details-row"><span class="payment-details-label">Payment Mode:</span><span>' + data.mode + '</span></div>';
        html += '<div class="payment-details-row"><span class="payment-details-label">Customer ID:</span><span>' + (data.customer.id || 'N/A') + '</span></div>';
        html += '</div></div>';
        body.innerHTML = html;
    })
    .catch(error => {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;"><i class="fas fa-exclamation-circle fa-2x"></i><p>Error: ' + error.message + '</p></div>';
    });
}
function closePaymentModal() { document.getElementById('paymentModal').style.display = 'none'; }

window.onclick = function(event) { if (event.target === document.getElementById('invoiceModal')) closeInvoiceModal(); if (event.target === document.getElementById('paymentModal')) closePaymentModal(); }
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeInvoiceModal(); closePaymentModal(); } });
</script>

</div>

@stop