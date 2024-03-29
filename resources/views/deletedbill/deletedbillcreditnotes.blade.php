@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content">
    @yield('breadcrumb')




<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Search oky Invoice</h5>
                    @if (Session::has('error'))
                    <div class="alert alert-success w-50">
                        {{ Session::get('error') }}
                    </div>
                @endif
                    <form action="{{ route('deletedcncustomer.deletebillno') }}" method="get" id="chosendatepdfform">
                        <div class="mb-3">
                            <label for="invoiceid" class="form-label">Enter Bill No</label>
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

        <h1 class="text-center"> Credit Notes/Sales Return</h1>

        @if(isset($forinvoicetype) && !empty($forinvoicetype))
        <b style="float: right; margin-right: 100px;">Date: {{ $forinvoicetype->date }}</b>

        
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


        Invoice Id: {{$invoiceid}} <br>

        @if ($allinvoicesdetails !=null)
            @foreach($allinvoicesdetails as $i)
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
                <th>Unit</th>
             
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
                        <td>{{$i->unit}}</td>
                      
                        <td>{{$i->subtotal}}</td>
                    </tr>
                @endforeach
            @endif

           
            
            @if ($allinvoicesdetails != null)
                @foreach($allinvoicesdetails as $i)
                    <tr>
                        <td colspan="4"></td>
                        <td class="text-right"><b>Sub-Total:</b></td>
                        <td><b>{{$i->subtotal}}</b></td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td class="text-right"><b>Extra Discount:</b></td>
                        <td><b>{{$i->discount}}</b></td>
                    </tr>
                    <tr>
                        
                        <td colspan="4" id="totalAmountWords">{{$i->total}}</td>

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




    </script>
</div>
@stop
