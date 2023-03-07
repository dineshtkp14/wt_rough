@extends('layouts.master')
@section('content')

    <h2 class="bg-warning"> Customer Details</h2>

    <div class="cl mt-5"></div>

    <div class="container mt-5">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <div class="container">
        <div class="mb-4">
            <a class="d-block" href="/daybooks/">Back</a>
        </div>


        <form class="row gx-5 gy-3" action="{{ route('itemsales.store') }}" method="post">
            @csrf
            <input type="hidden" id="salesArrInput" name="sales_arr" value="" />
            <input type="hidden" id="finalArrInput" name="final_arr" value="" />

            <div class="row">
                <table class="invoicetable table-responsive">
                    <tbody id="invoiceTableBody" style="max-height: none;">
                        <tr>
                            <th>
                                <button class=" btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></button>
                            </th>
                            <th class="w-25"> Product</th>
                            <th>Unstocked Name</th>
                            <th>Quantity</th>
                          
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                        </tr>
                    </tbody>
                </table>
            </div>

            <br><br>
            <br><br>

            <div class="row my-5 p-0">
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
                            <span class="input-group-text sales-input-final">Sub Total (Rs.)</span>
                            <input type="text" class="form-control" placeholder="0.00" id="subTotalInputFinal"
                                data-name="subtotal" disabled>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text sales-input-final">Discount (%)</span>
                            <input type="text" class="form-control" placeholder="0.00" id="discountInputFinal"
                                data-name="discount">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text sales-input-final">Total (Rs.)</span>
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
