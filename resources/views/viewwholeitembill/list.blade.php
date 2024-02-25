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
            <div class="col-md-3">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Search ok Invoice</h5>
                        <form action="{{ route('ViewWholeitemsBill.index') }}" method="get" id="chosendatepdfform">
                            <div class="mb-3">
                                <label for="invoiceid" class="form-label">Enter Bill No</label>
                                <input type="number" class="form-control" id="invoiceid" name="billno" placeholder="Enter Bill No" required>
                            </div>

                            <div class="search-box">
                                <input id="customerIdInput" name="companyid" hidden>
                                <input type="text" class="search-input @error('companyid') is-invalid @enderror" placeholder="Search Company Name" id="searchCustomerInput"  data-api="company_search" autocomplete="off">
                                @error('companyid')
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

                                    <div id="customerResultList">
                                    </div>
                                </div>
                            </div>
                            <br>

                            <button type="submit" class="btn btn-primary btn-lg btn-block" style="width: 100%;">Search</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div>
                    <h4>Bill Number: {{ $billNo }}</h4>
                    <h4>Company Name: {{ $companyName }}</h4>
                </div>
            </div>
        </div>
    </div>

   

    <div class="container">
        <div class="card customer-card mb-4" id="customerCard" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Customer Info</h5>
                <!-- Customer info placeholders -->
            </div>
            <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                <i class="fas fa-user"></i>
            </div>
        </div>

        @if ($all->isEmpty())
           <h3>No items found. !!!!</h3>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ITEM ID</th>
                        <th>ITEM Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Cost Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($all as $i)
                        <tr>
                            <td>{{$i->id}}</td>
                            <td>{{$i->itemsname}}</td>
                            <td>{{$i->quantity}}</td>
                            <td>{{$i->unit}}</td>
                            <td>{{$i->costprice}}</td>
                            <td>{{$i->total}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5"></td>
                        <td><b>Total-: {{ $totalSum }}</b></td>

                    </tr>
                  
                </tbody>
            </table>
        @endif

        
        
        <p style="margin-top: 20px; font-size: 14px; text-align: center;">Notes:  Goods once sold won't be returned</p>
    </div>
</div>

<script>
    // JavaScript for Delete Form, Update Form, and numerical value conversion
</script>

@stop
