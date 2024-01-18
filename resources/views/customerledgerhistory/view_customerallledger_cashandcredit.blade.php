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
		</div>
<script>

</script>
		<div class="row">
			<div class="col-md-4 mb-3">
				<label class="visually-hidden" for="specificSizeInputGroupUsername">Username</label>
				<div class="input-group">
				  <div class="input-group-text">Choose Start Date</div>
					<input type="date" name="date1" value="" class="form-control" id="inputs">
				</div>
			</div>

			<div class="col-md-4 mb-3">
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


<table>
	<thead>
		<tr>
			<th>Id</th>
			
			<th>Date</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
			<th>Invoice ID</th>
            <th>Invoice Type</th>
            <th>Sales Return</th>
            <th>Credit Notes Invoice Id</th>


			<th>Debit</th>  
            <th>Credit</th>

           

			
		</tr>
	</thead>
	<tbody>
        
  
                    @if($all!=null)
					   @foreach ($all as $i)
					   <tr>
						   <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Name">{{ $i->created_at }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Contact No.">{{ $i->invoiceid }}</td>
                           <td data-label="Remarks">{{ $i->invoicetype }}</td>
	                       <td data-label="Remarks">{{ $i->salesreturn }}</td>
                           <td data-label="Remarks">{{ $i->returnidforcreditnotes }}</td>



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
								   Total(only cash): <h2>{{$allnotcash }}</h2></td>
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

<div class="col-12 d-flex justify-content-end align-items-center pt-4">
	{{-- <a href="{{route('clhspdf.convert')}}" class="{{ count($all) <= 0 ? 'pdf-link-disabled' : ''  }}" id="pdfLink">convert To PDF --}}
	<div class="icon-box d-flex justify-content-center align-items-center">
	<i class="fa-solid fa-download"></i>
	</div>
	</a>
</div>
</div>

{{-- <h2 class="floatleft">Total Due Amount: <span class="forunderline">{{ $dts - $cts }} /-</span></h2> --}}


<h4 class="floatleft">Total Transcation Amount: <span class="forunderline">{{$dts}} /-</span></h4>

<h1 class="floatleft">Total Due Amount: <span class="forunderline">{{$allnotcash - $cts}} /-</span></h1>




<h2> --------------------------------Credit Notes Details---------------------------------------- </h2>


<table>
	<thead>
		<tr>
			<th>Id</th>
			<th>Date</th>
			<th>Particulars</th>
			<th>Voucher Type</th>
			<th>Invoice ID</th>
            <th>Invoice Type</th>
			<th>Debit</th>  

           

			
		</tr>
	</thead>
	<tbody>
        
  
                    @if($creditnoteledger!=null)
					   @foreach ($creditnoteledger as $i)
					   <tr>
						   <td data-label="Id">{{ $i->id }}</td>
						   <td data-label="Name">{{ $i->created_at }}</td>
						   <td data-label="Address">{{ $i->particulars}}</td>
						   <td data-label="Contact No.">{{ $i->voucher_type }}</td>
						   <td data-label="Contact No.">{{ $i->invoiceid }}</td>
                           <td data-label="Remarks">{{ $i->invoicetype }}</td>
						   <td data-label="Amount">{{ $i->debit }}</td>
						   
						  


						   
					   </tr>
					   
					   @endforeach
					   <tr>
						  


						  
			   
						   
						 
						   <td>-</td>	
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
			   
						   {{-- <td>
							   @if($cts!=null)
								   Total: <h2>{{$cts }}</h2></td>
							   @endif
						   </td> --}}
			   
					   </tr>
					   
					  
					   @else
                       <h2>Record Not Found </h2>
					   @endif
    
	</tbody>
</table>













<script>
	document.getElementById('pdfLink').addEventListener('click', function(e) {
        e.preventDefault(); 
		var query=window.location.search;
		var param=new URLSearchParams(query);

        var url = "{{ route('clhspdf.convert') }}?customerid=" + param.get('customerid') + "&date1=" + param.get('date1') + "&date2=" + param.get('date2');
		window.location.href = url;

    });
</script>

</div>
<script>
    console.log(convertNumberToWords({{ $dts - $cts }}));
    console.log("dinesh");
</script>


@stop