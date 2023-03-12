@extends('layouts.master')
@section('content')

<Center><h1 class="text-danger mt-5 bold"><U>SELL PRODUCTS</U></h1></Center>

    <div class="cl mt-5"></div>

    <div class="container mt-5">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <div class="container">

        <div class="mb-3">
            <a class="d-block" href="/daybooks/">Back</a>
        </div>

        <br>


        {{-- forproductdata --}}
      

        <!-- Dropdown -->
        <select class="sales-input-final" id='selectCustomerInput' data-name="customer">
            <option value='' selected disabled>Select Customer</option>
            @foreach ($all as $i)
                <option value='{{ $i->id }}' data-name={{ $i->name }} data-address={{ $i->address }}
                    data-email={{ $i->email }} data-phone={{ $i->phoneno }}>{{ $i->name }}
                </option>
            @endforeach
        </select>

        <br>
        <br>

        <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
            <div class="card-body">
                <h5 class="card-title">Customer Info</h5>
                <p>
                    <span>ID: </span><span id="customerPhone">...</span>
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
                    <span>PhoneN: </span><span id="customerPhone">...</span>
                </p>
            </div>
        </div>

        <form action="{{ route('itemsales.store') }}" method="post">
            @csrf
            <input type="text" name="particulars" placeholder="particulars">
            <input type="hidden" id="salesArrInput" name="sales_arr" value="" />
            <input type="hidden" id="finalArrInput" name="final_arr" value="" />
            <table class="invoicetable table-responsive">
                <tbody id="invoiceTableBody" style="max-height: none;">
                    <tr>
                        <th>
                            <button class=" btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></button>
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
                            <span class="input-group-text">Discount (%)</span>
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
@stop