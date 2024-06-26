@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content">

    
    @yield('breadcrumb')

    @if (auth()->check() && auth()->user()->email !== 'dineshtkp14@gmail.com')
        <script> window.location.href = "{{ route('login') }}";   </script>
    @endif

    
    <div class="container">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::has('updateerror'))
                        <div class="alert bg-danger text-white">
                            {{ Session::get('updateerror') }}
                        </div>
                    @endif
                    @if (Session::has('updatesuccess'))
                    <div class="alert bg-success text-white">
                        {{ Session::get('updatesuccess') }}
                    </div>
                @endif



                @if (Session::has('error'))
                <div class="alert bg-danger text-white">
                    {{ Session::get('error') }}
                </div>
            @endif
            @if (Session::has('deletesuccess'))
            <div class="alert bg-success text-white">
                {{ Session::get('deletesuccess') }}
            </div>
        @endif

        @if (Session::has('updateerrorcusname'))
            <div class="alert bg-danger text-white">
                {{ Session::get('updateerrorcusname') }}
            </div>
        @endif
        @if (Session::has('updatesuccesscusname'))
        <div class="alert bg-success text-white">
            {{ Session::get('updatesuccesscusname') }}
        </div>
    @endif

</div>
<a href="{{ route('itemsales.create') }}" class="btn btn-primary float-end me-5" style="margin-top: -100px; background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>



