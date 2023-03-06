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

            <button type="">Select customer</button>
            <input type="text" name="cid" id="cid" placeholder="Enter customer ID">
        </div>
        <br>


        <form class="row gx-5 gy-3" action="{{ route('itemsales.store') }}" method="post">
            @csrf
            {{-- <input type="hidden" name="allSalesData" value="[{}]" /> --}}
            <div class="row">
                <table class="invoicetable table-responsive">
                    <tbody id="invoiceTableBody" style="max-height: none;">
                        <tr>
                            <th>
                                <button class=" btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></button>
                            </th>
                            <th class="w-25"> Product</th>
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
                        <label class="my-3"><b>Amount in words: </b>Three thousand four hundred fifty-six
                            fifty-sixhundred fifty-six fifty-six</label><br>
                        <textarea placeholder="Additional notes" class="form-control" rows="3" cols="20"></textarea>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="basic-addon1">Sub-Total</span>
                            <input type="text" class="form-control" placeholder="Sub-Total" aria-label="Username"
                                aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Discount</span>
                            <input type="text" class="form-control" placeholder="Sub-Total" aria-label="Username"
                                aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">Total</span>
                            <input type="text" class="form-control" placeholder="" aria-label="Username"
                                aria-describedby="basic-addon1">
                        </div>
                        <br>
                        <button class="btn btn-success btn-lg">Save & Print</button>
                        <button class="btn btn-success btn-lg">Save</button>
                    </div>
                </div>
            </div>
    </div>
@stop
