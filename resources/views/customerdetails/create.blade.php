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
        <h4>Cash Receipt No: {{$displayReceiptNo ?? $nextUserId}}</h4>

        <form class="row gx-5 gy-3" action="{{ route('cpayments.store') }}" method="post">
            @csrf

            <div class="py-4 d-flex justify-content-between align-items-center payment-search-row{{ request('customerid') ? ' has-prefilled-customer' : '' }}">
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
            </div>

            <!-- Checkbox -->
            

            <div class="col-md-3">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="isCheque" name="is_cheque" value="1" style="width: 30px; height: 30px;" {{ old('is_cheque') ? 'checked' : '' }}>
                    <label class="form-check-label" for="isCheque">If Cheque</label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="nilaccount" name="nilaccount" style="width: 30px; height: 30px;">
                    <label class="form-check-label" for="nilaccount">Nil Account</label>
                </div>
            </div>

            <div class="col-md-5 customer-summary-slot">
                <span id="cname" style="font-size: 20px; font-weight: bold; background-color: black; color: white; padding: 4px 8px; border-radius: 4px;"></span>
            </div>

            <div class="date-summary-slot">
                <div class="input-group mb-1">
                    <span class="input-group-text">Date: <span style="color: red;">*</span></span>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="salesDate" value="{{ now()->format('Y-m-d') }}" name="date">
                    @error('date')
                    <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
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
                <label for="particularsSelect" class="form-label">Particulars <span style="color: red;">*</span></label>
                <div class="particulars-picker-row">
                    <select id="particularsSelect" class="form-select @error('particulars') is-invalid @enderror">
                        <option value="">-- Select Particulars --</option>
                        <option value="NMB BANK - DURGA AND DINESH TRADERS">NMB BANK - DURGA AND DINESH TRADERS</option>
                        <option value="RBB BANK - MALIKA AND NAWADURGA TRADERS">RBB BANK - MALIKA AND NAWADURGA TRADERS</option>
                        <option value="CASH IN HAND">CASH IN HAND</option>
                        <option value="ESEWA HARI PRASAD BAJAIN">ESEWA HARI PRASAD BAJAIN</option>
                        <option value="__other__">Other</option>
                    </select>
                    <div id="otherParticularsWrap" style="display: none;">
                        <input autocomplete="off" id="otherParticulars" type="text" class="form-control" placeholder="Enter particulars">
                    </div>
                </div>
                <input autocomplete="off" id="particulars" type="hidden" name="particulars" value="{{ old('particulars') }}">
                <input  id="hiddenParticulars" type="hidden" name="hiddenParticulars" value="{{ old('hiddenParticulars') }}">
                @error('particulars')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6" style="display: none;">
                <label for="inputPassword4" class="form-label">Voucher Type <span class="text-success fw-bold">(Receipt / Cash)</span> <span style="color: red;">*</span></label>
                <input autocomplete="off" id="vt" type="text" class="form-control @error('vt') is-invalid @enderror" name="vt" value="{{ old('vt') ?: request('voucher_type', 'Receipt') }}" >
                <input  id="hiddenVt" type="hidden" name="hiddenVt" value="{{ old('hiddenVt') ?: request('voucher_type', 'Receipt') }}">
                @error('vt')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="w-100 payment-row-break"></div>

            <div class="w-100 payment-section-divider" aria-hidden="true"></div>

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

        const particularsSelect = document.getElementById('particularsSelect');
        const particularsInput = document.getElementById('particulars');
        const otherParticularsWrap = document.getElementById('otherParticularsWrap');
        const otherParticularsInput = document.getElementById('otherParticulars');
        const syncParticulars = function (value) {
            const isOther = value === '__other__';
            if (otherParticularsWrap) otherParticularsWrap.style.display = isOther ? 'block' : 'none';
            if (!isOther && particularsInput) particularsInput.value = value || '';
            if (isOther && otherParticularsInput && particularsInput) particularsInput.value = otherParticularsInput.value.trim();
            if (document.getElementById('hiddenParticulars')) document.getElementById('hiddenParticulars').value = particularsInput ? particularsInput.value : '';
        };

        if (particularsSelect) particularsSelect.addEventListener('change', function () { syncParticulars(this.value); });
        if (otherParticularsInput) otherParticularsInput.addEventListener('input', function () { syncParticulars('__other__'); });


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
            const matchingOption = particularsSelect && Array.from(particularsSelect.options).find(function (option) { return option.value === particulars; });
            if (matchingOption) {
                particularsSelect.value = particulars;
                syncParticulars(particulars);
            } else if (particularsSelect) {
                particularsSelect.value = '__other__';
                if (otherParticularsInput) otherParticularsInput.value = particulars;
                syncParticulars('__other__');
            }
        }

        if (!particulars && particularsInput && particularsInput.value && particularsSelect) {
            const savedOption = Array.from(particularsSelect.options).find(function (option) { return option.value === particularsInput.value; });
            if (savedOption) {
                particularsSelect.value = particularsInput.value;
                syncParticulars(particularsInput.value);
            } else {
                particularsSelect.value = '__other__';
                if (otherParticularsInput) otherParticularsInput.value = particularsInput.value;
                syncParticulars('__other__');
            }
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
            const salesReturnCheckbox = document.getElementById('disableFields');
            if (salesReturnCheckbox) salesReturnCheckbox.disabled = true;
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
        if (!cashCheckbox || !fonepayCheckbox) return;
    
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

    if (!checkbox) return;

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
    // Automatically settle amounts within Rs. 10 of the old due amount.
    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.getElementById('amount');
        const dueSpan = document.getElementById('totaldueamountfornotclear');
        const nilCheckbox = document.getElementById('nilaccount');
        if (!amountInput || !dueSpan || !nilCheckbox) return;

        const autoSettleWithinTolerance = function () {
            const due = parseFloat(String(dueSpan.textContent || '').replace(/,/g, ''));
            const entered = parseFloat(amountInput.value);
            const withinTolerance = Number.isFinite(due) && due > 0 && Number.isFinite(entered)
                && entered > 0 && Math.abs(entered - due) <= 10;

            if (withinTolerance) {
                nilCheckbox.checked = true;
                nilCheckbox.dataset.autoSettled = '1';
                if (Math.abs(entered - due) > 0.005) {
                    amountInput.value = due.toFixed(2).replace(/\.00$/, '');
                    if (typeof updateAmountInWords === 'function') updateAmountInWords();
                }
            } else if (nilCheckbox.dataset.autoSettled === '1') {
                nilCheckbox.checked = false;
                delete nilCheckbox.dataset.autoSettled;
            }
        };

        amountInput.addEventListener('input', autoSettleWithinTolerance);
        amountInput.addEventListener('change', autoSettleWithinTolerance);
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
            const particularsSelect = document.getElementById('particularsSelect');
            const otherParticulars = document.getElementById('otherParticulars');
            if (particularsSelect) particularsSelect.addEventListener('change', validateForm);
            if (otherParticulars) otherParticulars.addEventListener('input', validateForm);
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
<style>
    /* Payment form polish */
    .main-content:has(form[action*="cpayments"]) {
        background: #f3f7fc;
    }

    .main-content:has(form[action*="cpayments"]) > .container,
    .main-content:has(form[action*="cpayments"]) > .d-flex {
        max-width: 1240px;
        margin-left: auto !important;
        margin-right: auto !important;
    }

    .main-content:has(form[action*="cpayments"]) > .d-flex {
        gap: 10px;
        margin-top: 8px;
        margin-bottom: 18px;
    }

    .main-content:has(form[action*="cpayments"]) > .d-flex .btn {
        border: 0;
        border-radius: 10px;
        font-weight: 800;
        padding: 11px 16px;
        box-shadow: 0 5px 14px rgba(37, 99, 235, .14);
    }

    .main-content:has(form[action*="cpayments"]) h4 {
        color: #173b72;
        font-size: 25px;
        font-weight: 900;
        margin: 8px 0 16px;
    }

    form[action*="cpayments"] {
        position: relative;
        overflow: hidden;
        margin-top: 0 !important;
        padding: 24px 30px 28px !important;
        border-top: 5px solid #2563eb;
        box-shadow: 0 18px 45px rgba(23, 59, 114, .13) !important;
    }

    /* Customer and date details are displayed in the options row now. */
    form[action*="cpayments"] .payment-search-row.has-prefilled-customer {
        display: none !important;
    }

    form[action*="cpayments"] .py-4 {
        position: relative;
        min-height: 76px;
        padding: 0 0 18px !important;
        margin-bottom: 8px;
    }

    form[action*="cpayments"] #cname {
        display: inline-block;
        max-width: 430px;
        overflow: hidden;
        background: #173b72 !important;
        border-radius: 10px !important;
        padding: 10px 16px !important;
        color: #fff !important;
        font-size: 16px !important;
        text-overflow: ellipsis;
        white-space: nowrap;
        box-shadow: 0 6px 16px rgba(23, 59, 114, .2);
    }

    form[action*="cpayments"] .input-group-text {
        border-radius: 9px 0 0 9px;
    }

    form[action*="cpayments"] > .col-md-2,
    form[action*="cpayments"] > .col-md-3,
    form[action*="cpayments"] > .col-md-4 {
        min-height: 58px;
        padding: 10px 14px;
        transition: border-color .18s ease, background .18s ease, transform .18s ease;
    }

    form[action*="cpayments"] > .col-md-2:has(input:checked),
    form[action*="cpayments"] > .col-md-3:has(input:checked),
    form[action*="cpayments"] > .col-md-4:has(input:checked) {
        background: #eaf2ff;
        border-color: #75a7f8;
        transform: translateY(-1px);
    }

    form[action*="cpayments"] #particularsSelect,
    form[action*="cpayments"] #otherParticulars {
        min-height: 62px;
        border: 1px solid #b9cce4;
        border-radius: 10px;
        background-color: #fbfdff;
        font-size: 21px;
        font-weight: 900;
    }

    form[action*="cpayments"] #particularsSelect option {
        font-size: 20px;
        font-weight: 900;
        padding: 10px;
    }

    form[action*="cpayments"] label[for="particularsSelect"] {
        font-size: 15px !important;
        font-weight: 900 !important;
    }

    form[action*="cpayments"] #particularsSelect:focus,
    form[action*="cpayments"] #otherParticulars:focus,
    form[action*="cpayments"] #amount:focus,
    form[action*="cpayments"] textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        outline: 0;
    }

    form[action*="cpayments"] #amount {
        min-height: 54px;
        border: 2px solid #93b4e7;
        background: #f8fbff;
    }

    form[action*="cpayments"] textarea {
        resize: vertical;
        min-height: 112px !important;
    }

    form[action*="cpayments"] #amountInWords {
        display: inline-block;
        min-height: 25px;
        margin-top: 5px;
        color: #1744a0;
        font-size: 16px;
    }

    form[action*="cpayments"] #paymentSubmitBtn {
        letter-spacing: .2px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    @media (max-width: 768px) {
        .main-content:has(form[action*="cpayments"]) > .d-flex {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            margin: 8px 12px 14px;
        }

        .main-content:has(form[action*="cpayments"]) > .d-flex .btn {
            margin: 0 !important;
            width: 100%;
            font-size: 13px;
        }

        form[action*="cpayments"] #cname {
            max-width: 100%;
            font-size: 14px !important;
        }
    }

    /* Keep the payment card closer to the page heading. */
    .main-content:has(form[action*="cpayments"]) > .container:has(form[action*="cpayments"]) {
        margin-top: -18px;
    }

    .main-content:has(form[action*="cpayments"]) > .d-flex {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        transform: translateY(-10px);
    }

    /* Final usability pass: keep related fields visually grouped. */
    form[action*="cpayments"] {
        row-gap: 0 !important;
        margin: 0 !important;
        padding: 0 28px 22px !important;
        font-size: 20px;
    }

    form[action*="cpayments"] label:not(.form-check-label) {
        font-size: 17px !important;
        line-height: 1.25;
    }

    form[action*="cpayments"] .form-check-label {
        font-size: 20px !important;
        font-weight: 900;
    }

    form[action*="cpayments"] .input-group-text,
    form[action*="cpayments"] input[type="date"] {
        font-size: 19px !important;
    }

    form[action*="cpayments"] #particularsSelect,
    form[action*="cpayments"] #otherParticulars,
    form[action*="cpayments"] #amount,
    form[action*="cpayments"] textarea {
        font-size: 22px !important;
    }

    form[action*="cpayments"] #amountInWords {
        font-size: 21px !important;
        font-weight: 800;
    }

    form[action*="cpayments"] #paymentSubmitBtn {
        font-size: 22px !important;
    }

    /* Large-display mode for easier reading. */
    form[action*="cpayments"] {
        font-size: 22px;
    }

    form[action*="cpayments"] label:not(.form-check-label) {
        font-size: 19px !important;
    }

    form[action*="cpayments"] .form-check-label,
    form[action*="cpayments"] .input-group-text,
    form[action*="cpayments"] input[type="date"] {
        font-size: 22px !important;
    }

    form[action*="cpayments"] #particularsSelect,
    form[action*="cpayments"] #otherParticulars,
    form[action*="cpayments"] #amount,
    form[action*="cpayments"] textarea {
        font-size: 25px !important;
    }

    form[action*="cpayments"] #amountInWords {
        font-size: 24px !important;
    }

    form[action*="cpayments"] #paymentSubmitBtn {
        font-size: 25px !important;
    }

    form[action*="cpayments"] label:not(.form-check-label) {
        font-size: 22px !important;
    }

    form[action*="cpayments"] #particularsSelect,
    form[action*="cpayments"] #otherParticulars {
        font-size: 30px !important;
        min-height: 68px;
    }

    form[action*="cpayments"] #particularsSelect option {
        font-size: 28px !important;
        font-weight: 900;
    }

    form[action*="cpayments"] > .col-md-6:has(#amount),
    form[action*="cpayments"] > .col-md-6:has(textarea[name="notes"]) {
        margin-top: 12px !important;
    }

    form[action*="cpayments"] > .col-md-6:has(#particularsSelect) {
        margin-top: 12px !important;
    }

    form[action*="cpayments"] .payment-section-divider {
        flex: 0 0 100%;
        width: 100%;
        height: 1px;
        margin: 8px 0 0;
        border-top: 2px solid #d7e2f0;
    }

    form[action*="cpayments"] .py-4 {
        height: 40px !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        justify-content: flex-start !important;
        gap: 16px;
        align-items: center !important;
    }

    form[action*="cpayments"] .py-4 > div:nth-child(2) {
        margin-left: 0 !important;
    }

    form[action*="cpayments"] .py-4 > div:last-child {
        margin-left: auto;
    }

    form[action*="cpayments"] .py-4 > div:first-child {
        margin-left: 0 !important;
    }

    form[action*="cpayments"] .py-4 > div:first-child,
    form[action*="cpayments"] .py-4 > div:first-child .input-group,
    form[action*="cpayments"] .py-4 > div:first-child .search-box {
        height: 40px;
    }

    form[action*="cpayments"] .py-4 > div:first-child .search-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    form[action*="cpayments"] .py-4 > div:first-child .search-icon {
        top: 50% !important;
        transform: translateY(-50%);
    }

    form[action*="cpayments"] > .col-md-2,
    form[action*="cpayments"] > .col-md-3,
    form[action*="cpayments"] > .col-md-4 {
        min-height: 50px;
        padding: 8px 14px;
    }

    form[action*="cpayments"] > .col-md-3:has(#isCheque),
    form[action*="cpayments"] > .col-md-4:has(#nilaccount) {
        flex: 0 0 50% !important;
        width: 50% !important;
        max-width: 50% !important;
    }

    form[action*="cpayments"] > .customer-summary-slot {
        flex: 0 0 33.3333% !important;
        width: 33.3333% !important;
        max-width: 33.3333% !important;
        display: flex;
        align-items: center;
        min-height: 50px;
        padding: 8px 14px;
    }

    form[action*="cpayments"] > .customer-summary-slot #cname {
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    form[action*="cpayments"] > .col-md-3:has(#isCheque),
    form[action*="cpayments"] > .col-md-4:has(#nilaccount) {
        flex: 0 0 220px !important;
        width: 220px !important;
        max-width: 220px !important;
    }

    form[action*="cpayments"] > .customer-summary-slot {
        flex: 1 1 auto !important;
        width: auto !important;
        max-width: none !important;
    }

    form[action*="cpayments"] > .date-summary-slot {
        flex: 0 0 300px;
        width: 300px;
        max-width: 300px;
        display: flex;
        align-items: center;
    }

    form[action*="cpayments"] > .date-summary-slot .input-group {
        width: 100%;
        margin-bottom: 0 !important;
    }

    form[action*="cpayments"] > .col-md-6:has(#particularsSelect) {
        margin-top: -4px;
    }

    form[action*="cpayments"] #particularsSelect {
        min-height: 56px;
        cursor: pointer;
    }

    form[action*="cpayments"] .particulars-picker-row {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    form[action*="cpayments"] .particulars-picker-row #particularsSelect {
        min-width: 0;
        flex: 1 1 auto;
    }

    form[action*="cpayments"] #otherParticularsWrap {
        flex: 0 0 42%;
        margin-top: 0 !important;
    }

    form[action*="cpayments"] #otherParticularsWrap input {
        min-height: 56px;
        width: 100%;
    }

    form[action*="cpayments"] #amount,
    form[action*="cpayments"] textarea {
        min-height: 52px;
    }

    form[action*="cpayments"] textarea {
        min-height: 104px !important;
    }

    form[action*="cpayments"] #amountInWords {
        margin-top: 0;
        padding: 6px 10px;
        border-left: 4px solid #2563eb;
        border-radius: 4px;
        background: #f1f6ff;
    }

    form[action*="cpayments"] .d-grid {
        margin-top: 2px;
        padding-bottom: 0 !important;
    }

    form[action*="cpayments"] #paymentSubmitBtn {
        min-height: 56px;
        font-size: 18px;
    }

    .main-content:has(form[action*="cpayments"]) h4 {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        transform: translateY(-10px);
    }

    @media (max-width: 768px) {
        .main-content:has(form[action*="cpayments"]) > .container:has(form[action*="cpayments"]) {
            margin-top: -8px;
        }

        .main-content:has(form[action*="cpayments"]) > .d-flex,
        .main-content:has(form[action*="cpayments"]) h4 {
            transform: none;
        }

        form[action*="cpayments"] .particulars-picker-row {
            align-items: stretch;
        }

        form[action*="cpayments"] #otherParticularsWrap {
            flex-basis: 42%;
        }

        form[action*="cpayments"] > .customer-summary-slot {
            flex-basis: 100% !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        form[action*="cpayments"] > .date-summary-slot {
            flex-basis: 100%;
            width: 100%;
            max-width: 100%;
        }

        form[action*="cpayments"] > .col-md-3:has(#isCheque),
        form[action*="cpayments"] > .col-md-4:has(#nilaccount) {
            flex-basis: 50% !important;
            width: 50% !important;
            max-width: 50% !important;
        }
    }
</style>
@stop
