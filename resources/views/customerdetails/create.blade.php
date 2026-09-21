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
                            <input id="customerIdInput" name="customerid" value="{{ old('customerid', request('customerid')) }}" hidden>
                            <input id="invoiceIdInput" name="invoiceid" value="{{ old('invoiceid', request('invoiceid')) }}" hidden>
                            <input id="invoiceIdsInput" name="invoiceids" value="{{ old('invoiceids', request('invoiceids')) }}" hidden>
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

            <div class="col-md-3">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="isCheque" name="is_cheque" value="1" style="width: 30px; height: 30px;" {{ old('is_cheque') ? 'checked' : '' }}>
                    <label class="form-check-label" for="isCheque">If Cheque</label>
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

            <div class="w-100 payment-row-break"></div>

            <div class="col-md-12 row g-3" id="chequeDetails" style="display: {{ old('is_cheque') ? 'flex' : 'none' }};">
                <div class="col-md-4"><label>Cheque Bank *</label><input name="cheque_bank" id="chequeBank" class="form-control" value="{{ old('cheque_bank') }}" placeholder="Bank of ..."></div>
                <div class="col-md-4"><label>Cheque No. *</label><input name="cheque_no" id="chequeNo" class="form-control" value="{{ old('cheque_no') }}" placeholder="Cheque number"></div>
            <div class="col-md-4"><label>Cheque Date (B.S.) *</label><input type="text" name="cheque_exchange_date_bs" id="chequeExchangeDate" class="form-control @error('cheque_exchange_date_bs') is-invalid @enderror" value="{{ old('cheque_exchange_date_bs', \App\Support\NepaliDate::adToBsString(now()->toDateString(), 'en')) }}" placeholder="YYYY-MM-DD" inputmode="numeric" pattern="[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}"><small class="text-muted d-block mt-1">Enter Nepali date, e.g. 2083-06-02</small>@error('cheque_exchange_date_bs')<small class="text-danger d-block">{{ $message }}</small>@enderror</div>
            </div>
            <div class="w-100 payment-row-break"></div>

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

            <div class="w-100 payment-row-break"></div>

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
                <input autocomplete="off" id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" placeholder="Enter payment amount" style="font-weight: bold;font-size: 20px;"  >
                @error('amount')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea autocomplete="off" maxlength="60" class="form-control @error('notes') is-invalid @enderror" name="notes" placeholder="Maximum 60 characters">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="inputPassword4" class="form-label">Amount in Words: </label>
               <b> <span id="amountInWords"></span></b>
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" id="paymentSubmitBtn" class="btn btn-lg btn-primary">
                    <i class="fas fa-save"></i>
                    Save Payment
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="paymentSendingSmsModal" tabindex="-1" aria-labelledby="paymentSendingSmsModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content payment-sending-modal">
            <div class="modal-body">
                <div class="payment-sending-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h5 id="paymentSendingSmsModalLabel">Saving payment and sending SMS...</h5>
                <p>Please wait until the receipt opens.</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);

        const customerId = urlParams.get('customerid');
        const particulars = urlParams.get('particulars');
        const voucherType = urlParams.get('voucher_type');
        const cname = urlParams.get('cname');


        const laptop = urlParams.get('laptop'); // Optional if needed later

        if (customerId) {
            document.getElementById('customerIdInput').value = customerId;
            // Optional: You can pre-fill search input or fetch customer info here
        }

        const invoiceId = urlParams.get('invoiceid');
        if (invoiceId) {
            document.getElementById('invoiceIdInput').value = invoiceId;
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

        

        // Optionally disable the checkbox and input fields if they are auto-filled
        if (particulars || voucherType) {
            document.getElementById('disableFields').disabled = true;
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded',function(){const check=document.getElementById('isCheque'),details=document.getElementById('chequeDetails'),date=document.getElementById('chequeExchangeDate'),cash=document.getElementById('forautoinputcash'),fonepay=document.getElementById('forautoinputfonepay');if(!check||!details)return;const fields=details.querySelectorAll('input');const toggle=()=>{details.style.display=check.checked?'flex':'none';fields.forEach(field=>field.required=check.checked);if(!check.checked)fields.forEach(field=>field.value='')};const formatBsDate=()=>{if(!date)return;let digits=date.value.replace(/\D/g,'').slice(0,8);if(digits.length>6)digits=digits.slice(0,4)+'-'+digits.slice(4,6)+'-'+digits.slice(6);else if(digits.length>4)digits=digits.slice(0,4)+'-'+digits.slice(4);date.value=digits};check.addEventListener('change',function(){if(this.checked){if(cash)cash.checked=false;if(fonepay)fonepay.checked=false}toggle()});if(cash)cash.addEventListener('change',function(){if(this.checked){check.checked=false;if(fonepay)fonepay.checked=false;toggle()}});if(fonepay)fonepay.addEventListener('change',function(){if(this.checked){check.checked=false;if(cash)cash.checked=false;toggle()}});if(check.checked){if(cash)cash.checked=false;if(fonepay)fonepay.checked=false}else if(cash&&fonepay&&cash.checked&&fonepay.checked){fonepay.checked=false}if(date)date.addEventListener('input',formatBsDate);toggle();formatBsDate();});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cheque = document.getElementById('isCheque');
    const bank = document.getElementById('chequeBank');
    const particulars = document.getElementById('particulars');
    const hiddenParticulars = document.getElementById('hiddenParticulars');
    const voucherType = document.getElementById('vt');
    const hiddenVoucherType = document.getElementById('hiddenVt');
    if (!cheque || !bank || !particulars || !voucherType) return;

    const syncChequePayment = () => {
        if (cheque.checked) {
            const bankName = bank.value.trim();
            const particularsValue = bankName ? 'CHEQUE DEPOSIT - ' + bankName : 'CHEQUE DEPOSIT';
            particulars.value = particularsValue;
            voucherType.value = 'CHEQUE DEPOSIT';
            if (hiddenParticulars) hiddenParticulars.value = particularsValue;
            if (hiddenVoucherType) hiddenVoucherType.value = 'CHEQUE DEPOSIT';
        } else if (particulars.value.indexOf('CHEQUE DEPOSIT') === 0) {
            particulars.value = '';
            voucherType.value = '';
            if (hiddenParticulars) hiddenParticulars.value = '';
            if (hiddenVoucherType) hiddenVoucherType.value = '';
        }
    };

    cheque.addEventListener('change', syncChequePayment);
    bank.addEventListener('input', syncChequePayment);
    syncChequePayment();
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
                $('#paymentSubmitBtn').prop('disabled', true);

                var modalElement = document.getElementById('paymentSendingSmsModal');
                if (modalElement && window.bootstrap) {
                    new bootstrap.Modal(modalElement).show();
                }
                
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
            const submitBtn = document.getElementById('paymentSubmitBtn');
            const cheque = document.getElementById('isCheque');
            const chequeDate = document.getElementById('chequeExchangeDate');

            function validateChequeDate() {
                if (!cheque || !cheque.checked || !chequeDate) return true;
                const match = /^(\d{4})-(\d{1,2})-(\d{1,2})$/.exec(chequeDate.value.trim());
                const valid = match && Number(match[1]) >= 2000 && Number(match[1]) <= 2200
                    && Number(match[2]) >= 1 && Number(match[2]) <= 12
                    && Number(match[3]) >= 1 && Number(match[3]) <= 32;
                chequeDate.setCustomValidity(valid ? '' : 'Enter a valid Nepali date, for example 2083-06-02.');
                return Boolean(valid);
            }
        
            function validateForm() {
                const isValid =
                    particulars.value.trim() !== '' &&
                    voucherType.value.trim() !== '' &&
                    amount.value.trim() !== '' &&
                    validateChequeDate();
        
                submitBtn.disabled = !isValid;
            }
        
            // Listen to all possible changes
            particulars.addEventListener('input', validateForm);
            voucherType.addEventListener('input', validateForm);
            amount.addEventListener('input', validateForm);
            if (chequeDate) chequeDate.addEventListener('input', validateForm);
            if (cheque) cheque.addEventListener('change', validateForm);

            const paymentForm = submitBtn ? submitBtn.closest('form') : null;
            if (paymentForm) paymentForm.addEventListener('submit', function (event) {
                if (!validateChequeDate()) {
                    event.preventDefault();
                    chequeDate.focus();
                    chequeDate.reportValidity();
                }
            });
        
            // Also trigger validation when checkboxes change (auto-fill cases)
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener('change', validateForm);
            });

            validateForm();
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

