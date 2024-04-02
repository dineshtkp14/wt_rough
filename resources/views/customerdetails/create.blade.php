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
    <div class="card customer-card mb-4" id="customerCard" style="display: none;">
        <div class="card-body">
            <h5 class="card-title">Customer Info</h5>
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
                <div style="width: 300px">
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
            <div class="col-md-12">
                <div class="form-check d-flex align-items-center">
                    <input class="form-check-input me-2" type="checkbox" id="disableFields" name="disableFields" style="width: 30px; height: 30px;">
                    <label class="form-check-label" for="disableFields">If Sales Return</label>
                </div>
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label"> Particulars (Bank Name/Fone Pay/Payment) <span style="color: red;">*</span></label>
                <input id="particulars" type="text" class="form-control @error('particulars') is-invalid @enderror" name="particulars" value="{{ old('particulars') }}" >
                <input id="hiddenParticulars" type="hidden" name="hiddenParticulars" value="{{ old('hiddenParticulars') }}">
                @error('particulars')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Voucher Type (Receipt/Cash) <span style="color: red;">*</span></label>
                <input id="vt" type="text" class="form-control @error('vt') is-invalid @enderror" name="vt" value="{{ old('vt') }}" >
                <input id="hiddenVt" type="hidden" name="hiddenVt" value="{{ old('hiddenVt') }}">
                @error('vt')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <!-- Your existing HTML code -->
            <div class="col-md-6" id="additionalFieldContainer" style="display: none;">
                <label for="cninvoiceid" class="form-label">Credit Notes Invoice ID</label>
                <input type="text" class="form-control" id="cninvoiceid" name="cninvoiceid">
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Amount <span style="color: red;">*</span></label>
                <input type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" >
                @error('amount')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

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

</script>
@stop
