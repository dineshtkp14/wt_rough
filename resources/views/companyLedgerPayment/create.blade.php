@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
        @yield('breadcrumb')


    <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
        <div class="card-body">
            <h5 class="card-title">Company Info</h5>
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

 


<div class="container">
            @if (Session::has('success'))
            <div class="alert bg-success text-white w-50">
                {{ Session::get('success') }}
                </div>
            @endif
</div>

<div class="container">


<form class="row gx-5 gy-3" action="{{route('companyLedgerspay.store')}}" method="post">
                @csrf

               
           
           
                  <div class="py-4 d-flex justify-content-between align-items-center">
                    <div style="width: 300px">
                      
                        <div class="input-group mb-1">
                            <div class="search-box">
                               
                                <input id="customerIdInput"  name="companyid" hidden required>

                                <input  autocomplete="off" type="text" required class="search-input @error('customerid') is-invalid @enderror" placeholder="Search Company"
                                    id="searchCustomerInput"  data-api="company_search" autocomplete="off">
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
                    <div style="width: 300px">
                      
                        <div class="input-group mb-1">
                            <span class="input-group-text">Date:  <span style="color: red;">*</span></span>
                            <input  autocomplete="off" type="date" class="form-control  @error('date') is-invalid @enderror" placeholder="" id="salesDate" class="form-control foritemsaledatecss" value="{{now()->format('Y-m-d')}}" name="date" >
                            @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Particulars (bank name/cash)  <span style="color: red;">*</span></label>
                    <input  autocomplete="off" type="text" class="form-control @error('particulars') is-invalid @enderror" 
                        name="particulars" value="{{ old('particulars') }}">
                    @error('particulars')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Voucher Type (receipt/cash)   <span style="color: red;">*</span></label>
                    <input  autocomplete="off" type="text" class="form-control @error('vt') is-invalid @enderror" 
                        name="vt" value="{{ old('vt') }}">
                    @error('vt')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Amount  <span style="color: red;">*</span></label>
                    <input  id="amount" autocomplete="off" type="number" class="form-control @error('amount') is-invalid @enderror" 
                        name="amount" value="{{ old('amount') }}">
                    @error('amount')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea  autocomplete="off"  class="form-control @error('notes') is-invalid @enderror" 
                    name="notes" value="{{ old('notes') }}"> </textarea>
                @error('notes')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

        <div class="col-md-12">
            <label for="inputPassword4" class="form-label">Amount in Words: </label>
           <b> <span id="amountInWords"></span></b>
        </div>

           

            <div class="d-grid gap-2 pt-2 pb-4">
                    <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Save</button>
            </div>
</form>
</div>



</div>



<script>
    function convertNumberToWords(num) {
    var ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    var tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
    var decimals = ["", "Tenth", "Hundredth"];

    // Split the number into integer and fractional parts
    var parts = String(num).split('.');
    var integerPart = parseInt(parts[0], 10);
    var fractionalPart = parts[1] ? parseInt(parts[1], 10) : 0; // Convert fractional part to an integer

    var words = "";

    // Convert the integer part to words
    if (integerPart === 0) {
        words = "Zero";
    } else {
        // Convert each part of the number separately
        if (integerPart >= 10000000) {
            words += convertNumberToWords(Math.floor(integerPart / 10000000)) + " Crore ";
            integerPart %= 10000000;
        }
        if (integerPart >= 100000) {
            words += convertNumberToWords(Math.floor(integerPart / 100000)) + " Lakh ";
            integerPart %= 100000;
        }
        if (integerPart >= 1000) {
            words += convertNumberToWords(Math.floor(integerPart / 1000)) + " Thousand ";
            integerPart %= 1000;
        }
        if (integerPart >= 100) {
            words += convertNumberToWords(Math.floor(integerPart / 100)) + " Hundred ";
            integerPart %= 100;
        }
        if (integerPart >= 20) {
            words += tens[Math.floor(integerPart / 10)] + " ";
            integerPart %= 10;
        }
        if (integerPart > 0) {
            words += ones[integerPart] + " ";
        }
    }

    // Convert the fractional part to words
    if (fractionalPart > 0) {
        words += " and " + ones[fractionalPart] + " " + decimals[parts[1].length] + " "; // Append the fractional part
    }

    return words.trim();
}

        function updateAmountInWords() {
            var amount = parseInt(document.getElementById('amount').value, 10); // Parse the input as an integer
            var amountInWords = convertNumberToWords(amount);
            document.getElementById('amountInWords').innerText = amountInWords + '  Only/-';
        }


        document.getElementById('amount').addEventListener('input', updateAmountInWords);
</script>
<script>
        
    $(document).ready(function () {
            $('form').submit(function () {
                // Disable the submit button
                $('#submitBtn').prop('disabled', true);
                
            });
        });
    
        </script>
@stop

