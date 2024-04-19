@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')
    <div class="container">
        @if (Session::has('success'))
            <div class="alert bg-success text-white w-50">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <div class="container">
        <form class="row gx-5 gy-3" action="{{ route('chequedeposit.store') }}" method="post">
            @csrf

           
            
            <!-- Move Date input to the top right -->
            <div class="col-md-3">
                <div>
                    <label for="date" class="form-label" style="white-space: nowrap;">Date *</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                        name="date" value="{{ old('date') ?: date('Y-m-d') }}" id="date" placeholder="YYYY-MM-DD">
                    @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-3">
                    <a href="{{ route('chequedeposit.index') }}" class="btn btn-success" target="" rel="noopener noreferrer">
                        <i class="fas fa-list"></i> View Cheque Deposit <!-- Icon added before text -->
                    </a>
            
            </div>

            <!-- Search Customer input -->
            <div class="col-md-6">
                <div class="py-4 d-flex justify-content-end align-items-start">
                    <div class="w-100">
                        <div class="search-box">
                            <input id="customerIdInput" name="customerid" hidden>

                            <input type="text" class="search-input @error('customerid') is-invalid @enderror" placeholder="Search Customer *"
                                id="searchCustomerInput" data-api="customer_search" autocomplete="off">
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
            </div>
            

            <!-- Bank Name input -->
            <div class="col-md-6">
                <label for="bankName" class="form-label">Bank Name *</label>
                <input autocomplete="off" type="text" class="form-control @error('bank_name') is-invalid @enderror" 
                    name="bank_name" value="{{ old('bank_name') }}" id="bankName" placeholder="Bank Name *">
                @error('bank_name')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cheque Date input -->
            <div class="col-md-6">
                <label for="chequeDate" class="form-label">Cheque Date *</label>
                <input autocomplete="off" type="date" class="form-control @error('cheque_date') is-invalid @enderror" 
                    name="cheque_date" value="{{ old('cheque_date') }}" id="chequeDate" placeholder="YYYY-MM-DD">
                @error('cheque_date')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

             <!-- Amount input -->
             <div class="col-md-6">
                <label for="amount" class="form-label">Amount *</label>
                <input  autocomplete="off" type="text" class="form-control @error('amount') is-invalid @enderror" 
                    name="amount" value="{{ old('amount') }}" id="amount" placeholder="Amount *">
                @error('amount')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea type="text" class="form-control" 
                    name="notes" id="notes" placeholder="Notes"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('form').submit(function () {
            // Disable the submit button
            $('#submitBtn').prop('disabled', true);
        });
    });
</script>

@stop
