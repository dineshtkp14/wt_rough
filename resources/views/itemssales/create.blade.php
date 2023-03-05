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
            <div class="row">
            <table class="invoicetable table-responsive">
                <tbody style="max-height: none;">
                    <tr>
                        <th>
                            <button class=" btn btn-success">+</button>
                        </th>
                        <th class="w-25"> Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Subtotal</th>
                    </tr>
                    <tr>
                        <td><button class=" btn btn-danger">X</button></td>
                        <td>
                            <input type="text" class= "w-100 inputwidth form-control @error('itemid') is-invalid @enderror"
                                name="itemid" value="{{ old('itemid') }}" placeholder="Enter a Product">
                            @error('itemid')
                                <p class="invalid-feedback">{{ $message }}</p>
                            @enderror <a href="">or Select a Product</a>

                        </td>

                        <td>
                            <input type="text" placeholder="Quantity"
                                class="form-control @error('itemid') is-invalid @enderror" name="itemid"
                                value="{{ old('itemid') }}">
                            @error('itemid')
                                <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">$</span>
                                <input type="text" placeholder="Price"
                                    class="form-control @error('itemid') is-invalid @enderror" name="itemid"
                                    value="{{ old('itemid') }}">
                                @error('itemid')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                        </td>
                        <td>
                            <input type="text" placeholder="Discount"
                                class="form-control @error('itemid') is-invalid @enderror" name="itemid"
                                value="{{ old('itemid') }}">
                            @error('itemid')
                                <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text" id="basic-addon1">$</span>
                                <input type="text" placeholder="Sub-Total"
                                    class="form-control @error('itemid') is-invalid @enderror" name="itemid"
                                    value="{{ old('itemid') }}">
                                @error('itemid')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                        </td>
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