<style>
    .payment-sending-modal {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .payment-sending-modal .modal-body {
        padding: 28px;
        text-align: center;
    }

    .payment-sending-icon {
        align-items: center;
        background: #2563eb;
        border-radius: 50%;
        color: #ffffff;
        display: inline-flex;
        height: 58px;
        justify-content: center;
        margin-bottom: 14px;
        width: 58px;
    }

    .payment-sending-icon i {
        animation: paymentSendingPulse 1s ease-in-out infinite;
        font-size: 24px;
    }

    .payment-sending-modal h5 {
        color: #111827;
        font-size: 20px;
        font-weight: 900;
        margin: 0 0 6px;
    }

    .payment-sending-modal p {
        color: #4b5563;
        font-size: 15px;
        font-weight: 700;
        margin: 0;
    }

    @keyframes paymentSendingPulse {
        0%, 100% {
            transform: translateX(0);
        }

        50% {
            transform: translateX(5px);
        }
    }
</style>
<style>
    .main-content:has(form[action*="cpayments"]){background:#eef4fb;min-height:calc(100vh - 70px);padding-bottom:50px}
    .main-content:has(form[action*="cpayments"])>.container{max-width:none!important;width:100%!important;padding-left:24px!important;padding-right:24px!important}
    form[action*="cpayments"]{max-width:none;width:100%;margin:28px 0 0!important;padding:28px 32px!important;background:#fff;border:1px solid #dce6f2;border-radius:18px;box-shadow:0 12px 32px rgba(23,59,114,.10)!important;align-items:stretch!important}
    form[action*="cpayments"] .py-4{padding:0 0 24px!important;margin-bottom:8px;border-bottom:1px solid #e4ebf4}
    form[action*="cpayments"] .py-4>div:first-child{width:340px!important}.search-input{height:46px!important;border:1px solid #c9d7e8!important;border-radius:10px!important;padding-left:42px!important}.search-icon{top:14px!important}
    form[action*="cpayments"] .py-4>div:last-child{width:250px!important}.input-group-text{background:#f8fbff!important;border-color:#c9d7e8!important;color:#526987;font-weight:700}
    form[action*="cpayments"]>.col-md-2,form[action*="cpayments"]>.col-md-3,form[action*="cpayments"]>.col-md-4{padding:14px 16px;border:1px solid #e2eaf4;border-radius:12px;background:#f8fbff;display:flex;align-items:center;min-height:66px}
    form[action*="cpayments"]>.col-md-2,form[action*="cpayments"]>.col-md-3,form[action*="cpayments"]>.col-md-4{flex:1 1 0!important;width:auto!important;max-width:none!important}
    form[action*="cpayments"]>.col-md-6{flex:0 0 50%!important;width:50%!important;max-width:50%!important}
    form[action*="cpayments"]>.col-md-6:has(#particulars){flex:0 0 50%!important;width:50%!important;max-width:50%!important;margin-top:4px}
    form[action*="cpayments"]>.col-md-2 .form-check,form[action*="cpayments"]>.col-md-3 .form-check,form[action*="cpayments"]>.col-md-4 .form-check{width:100%;margin:0}
    form[action*="cpayments"] .form-check-input{width:22px!important;height:22px!important;margin-top:0!important;accent-color:#2563eb}
    form[action*="cpayments"] .form-check-label{font-weight:800;color:#173b72;font-size:15px}
    form[action*="cpayments"] label:not(.form-check-label){display:block;margin-bottom:7px;color:#405675;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:.25px}
    form[action*="cpayments"] .form-control{min-height:46px;border:1px solid #c9d7e8;border-radius:9px;background:#fbfdff;font-size:15px}
    form[action*="cpayments"] textarea.form-control{min-height:110px}
    form[action*="cpayments"] #chequeDetails{display:none;align-items:stretch!important;padding:18px!important;margin:4px 0!important;border:1px solid #f4d58b;border-radius:14px;background:linear-gradient(135deg,#fff9e8,#fffdf6)}
    form[action*="cpayments"] #chequeDetails>div{padding:0 8px!important;border:0;background:transparent;display:block;min-height:0}
    form[action*="cpayments"] #chequeDetails label{color:#8a5a00}
    form[action*="cpayments"] #chequeDetails input{background:#fff;border-color:#e7c979}
    form[action*="cpayments"] #amount{font-size:20px!important;font-weight:800!important;color:#173b72}
    form[action*="cpayments"] #paymentSubmitBtn{height:54px;border:0;border-radius:11px;background:linear-gradient(135deg,#2563eb,#1744a0);font-size:17px;font-weight:900;box-shadow:0 8px 18px #2563eb35}
    form[action*="cpayments"] #paymentSubmitBtn:hover{transform:translateY(-1px);box-shadow:0 10px 22px #2563eb45}
    @media(max-width:768px){form[action*="cpayments"]{padding:18px!important;margin-top:18px!important}form[action*="cpayments"] .py-4{display:block!important}form[action*="cpayments"] .py-4>div{width:100%!important;margin:12px 0}form[action*="cpayments"]>.col-md-2,form[action*="cpayments"]>.col-md-3,form[action*="cpayments"]>.col-md-4,form[action*="cpayments"]>.col-md-6{flex:0 0 100%!important;width:100%!important;max-width:100%!important}.main-content:has(form[action*="cpayments"])>.container{padding-left:12px!important;padding-right:12px!important}}
</style>
@stop