<div class="container">


    <span class="thisisforhideandshow">


        <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">SEARCH INVOICE</h5>
                    <form action="{{ route('customer.billno') }}" method="get" id="chosendatepdfform">
                        <div class="mb-3">
                            <label for="invoiceid" class="form-label">Enter Invoice No</label>
                            <input type="number" autocomplete="off" class="form-control" id="invoiceid" name="invoiceid" placeholder="Enter Invoice No" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%;">Search</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Delete Invoice</h5>
                   
                    <form action="{{ route('customer.deletebillno') }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <div class="mb-3">
                            <label for="deleteInvoiceId" class="form-label text-danger">Enter Bill No To Delete:</label>
                            <input  autocomplete="off" type="number" class="form-control @error('invoiceid') is-invalid @enderror" id="deleteInvoiceId" name="invoiceid" value="{{ old('invoiceid') }}" placeholder="Enter Bill No" required>
                            @error('invoiceid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg btn-block" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Update Invoice Type</h5>
                    

                    <form action="{{ route('customer.updatebillinvoicetype') }}" method="POST" id="updateForm">
                        @csrf
                        @method('put')
                        <div class="mb-3">
                            <label for="updateInvoiceId" class="form-label text-danger">Enter Bill No To Update:</label>
                            <input autocomplete="off" type="number" value="{{ old('updateinvoiceid') }}" class="form-control @error('updateinvoiceid') is-invalid @enderror" id="updateInvoiceId" name="updateinvoiceid" value="{{ old('updateinvoiceid') }}" placeholder="Enter Bill No" required>
                            @error('updateinvoiceid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="invoiceType" class="form-label text-danger">Select Invoice Type:</label>
                            <select class="form-select @error('invoicetype') is-invalid @enderror" id="invoiceType" name="invoicetype" required>
                                <option value="check" selected>Please Select Invoice Type</option>
                                <option value="credit">Credit</option>
                                <option value="cash">Cash</option>
                            </select>
                            @error('invoicetype')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg btn-block" style="width: 100%;">Update</button>
                    </form>
                </div>
            </div>
        </div>



{{-- //updatecustomername --}}
<div class="col-md-3">
    <div class="card mb-3">
        <div class="card-body">
          
            <h5 class="card-title">Update Customer name </h5>

            <form action="{{ route('updatecustomername') }}" method="POST" id="updateFormsci">
                @csrf
                @method('put')
                <div class="mb-3">
                    <label for="Bill_No" class="form-label text-danger">Enter Bill No To Update:</label>
                    <input autocomplete="off" type="number" value="{{ old('Bill_No') }}" class="form-control @error('Bill_No') is-invalid @enderror" id="Bill_No" name="Bill_No" value="{{ old('Bill_No') }}" placeholder="Enter Bill No" required>
                    @error('Bill_No')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    
                    <label for="invoiceType" class="form-label text-danger">Select Customer Name:</label>
                    <div style="width: 300px">
                        <div class="search-box">
                            <input id="customerIdInput" name="customerid" hidden  >

                           <input type="text" class="search-input  @error('Bill_No') is-invalid @enderror" placeholder="Search Customer"
                                id="searchCustomerInput" data-api="customer_search" autocomplete="off" name="cid" id="cid" required>
                                @error('cid')
                                <div class="invalid-feedback">{{ $message }}</div>
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
                <button type="submit" class="btn btn-danger btn-lg btn-block" style="width: 100%;">Update</button>
            </form>
        </div>
    </div>
</div>

    </div>
</div>

</span>
       
          


        <div class="card customer-card mb-4" id="customerCard" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Customer Info</h5>
                <p>
                    <span>ID: </span><span id="customerId">...</span>
                </p>
                <p class="card-text">
                    <span>Name: </span><span id="customerName">...</span>
                </p>
                <p>
                    <span>Address: </span><span id="customerAddress">...</span>
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
        
        @if(isset($forinvoicetype) && !empty($forinvoicetype))
        <b style="float: right; margin-right: 100px; " class="makered">Invoice Type: {{ $forinvoicetype->invoicetype }}</b>
        <b style="float: right; margin-right: 100px;" class="makered">Date: {{ $forinvoicetype->date }}</b>


        {{-- <b style="float: right; margin-right: 100px;">Sales Return: {{ $forinvoicetype->salesreturn }}</b> --}}
        
                @if($forinvoicetype->salesreturn=="yes")
                    <b style="float: right; margin-right: 100px; color: green;" class="h5">Sales Return/Credit Notes Bill no :{{ $forinvoicetype->returnidforcreditnotes }}</b>
                @endif

                @if($forinvoicetype->invoicetype == "credit")
                <style>
                    .makered,.cusdetails {
                        background-color: rgb(216, 18, 141) !important;
                        color: white !important;
                        padding: 10px;
                        
                    }
                   
                </style>
            @endif

       @endif
       <div class="cusdetails">
        @if ($cinfodetails !=null)
            @foreach($cinfodetails as $i)
                Name:  {{$i->name}}<br>
                Address:  {{$i->address}}<br>
                Email:  {{$i->email}}<br>
                ContactNo:  {{$i->phoneno}}<br>
            @endforeach
        @endif
       </div>
<span class="float-end mb-5">
    <div class="col-12 d-flex justify-content-end align-items-center pt-4 p-4">
        <a href="{{ route('invoicebillno.convert', ['invoiceid' => $invoiceid]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($allinvoices) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink" style="font-size: 18px;">Print
            <div class="icon-box d-flex justify-content-center align-items-center" style="font-size: 34px;">
                <i class="fa-solid fa-print"></i>
            </div>
        </a>
    </div>
    
 </span>
       <b> Invoice Id: <span class=" bg-dark px-3 text-white">{{$invoiceid}} </span></b> <br>
        @if ($allinvoices !=null)
            @foreach($allinvoices as $i)
                Customer Id: {{$i->customerid}} <br>
            @endforeach
        @endif
        <span class="my-4">

           
        
           
     <table>
        <thead>
            <tr>
                <th>S.N.</th>
                <th>ITEM ID</th>
                <th>ITEM Name</th>
                <th>MRP</th>
               
                <th>Quantity</th>
                <th>Unit</th>
                <th>Sold Price</th>
                {{-- <th>Total</th>
                <th>Discount</th> --}}
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $serialNo = 1; @endphp
            @if ($allcusbyid != null)
                @foreach($allcusbyid as $i)
                    <tr>
                        <td>{{ $serialNo++ }}</td>
                        <td>{{$i->itemidorg}}</td>
                        <td>{{$i->itemid}}</td>
                        

                        <td>{{$i->mrp}}</td>
                        <td>{{$i->quantity}}</td>
                        <td>{{$i->unit}}</td>
                        <td>{{$i->price}}</td>
                        
                        <td>{{$i->subtotal}}</td>
                    </tr>
                @endforeach

                @else
        
            @endif

              
            @if ($allinvoices != null)
                @foreach($allinvoices as $i)
                    <tr>
                        <td colspan="6"></td>
                        <td class="text-right"><b>Sub-Total:</b></td>
                        <td><b>{{$i->subtotal}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="6"></td>
                        <td class="text-right"><b>Extra Discount:</b></td>
                        <td><b>{{$i->discount}}</b></td>
                    </tr>
                    <tr>
                        
                        <td colspan="6">


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
                            $number = $i->total;

                            // Convert the numerical value to words
                            $words = convertNumberToWords($number);

                            echo $words;
                        @endphp
                        only/-

                        </td>

                        <td class="text-right"><b>Total Amount:</b></td>
                        <td><b>{{$i->total}}</b></td>
                    </tr>
                   
                    <tr>
                        <td colspan="5" class="notes"><b>Notes:</b> {{$i->notes}}</td>
                    </tr>
                @endforeach
               
          
            
            @endif
        </tbody>
    </table>
<br>
    @if ($allinvoices !=null)
    @foreach($allinvoices as $i)
      
      Bill Created_by: {{$i->added_by}}
    @endforeach
@endif

    <p style="margin-top: 20px; font-size: 14px; text-align: center;">Notes:  Goods once sold won't be returned</p>
</div>
            
            <br>
            
          
           

            
        </span>
    </div>

    <script>
// JavaScript for Delete Form
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    // Get the value entered by the user in the input field
    var deleteId = document.getElementById('deleteInvoiceId').value;

    // Prompt the user with a confirmation message including the entered ID
    var confirmation = confirm('Are you sure you want to delete Invoice ID=' + deleteId + '?');

    // If the user confirms, proceed with form submission; otherwise, prevent it
    if (!confirmation) {
        e.preventDefault(); // Prevent the form from submitting if the user clicks Cancel
    }
});
// JavaScript for Update Form
document.getElementById('updateForm').addEventListener('submit', function(e) {
    // Get the value entered by the user in the input field
    var updateId = document.getElementById('updateInvoiceId').value;

    // Prompt the user with a confirmation message including the entered ID
    var confirmation = confirm('Are you sure you want to update Invoice ID=' + updateId + '?');

    // If the user confirms, proceed with form submission; otherwise, prevent it
    if (!confirmation) {
        e.preventDefault(); // Prevent the form from submitting if the user clicks Cancel
    }
});





// You can also add this if you want to execute the conversion when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    
    // Get the element by its ID
    var totalAmountElement = document.getElementById('totalAmountWords');
    
    // Get the numerical value from the element's content
    var numericalValue = parseFloat(totalAmountElement.textContent.trim());

    // Convert the numerical value to words
    var words = convertNumberToWords(numericalValue);

    // Update the content of the element with the words
    totalAmountElement.textContent = words;
});


function openPdfInNewTab(event, url) {
        event.preventDefault();
        var newTab = window.open(url, '_blank');
        newTab.focus();
    }


    
    </script>
</div>
@stop
