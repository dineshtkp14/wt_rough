@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content"> 
    @yield('breadcrumb')

    <div class="container">
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('openingbalances.update', $openingBalance->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                       
                        
                        <div class="mb-4 col-md-6 ">
                            <div class="py-4 d-flex justify-content-between align-items-center">
                                <div style="width: 500px">
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
                                
                            </div>
                        </div>
                      

                        <div class="mb-4 col-md-6">
                            <div class="form-group">
                                <label for="date">Date:</label>
                                <input type="date" name="date" id="date" class="form-control form-control-lg" value="{{ old('date', $openingBalance->date) }}" required>
                            </div>
                        </div>

                       

                        <div class="mb-4 col-md-6">
                            <div class="form-group">
                                <label for="amount">Amount:</label>
                                <input type="number" name="amount" id="amount" class="form-control form-control-lg" value="{{ old('amount', $openingBalance->debit) }}" required>
                            </div>
                        </div>

                        <div class="mb-4 col-md-6"> 
                            <div class="form-group">
                                <label for="notes">Notes:</label>
                                <textarea name="notes" id="notes" class="form-control form-control-lg">{{ old('notes', $openingBalance->notes) }}</textarea>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4"> <!-- Add margin bottom to the form -->
                        <button type="submit" class="btn btn-primary btn-lg">Update Opening Balance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@stop
