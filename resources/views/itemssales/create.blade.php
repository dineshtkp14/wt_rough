@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

    <div class="main-content">
        <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
            <div class="card-body">
                <h5 class="card-title">Customer Info</h5>
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
        @yield('breadcrumb')

        <div class="container ">
            @if (Session::has('success'))
                <div class="alert alert-success w-50">
                    {{ Session::get('success') }}
                </div>
            @endif
        </div>

        <div class="container">

            <h2 class=""> Bill N0:{{ $nextgenid }} </h2>

            <form action="{{ route('itemsales.store') }}" method="post">
                @csrf
                <div class="py-4 d-flex justify-content-between align-items-start">
                    <div style="width: 300px">
                        <div class="search-box-cus">
                            <input type="tetxt" class="searchcustomerinput" placeholder="Search Customer"
                                id="searchCustomerInput"> <i class="fas fa-search search-icon"> </i>
                            <div class="searchresult" id="searchResult" style="display: none;">

                                <div class="result-box d-flex justify-content-start align-items-center"
                                    id="loadingResultBox">
                                    <i class="fas fa-spinner" id="spinnerIcon"> </i>
                                    <h1 class="m-0 px-2"> Loading</h1>
                                </div>

                                <div class="result-box d-flex justify-content-start align-items-center d-none"
                                    id="notFoundResultBox">
                                    <i class="fas fa-triangle-exclamation"> </i>
                                    <h1 class="m-0 px-2"> Record Not Found</h1>
                                </div>
                                
                                <div id="customerResult">

                                </div> 

                            </div>
                        </div>
                    </div>

                    <div style="width: 300px">

                        <select name="modeofinvoice" class=" d-inline form-select" aria-label="Default select example">

                            <option value="credit" selected>Credit</option>
                            <option value="cash">Cash</option>

                        </select>
                        <small style="font-size: 12px; color:#6c757d;"> Choose mode of invoice </small>
                    </div>


                    <div style="width: 300px">

                        <div class="input-group mb-1">
                            <span class="input-group-text">Date:</span>
                            <input type="date" class="form-control" placeholder="" id="salesDate"
                                class="form-control foritemsaledatecss" value="{{ now()->format('Y-m-d') }}" name="date">
                        </div>
                    </div>

                </div>
                <input type="hidden" id="salesArrInput" name="sales_arr" value="" />
                <input type="hidden" id="finalArrInput" name="final_arr" value="" />
                <table class="invoicetable table-responsive bg-white">
                    <tbody id="invoiceTableBody" style="max-height: none;">
                        <tr>
                            <th>
                                <a class=" btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></a>
                            </th>
                            <th style="width: 20%;"> Product</th>
                            <th>Unstocked Name</th>
                            <th>Quantity</th>

                            <th>Price</th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                        </tr>
                    </tbody>
                </table>

                <div class="row mt-5 mb-4 p-0">
                    <div class="col-md-9">
                        <div class="">
                            <label class="my-3"><b>Amount in words: </b><span id="totalAmountWords"
                                    style="text-transform: capitalize;">...</span></label><br>
                            <textarea placeholder="Additional notes" class="form-control" id="noteInput" rows="3" cols="20"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Sub Total (Rs.)</span>
                                <input type="text" class="form-control" placeholder="0.00" id="subTotalInputFinal"
                                    data-name="subtotal" disabled>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Discount (Rs.)</span>
                                <input type="text" class="form-control sales-input-final" placeholder="0.00"
                                    id="discountInputFinal" data-name="discount">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Total (Rs.)</span>
                                <input type="text" class="form-control" placeholder="0.00" id="totalInputFinal"
                                    data-name="total" disabled>
                            </div>
                            <br>
                            <div class="error-message mb-2">
                                <small class="text-danger fw-bold" id="errorText"></small>
                            </div>
                            <button class="btn btn-primary btn-md" id="verifyBtn">Verify</button>
                            <button class="btn btn-success btn-md" type="submit" id="submitBtn" disabled>Submit</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
