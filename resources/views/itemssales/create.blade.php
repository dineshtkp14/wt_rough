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

            <div class="toggle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                <i class="fas fa-user"></i>
            </div>
            
        </div>
        @yield('breadcrumb')
        
      
            
        
        



        <div class="container-fluid">
           <span class="h4"> Invoice N0: <span class="h3">{{ $nextgenid }}</span> </span>

        <span class="float-end" style="margin-top: -100px; margin-right:500px;">
            <a href="{{ route('customerinfos.create') }}" class="btn btn-primary m"> <i class="fa-solid fa-plus"></i> Add New Customer</a>
            <a href="{{ route('onlyviewbillafterbill') }}" class="btn ms-5" style="background-color: #556B2F; border-color:rgb(29, 3, 3); color: #ffffff;"> <i class="fa-solid fa-eye"></i> Search Invoice</a>
        </span>
            <form action="{{ route('itemsales.store') }}" method="post">


                

               



                @csrf
                <div class="py-4 d-flex justify-content-between align-items-start">
                    
                    <div style="width: 400px">
                        <div class="search-box">
                           <input type="text" class="search-input" placeholder="Search Customer"
                                id="searchCustomerInput" data-api="customer_search" autocomplete="off">
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


                    



                    <div style="width: 300px"   >
                        <select id="invoice_type" name="invoice_type" class="d-inline form-select select-background"  onchange="changeBackgroundColor(this)">
                            <option value="">--Choose Invoice Type--</option>
                            <option value="cash">CASH </option>
                            <option value="credit">CREDIT </option>
                           
                        </select>
                        <small style="font-size: 14px; padding:20px; color:#02090f;"> Choose mode of invoice &nbsp;    (cash / Credit) </small>
                    </div>

                    
                    <div style="width: 300px; " class="">
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
                            <th>#</th> <!-- Serial number column -->

                            <th>
                                <a class=" btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></a>
                            </th>
                            
                            <th>Product</th>
                            <th class="unstockedth">Unstocked Name</th>
                            <th>Quantity</th>
                            <th>Unit (pcs/kg) </th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </tbody>
                </table>

                <div class="row mt-5 mb-4 p-0">
                    <div class="col-md-9">
                        <div class="">
                            <label class="my-3"><b>Amount in words: </b><span id="totalAmountWords"
                                    style="text-transform: capitalize;">...</span></label><br>
                            <textarea autocomplete="off" placeholder="Additional notes" class="form-control" id="noteInput" rows="3" cols="20"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Sub Total (Rs.)</span>
                                <input autocomplete="off" type="text" class="form-control" placeholder="0.00" id="subTotalInputFinal"
                                    data-name="subtotal" name="test" disabled>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Discount (Rs.)</span>
                                <input autocomplete="off" type="text" class="form-control sales-input-final" placeholder="0.00"
                                    id="discountInputFinal" data-name="discount">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Total (Rs.)</span>
                                <input autocomplete="off" type="text" class="form-control" placeholder="0.00" id="totalInputFinal"
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

        <div class="modal-wrapper" id="modalWrapper" style="display: none;">
            <div class="modal-container flex-css" id="modalContainer" data-close="true">
                <div class="modal-box">
                    <div class="title flex-css mb-4">
                        <h1>Select Product</h1>
                    </div>
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Search Product" id="searchProductInput" autocomplete="off">
                        <i class="fas fa-search search-icon modal-search-icon"> </i>
                        <div class="result-wrapper modal-result-wrapper" id="productResultWrapper"
                            style="display: none;">
                            <div class="result-box d-flex justify-content-start align-items-center"
                                id="productLoadingResultBox">
                                <i class="fas fa-spinner" id="spinnerIcon"> </i>
                                <h1 class="m-0 px-2"> Loading</h1>
                            </div>

                            <div class="result-box d-flex justify-content-start align-items-center d-none"
                                id="productNotFoundResultBox">
                                <i class="fas fa-triangle-exclamation"> </i>
                                <h1 class="m-0 px-2"> Record Not Found</h1>
                            </div>

                            <div id="productResultList">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>

$(document).ready(function () {
        $('form').submit(function () {
            // Disable the submit button
            $('#submitBtn').prop('disabled', true);
            
        });
    });


        function changeBackgroundColor(selectElement) {
            var selectedValue = selectElement.value;
    
            if (selectedValue === 'cash') {
                selectElement.classList.remove('credit-bg');
                selectElement.classList.add('cash-bg');
            } else if (selectedValue === 'credit') {
                selectElement.classList.remove('cash-bg');
                selectElement.classList.add('credit-bg');
            }
        }
    
        // Set the initial background color based on the default selected value
        changeBackgroundColor(document.querySelector('select[name="invoice_type"]'));




    </script>
    
    <style>
        .select-background {
            background-color: white;
            font-size: 25px;
        }
    
        .cash-bg {
            background-color: white;
        }
    
        .credit-bg {
            background-color: rgb(216, 18, 141) !important;
            color: white;
        }
        
    </style>

   
@stop