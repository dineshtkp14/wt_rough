@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')

    <div class="container">
        @if (Session::has('error'))
        <div class="alert bg-danger text-white w-50">
            {{ Session::get('error') }}
            </div>
        @endif
</div>

<div class="d-flex justify-content-end me-5">
    <a href="{{ route('cashreceipt.search') }}" class="btn btn-primary me-5">
        <!-- Icon -->
        <i class="fas fa-search"></i>
        Search Cash Receipt
    </a>
    
    <a href="{{ route('customerinfos.create') }}" class="btn btn-success">
        <!-- Icon -->
        <i class="fas fa-user-plus"></i> <!-- Different icon here -->
        Add New Customer
    </a>
</div>


  
  <div class="card customer-card mb-4" id="customerCard" style="display: none;">
        <div class="card-body">
            <h5 class="card-title">Customer Information</h5>
            <p><span>ID: </span><span id="customerId">...</span></p>
            <p class="card-text"><span>Name: </span><span id="customerName">...</span></p>
            <p><span>Addres: </span><span id="customerAddress">...</span></p>
            <p><span>E-mail: </span><span id="customerEmail">...</span></p>
            <p><span>PhoneNo: </span><span id="customerPhone">...</span></p>
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
        <h4>Cash Receipt No: {{$nextUserId}}</h4>

        <form class="row gx-5 gy-3" action="{{ route('cpayments.store') }}" method="post">
            @csrf

            <div class="py-4 d-flex justify-content-between align-items-center">
                <div style="width: 200px">
                    <div class="input-group mb-1">
                        <div class="search-box">
                            <input id="customerIdInput" name="customerid" hidden>
                            <input type="text" class="search-input @error('customerid') is-invalid @enderror" placeholder="Search Customer" id="searchCustomerInput" data-api="customer_search" autocomplete="off">
                            @error('customerid')
                            <p class="invalid-feedback m-0" style="position: absolute; bottom: -24px; left: 0;">{{ $message }}</p>
                            @enderror  
                            <i class="fas fa-search search-icon"></i>
                            <div class="result-wrapper" id="customerResultWrapper" style="display: none;">
                                <div class="result-box d-flex justify-content-start align-items-center" id="customerLoadingResultBox">
                                    <i class="fas fa-spinner" id="spinnerIcon"></i>
                                    <h1 class="m-0 px-2"> Loading</h1>
                                </div>
                                <div class="result-box d-flex justify-content-start align-items-center d-none" id="customerNotFoundResultBox">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <h1 class="m-0 px-2"> Record Not Found</h1>
                                </div>
                                <div id="customerResultList"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    
                    <span id="cname" style="font-size: 20px; font-weight: bold; background-color: black; color: white; padding: 4px 8px; border-radius: 4px;"></span>
                </div>
                
                <div style="width: 300px">
                    <div class="input-group mb-1">
                        <span class="input-group-text">Date: <span style="color: red;">*</span></span>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" placeholder="" id="salesDate" class="form-control foritemsaledatecss" value="{{now()->format('Y-m-d')}}" name="date">
                        @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Checkbox -->
            

            <div class="col-md-2">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="forautoinputcash" name="forautoinputcash" style="width: 30px; height: 30px;">
                    <label class="form-check-label" for="forautoinputcash">If Cash</label>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="forautoinputfonepay" name="forautoinputfonepay" style="width: 30px; height: 30px;">
                    <label class="form-check-label" for="forautoinputfonepay">If Fonepay</label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="nilaccount" name="nilaccount" style="width: 30px; height: 30px;">
                    <label class="form-check-label" for="nilaccount">Nil Account</label>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="disableFields" name="disableFields" style="width: 30px; height: 30px;">
                    <label class="form-check-label" for="disableFields">If Sales Return</label>
                </div>
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label"> Particulars <span class="text-success fw-bold"> (Bank Name / Fone Pay / Payment) </span> <span style="color: red;">*</span></label>
                <input autocomplete="off" id="particulars" type="text" class="form-control @error('particulars') is-invalid @enderror" name="particulars" value="{{ old('particulars') }}" >
                <input  id="hiddenParticulars" type="hidden" name="hiddenParticulars" value="{{ old('hiddenParticulars') }}">
                @error('particulars')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Voucher Type <span class="text-success fw-bold">(Receipt / Cash)</span> <span style="color: red;">*</span></label>
                <input autocomplete="off" id="vt" type="text" class="form-control @error('vt') is-invalid @enderror" name="vt" value="{{ old('vt') }}" >
                <input  id="hiddenVt" type="hidden" name="hiddenVt" value="{{ old('hiddenVt') }}">
                @error('vt')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <!-- Your existing HTML code -->
            <div class="col-md-6" id="additionalFieldContainer" style="display: none;">
                <label for="cninvoiceid" class="form-label">Credit Notes Invoice ID</label>
                <input autocomplete="off" type="number" class="form-control" id="cninvoiceid" name="cninvoiceid">
            </div>

            <div class="col-md-6">
                <label for="amount" class="form-label">
                    Amount <span style="color: red;">*</span>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;( Old due amount: <span id="totaldueamountfornotclear" class="text-danger fw-bold"></span> )
                </label>                
                <input autocomplete="off" id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" style="font-weight: bold;font-size: 20px;"  >
                @error('amount')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea autocomplete="off" class="form-control @error('notes') is-invalid @enderror" name="notes">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="inputPassword4" class="form-label">Amount in Words: </label>
               <b> <span id="amountInWords"></span></b>
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Payment</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);

        const customerId = urlParams.get('customerid');
        const particulars = urlParams.get('particulars');
        const voucherType = urlParams.get('voucher_type');
        const amountx = urlParams.get('amount');
        const cname = urlParams.get('cname');


        const laptop = urlParams.get('laptop'); // Optional if needed later

        if (customerId) {
            document.getElementById('customerIdInput').value = customerId;
            // Optional: You can pre-fill search input or fetch customer info here
        }

        if (particulars) {
            document.getElementById('particulars').value = particulars;
            document.getElementById('hiddenParticulars').value = particulars;
        }

        if (voucherType) {
            document.getElementById('vt').value = voucherType;
            document.getElementById('hiddenVt').value = voucherType;
        }
        if (cname) {
    const cnameElement = document.getElementById('cname');
    if (cnameElement) {
        cnameElement.textContent = cname;
    }

    const searchInput = document.getElementById('searchCustomerInput');
    if (searchInput) {
        searchInput.style.display = 'none';
    }
}

