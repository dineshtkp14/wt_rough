@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content">
    @yield('breadcrumb')

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

        @if (Session::has('error'))
        <div class="alert bg-danger text-white w-100">
            {{ Session::get('error') }}
            </div>
        @endif
        @if (Session::has('deletesuccess'))
        <div class="alert bg-success text-white w-100">
            {{ Session::get('deletesuccess') }}
        </div>
    @endif



<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Search in ok Invoice</h5>
                    <form action="{{ route('creditnotescustomeronlyview.billno') }}" method="get" id="chosendatepdfform">
                        <div class="mb-3">
                            <label for="invoiceid" class="form-label">Enter Bill No</label>
                            <input type="number" class="form-control" id="invoiceid" name="invoiceid" placeholder="Enter Bill No" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%;">Search</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4"></div>
        <div class="col-md-4">
       
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
        <b style="float: right; margin-right: 100px;">Invoice Type: Credit Notes/Sales Return</b>
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

        @if (Session::has('success'))  

        <span class="me-5">
         <form action="{{ route('customerCreditnotes.deletebillnoforuser', ['invoiceid' => $invoiceid]) }}" method="POST">
             @csrf
             @method('DELETE')
             <button type="submit" class="me-5 btn btn-danger btn-lg" onclick="return confirmDelete();">
                 <i class="fas fa-trash-alt"></i> <!-- Font Awesome trash icon -->
                 Delete Invoice
             </button>
         </form>   
        </span>
        
       @endif
        <a href="{{ route('creditnotesbillno.convert', ['invoiceid' => $invoiceid]) }}" onclick="openPdfInNewTab(event, this.href); return false;" class="{{ count($allinvoices) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink" style="font-size: 18px;">Print
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
                <th>ITEM ID</th>
                <th>ITEM Name</th>
                <th>Original Price</th>
              
                <th>Quantity</th>
                <th>Unit</th>
                <th>Sold Price</th>
             
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @if ($allcusbyid != null)
                @foreach($allcusbyid as $i)
                    <tr>
                        <td>{{$i->itemidorg}}</td>
                        <td>{{$i->itemid}}</td>
                        <td>{{$i->mrp}}</td>
                        <td>{{$i->quantity}}</td>
                        <td>{{$i->unit}}</td>
                        <td>{{$i->price}}</td>
                        <td>{{$i->subtotal}}</td>
                    </tr>
                @endforeach
            @endif

            @if ($allinvoices != null)
                @foreach($allinvoices as $i)
                    <tr>
                        <td colspan="5"></td>
                        <td class="text-right"><b>Sub-Total:</b></td>
                        <td><b>{{$i->subtotal}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="5"></td>
                        <td class="text-right"><b>Extra Discount:</b></td>
                        <td><b>{{$i->discount}}</b></td>
                    </tr>
                    <tr>
                        
                        <td colspan="5">

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
                                    $words .= $ones[$num] . " ";
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

function openPdfInNewTab(event, url) {
        event.preventDefault();
        var newTab = window.open(url, '_blank');
        newTab.focus();
    }

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



    </script>
</div>
@stop
