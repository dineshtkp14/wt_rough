@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content">
    @yield('breadcrumb')

    <div class="container">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif

</div>


<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Search ok Invoice</h5>
                    <form action="{{ route('customer.billno') }}" method="get" id="chosendatepdfform">
                        <div class="mb-3">
                            <label for="invoiceid" class="form-label">Enter Bill No</label>
                            <input type="number" class="form-control" id="invoiceid" name="invoiceid" placeholder="Enter Bill No" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%;">Search</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Delete Invoice</h5>
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

                    <form action="{{ route('customer.deletebillno') }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <div class="mb-3">
                            <label for="deleteInvoiceId" class="form-label text-danger">Enter Bill No To Delete:</label>
                            <input type="number" class="form-control @error('invoiceid') is-invalid @enderror" id="deleteInvoiceId" name="invoiceid" value="{{ old('invoiceid') }}" placeholder="Enter Bill No" required>
                            @error('invoiceid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg btn-block" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Update Invoice Type</h5>
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

                    <form action="{{ route('customer.updatebillinvoicetype') }}" method="POST" id="updateForm">
                        @csrf
                        @method('put')
                        <div class="mb-3">
                            <label for="updateInvoiceId" class="form-label text-danger">Enter Bill No To Update:</label>
                            <input type="number" value="{{ old('updateinvoiceid') }}" class="form-control @error('updateinvoiceid') is-invalid @enderror" id="updateInvoiceId" name="updateinvoiceid" value="{{ old('updateinvoiceid') }}" placeholder="Enter Bill No" required>
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
        <b style="float: right; margin-right: 100px;">Invoice Type: {{ $forinvoicetype->invoicetype }}</b>
        <b style="float: right; margin-right: 100px;">Date: {{ $forinvoicetype->date }}</b>

        {{-- <b style="float: right; margin-right: 100px;">Sales Return: {{ $forinvoicetype->salesreturn }}</b> --}}
        
                @if($forinvoicetype->salesreturn=="yes")
                    <b style="float: right; margin-right: 100px; color: green;" class="h5">Sales Return/Credit Notes Bill no :{{ $forinvoicetype->returnidforcreditnotes }}</b>
                @endif

       @endif
        @if ($cinfodetails !=null)
            @foreach($cinfodetails as $i)
                Name:  {{$i->name}}<br>
                Address:  {{$i->address}}<br>
                Email:  {{$i->email}}<br>
                ContactNo:  {{$i->phoneno}}<br>
            @endforeach
        @endif
<span class="float-end mb-5">
    <div class="col-12 d-flex justify-content-end align-items-center pt-4 p-4">
        <a href="{{ route('invoicebillno.convert', ['invoiceid' => $invoiceid]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($allinvoices) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink" style="font-size: 18px;">Print
            <div class="icon-box d-flex justify-content-center align-items-center" style="font-size: 34px;">
                <i class="fa-solid fa-print"></i>
            </div>
        </a>
    </div>
    
 </span>
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