//         if (amountx) {
//     document.getElementById('amount').value = amountx;
//     if (typeof updateAmountInWords === 'function') {
//         updateAmountInWords();
//     }
// }
        

        // Optionally disable the checkbox and input fields if they are auto-filled
        if (particulars || voucherType) {
            document.getElementById('disableFields').disabled = true;
        }
    });
</script>

<script>
    //modefonepayandcash
    document.addEventListener('DOMContentLoaded', function () {
        const cashCheckbox = document.getElementById('forautoinputcash');
        const fonepayCheckbox = document.getElementById('forautoinputfonepay');
    
        const particularsInput = document.getElementById('particulars');
        const hiddenParticulars = document.getElementById('hiddenParticulars');
        const vtInput = document.getElementById('vt');
        const hiddenVt = document.getElementById('hiddenVt');
    
        function setPaymentMode(mode) {
            particularsInput.value = mode;
            hiddenParticulars.value = mode;
            vtInput.value = mode;
            hiddenVt.value = mode;
        }
    
        function clearPaymentMode() {
            particularsInput.value = '';
            hiddenParticulars.value = '';
            vtInput.value = '';
            hiddenVt.value = '';
        }
    
        cashCheckbox.addEventListener('change', function () {
            if (this.checked) {
                fonepayCheckbox.checked = false;
                setPaymentMode('CASH');
            } else {
                clearPaymentMode();
            }
        });
    
        fonepayCheckbox.addEventListener('change', function () {
            if (this.checked) {
                cashCheckbox.checked = false;
                setPaymentMode('FONEPAY');
            } else {
                clearPaymentMode();
            }
        });
    });
    </script>
    


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const totalDue = urlParams.get('totaldueamountfornotclear');

        if (totalDue && document.getElementById('totaldueamountfornotclear')) {
            document.getElementById('totaldueamountfornotclear').innerText = totalDue;
        }
    });
