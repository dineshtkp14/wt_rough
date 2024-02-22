@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content">
    @yield('breadcrumb')

    {{-- <div class="container">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif

</div> --}}


<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Search Deleted Invoice</h5>
                    @if (Session::has('error'))
                    <div class="alert alert-success w-50">
                        {{ Session::get('error') }}
                    </div>
                @endif
                    <form action="{{ route('deletedcustomer.deletebillno') }}" method="get" id="chosendatepdfform">
                        <div class="mb-3">
                            <label for="invoiceid" class="form-label">Enter Deleted Bill No</label>
                            <input type="number" class="form-control" id="invoiceid" name="invoiceid" placeholder="Enter Bill No" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%;">Search</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>


       
          


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
        <b style="float: right; margin-right: 100px;">Invoice Type: {{ $forinvoicetype->invoicetype }} {{ $forinvoicetype->invoicetype }}</b>
       @endif

       @if(isset($displayaddedby))
    <b style="float: right; margin-right: 100px;">Deleted By: {{ $displayaddedby }}</b>
    <b style="float: right; margin-right: 100px;">Date: {{ $displayaddedbydate }}</b>
@endif

   


        @if ($cinfodetails !=null)
            @foreach($cinfodetails as $i)
                Name:  {{$i->name}}<br>
                Address:  {{$i->address}}<br>
                Email:  {{$i->email}}<br>
                ContactNo:  {{$i->phoneno}}<br>
            @endforeach
        @endif

        Invoice Id: {{$invoiceid}} <br>

        @if ($allinvoices !=null)
            @foreach($allinvoices as $i)
                Customer Id: {{$i->customerid}}
            @endforeach
        @endif
        <span class="my-4">
            <table>
                <thead>
                    <tr>
                        <th>ITEM Name</th>
                        <th>Original Price</th>
                        <th>Sold Price</th>
                        <th>Quantity</th>
                        {{-- <th>Total</th>
                        <th>Discount</th> --}}
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($allcusbyid != null)
                        @foreach($allcusbyid as $i)
                            <tr>
                                <td>{{$i->itemid}}</td>
                                <td>{{$i->mrp}}</td>
                                <td>{{$i->price}}</td>
                                <td>{{$i->quantity}}</td>
                                {{-- <td>{{$i->price*$i->quantity}}</td>
                                <td>{{$i->discount}}</td> --}}
                                <td>{{$i->subtotal}}</td>
                            </tr>
                        @endforeach
                    @endif
        
                    @if ($allinvoices != null)
                        @foreach($allinvoices as $i)
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-right"><b>Sub-Total:</b></td>
                                <td><b>{{$i->subtotal}}</b></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td class="text-right"><b>Extra Discount:</b></td>
                                <td><b>{{$i->discount}}</b></td>
                            </tr>
                            <tr>
                                
                                <td colspan="3" id="totalAmountWords">{{$i->total}}</td>
        
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

        </span>
    </div>

    <script>
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



// JavaScript for PDF Link
document.getElementById('pdfLink').addEventListener('click', function(e) {
    e.preventDefault();
    var query = window.location.search;
    var param = new URLSearchParams(query);
    var url = "{{ route('invoicebillno.convert') }}?invoiceid=" + param.get('invoiceid');
    window.location.href = url;
});




    </script>
</div>
@stop