</script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('disableFields');
    const particularsInput = document.getElementById('particulars');
    const hiddenParticularsInput = document.getElementById('hiddenParticulars');
    const voucherTypeInput = document.getElementById('vt');
    const hiddenVoucherTypeInput = document.getElementById('hiddenVt');
    const additionalFieldContainer = document.getElementById('additionalFieldContainer');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            particularsInput.value = 'salesreturn';
            voucherTypeInput.value = 'return';
            hiddenParticularsInput.value = 'salesreturn';
            hiddenVoucherTypeInput.value = 'return';
            particularsInput.disabled = true;
            voucherTypeInput.disabled = true;
            additionalFieldContainer.style.display = 'block'; // Show the additional field
        } else {
            particularsInput.value = '';
            voucherTypeInput.value = '';
            hiddenParticularsInput.value = '';
            hiddenVoucherTypeInput.value = '';
            particularsInput.disabled = false;
            voucherTypeInput.disabled = false;
            additionalFieldContainer.style.display = 'none'; // Hide the additional field
        }
    });

    particularsInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }
    });

    voucherTypeInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }
    });
});


$(document).ready(function () {
            $('form').submit(function () {
                // Disable the submit button
                $('#submitBtn').prop('disabled', true);
                
            });
        });
</script>

<script>
    // for nilling account script
    document.addEventListener('DOMContentLoaded', function () {
        const nilCheckbox = document.getElementById('nilaccount');
        const amountInput = document.getElementById('amount');
        const dueSpan = document.getElementById('totaldueamountfornotclear');
        const amountInWords = document.getElementById('amountInWords');

        nilCheckbox.addEventListener('change', function () {
            if (this.checked) {
                let dueAmount = dueSpan.innerText.replace(/,/g, '');
                amountInput.value = dueAmount || '';

                // 🔥 STYLE CHANGE
                amountInput.style.backgroundColor = 'black';
                amountInput.style.color = 'white';
                amountInput.style.border = '8px solid red'; // yellow border


                if (typeof updateAmountInWords === 'function') {
                    updateAmountInWords();
                }
            } else {
                amountInput.value = '';
                amountInWords.innerText = '';

                // 🔄 RESET STYLE
                amountInput.style.backgroundColor = '';
                amountInput.style.color = '';
                amountInput.style.border = '';

            }
        });
    });
</script>

    

    <script>
        //forvalidation of submit or payment button disable and enable 
        document.addEventListener('DOMContentLoaded', function () {
            const particulars = document.getElementById('particulars');
            const voucherType = document.getElementById('vt');
            const amount = document.getElementById('amount');
            const submitBtn = document.getElementById('submitBtn');
        
            function validateForm() {
                const isValid =
                    particulars.value.trim() !== '' &&
                    voucherType.value.trim() !== '' &&
                    amount.value.trim() !== '';
        
                submitBtn.disabled = !isValid;
            }
        
            // Initial state (DISABLED on page load)
            submitBtn.disabled = true;
        
            // Listen to all possible changes
            particulars.addEventListener('input', validateForm);
            voucherType.addEventListener('input', validateForm);
            amount.addEventListener('input', validateForm);
        
            // Also trigger validation when checkboxes change (auto-fill cases)
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', validateForm);
            });
        });
        </script>
        

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
@stop
