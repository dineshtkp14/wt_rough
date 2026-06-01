@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

    <div class="main-content">




        <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
            <div class="card-body">

                <h5 class="card-title">Customer Information</h5>
                <p>
                    <span>ID: </span><span id="customerId"  class="custoinfo-bold-text">...</span>
                </p>
                <p class="card-text">
                    <span>Name: </span><span id="customerName" class="custoinfo-bold-text">...</span>
                </p>
                <p>
                    <span>Addres: </span><span id="customerAddress" class="custoinfo-bold-text">...</span>
                </p>
                <p>
                    <span>E-mail: </span><span id="customerEmail" class="custoinfo-bold-text">...</span>
                </p>
                <p>
                    <span>PhoneNo: </span><span id="customerPhone" class="custoinfo-bold-text">...</span>
                </p>
            </div>

            <div class="toggle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                <i class="fas fa-user"></i>
            </div>
            
        </div>
        @yield('breadcrumb')
        
      
            
        
        



        <div class="container-fluid invoice-create-page">

        <span class="invoice-quick-actions">
            <a href="{{ route('customerinfos.create') }}" class="btn btn-primary m"> <i class="fa-solid fa-plus"></i> Add New Customer</a>
            <a href="{{ route('onlyviewbillafterbill') }}" class="btn ms-5" style="background-color: #556B2F; border-color:rgb(29, 3, 3); color: #ffffff;"> <i class="fa-solid fa-eye"></i> Search Invoice</a>
        </span>
            <form action="{{ route('itemsales.store') }}" method="post">


                

               



                @csrf
                <div class="invoice-top-controls pt-0 pb-4 d-flex justify-content-between align-items-start" style="margin-top: 2px;">
                    
                    <div class="invoice-control customer-control" style="width: 400px">
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


                    



                    <div class="invoice-control type-control" style="width: 350px"   >
                        <select id="invoice_type" name="invoice_type" class="d-inline form-select select-background"  onchange="changeBackgroundColor(this)">
                            <option value="">--Choose Invoice Type--</option>
                            <option value="cash">CASH </option>
                            <option value="credit">CREDIT </option>
                           
                        </select>
                        <small style="font-size: 14px; padding:20px; color:#02090f;"> Choose mode of invoice &nbsp;    (cash / Credit) </small>
                    </div>

                    
                    <div class="invoice-control date-control" style="width: 250px; " class="">
                        <div class="input-group mb-1">
                            <span class="input-group-text">Date:</span>
                            <input type="date" class="form-control" placeholder="" id="salesDate"
                                class="form-control foritemsaledatecss" value="{{ now()->format('Y-m-d') }}" name="date">
                        </div>
                        <small class="text-muted">
                            Nepali Date: {{ \App\Support\NepaliDate::adToBsString(now()->toDateString(), 'en') }}
                        </small>
                        
                    </div>
                  
                   
                
                   

                </div>
                <input type="hidden" id="salesArrInput" name="sales_arr" value="" />
                <input type="hidden" id="finalArrInput" name="final_arr" value="" />
                <div class="invoice-table-shell">
                    <table class="invoicetable table-responsive bg-white">
                        <tbody id="invoiceTableBody" style="max-height: none;">
                            <tr>
                                <th>#</th> <!-- Serial number column -->

                                <th>
                                    <a class=" btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></a>
                                </th>
                                
                                <th>Item</th>
                                <th class="unstockedth">Unstocked Item</th>
                                <th>Quantity</th>
                                <th>Unit (pcs/kg) </th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="invoice-bottom-grid row mt-5 mb-4 p-0">
                    <div class="col-md-9 invoice-notes-panel">
                        <div class="">
                            <label class="my-3"><b>Amount in words: </b><span id="totalAmountWords"
                                    style="text-transform: capitalize;">...</span></label><br>
                            <textarea autocomplete="off" placeholder="Additional notes" class="form-control" id="noteInput" rows="3" cols="20"></textarea>

                            {{-- /forcreditdaystextbox --}}
                            <div class="d-flex justify-content-center credit-days-holder">
                                <div class="d-inline-flex align-items-center mt-3 px-3 py-2 border rounded shadow-sm"
                                     id="creditDaysWrapper" style="display:none; background:#f8f9fa;">
                            
                                    <span class="fw-semibold me-2" style="font-size:16px;">
                                        ⏳ Credit Days
                                    </span>
                            
                                    <input type="number"
                                        class="form-control"
                                        id="creditDays"
                                        name="credit_days"
                                        placeholder="Enter days"
                                        min="0"
                                        step="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        style="width:130px; font-size:16px; font-weight:600; text-align:center;">

                                    </div>
                            </div>
                            
                       {{-- //endforcredidays --}}

                        </div>
                    </div>
                    <div class="col-md-3 invoice-total-panel">
                        <div class="invoice-total-box">
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
                            <button class="btn btn-primary btn-md invoice-action-btn" id="verifyBtn">Verify</button>
                            <button class="btn btn-success btn-md invoice-action-btn" type="submit" id="submitBtn" style="display: none;" disabled>Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-wrapper" id="modalWrapper" style="display: none;">
            <div class="modal-container flex-css" id="modalContainer" data-close="true">
                <div class="modal-box">
                    <div class="title flex-css mb-4">
                        <h1>Select Items</h1>
                    </div>
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Search Items" id="searchProductInput" autocomplete="off">
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



        //forboldtext
        $("#customerId").css("font-weight", "bold");


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

        .old-price-wrapper {
            position: relative;
        }

        .old-price-result-box {
            background: #ffffff;
            border: 1px solid #ced4da;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.15);
            max-height: 260px;
            min-width: 320px;
            overflow-y: auto;
            position: fixed;
            z-index: 9999;
        }

        .invoicetable td {
            overflow: visible !important;
        }

        .old-price-result-item {
            background: #ffffff;
            border: 0;
            border-bottom: 1px solid #e9ecef;
            color: #111827;
            display: grid;
            gap: 2px;
            padding: 8px 10px;
            text-align: left;
            width: 100%;
        }

        .old-price-result-item:hover {
            background: #fff3cd;
        }

        .old-price-result-item small {
            color: #6c757d;
        }

        .old-price-empty {
            color: #dc3545;
            font-size: 14px;
            padding: 8px 10px;
        }

        .invoice-action-btn {
            min-width: 300px;
            padding-left: 24px;
            padding-right: 24px;
        }

        .invoice-create-page {
            padding-bottom: 24px;
        }

        .invoice-quick-actions {
            display: flex;
            justify-content: flex-end;
            gap: 14px;
            margin: -88px 0 42px;
            padding-right: 28px;
        }

        .invoice-quick-actions .btn {
            align-items: center;
            display: inline-flex;
            gap: 7px;
            min-height: 42px;
            white-space: nowrap;
        }

        .invoice-top-controls {
            gap: 18px;
        }

        .invoice-control {
            flex: 0 1 auto;
        }

        .invoice-control .search-box,
        .invoice-control .search-input,
        .invoice-control .form-select {
            width: 100%;
        }

        .invoice-table-shell {
            background: #ffffff;
            border: 1px solid #d5deea;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
        }

        .invoice-table-shell::after {
            color: #64748b;
            content: "Swipe table left/right on mobile";
            display: none;
            font-size: 12px;
            font-weight: 800;
            padding: 8px 12px;
            text-align: center;
        }

        .invoicetable {
            border-collapse: collapse;
            margin: 0;
            min-width: 1040px;
            width: 100%;
        }

        .invoicetable th,
        .invoicetable td {
            padding: 10px;
            vertical-align: middle;
        }

        .invoicetable th {
            background: #3348d4;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            white-space: nowrap;
        }

        .invoicetable .form-control,
        .invoicetable .form-select {
            min-height: 42px;
            font-size: 16px;
        }

        .invoice-total-box {
            background: #ffffff;
            border: 1px solid #d5deea;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
            padding: 14px;
        }

        .invoice-total-box .input-group-text {
            font-weight: 800;
        }

        .invoice-total-box .form-control {
            min-height: 44px;
            font-size: 17px;
            font-weight: 800;
            text-align: right;
        }

        .invoice-notes-panel textarea {
            min-height: 96px;
            font-size: 16px;
        }

        #creditDaysWrapper {
            max-width: 100%;
        }

        @media (max-width: 1200px) {
            .invoice-quick-actions {
                margin: 0 0 18px;
                padding-right: 0;
            }

            .invoice-top-controls {
                align-items: stretch !important;
                flex-wrap: wrap;
            }

            .invoice-control,
            .customer-control,
            .type-control,
            .date-control {
                width: calc(50% - 9px) !important;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding-left: 10px;
                padding-right: 10px;
            }

            .invoice-create-page {
                padding-left: 0;
                padding-right: 0;
            }

            .invoice-quick-actions {
                align-items: stretch;
                flex-direction: column;
                gap: 10px;
            }

            .invoice-quick-actions .btn {
                justify-content: center;
                margin-left: 0 !important;
                width: 100%;
            }

            .invoice-top-controls {
                flex-direction: column;
            }

            .invoice-control,
            .customer-control,
            .type-control,
            .date-control {
                width: 100% !important;
            }

            .select-background {
                font-size: 18px;
                min-height: 48px;
            }

            .invoice-control small {
                display: block;
                padding: 6px 0 0 !important;
            }

            .invoice-table-shell::after {
                display: block;
            }

            .invoicetable {
                min-width: 920px;
            }

            .invoicetable th,
            .invoicetable td {
                padding: 8px;
            }

            .invoicetable .btn {
                min-height: 42px;
                min-width: 42px;
            }

            .invoice-bottom-grid {
                margin-top: 18px !important;
            }

            .invoice-notes-panel,
            .invoice-total-panel {
                width: 100%;
            }

            .credit-days-holder {
                justify-content: stretch !important;
            }

            #creditDaysWrapper {
                align-items: stretch !important;
                flex-direction: column;
                gap: 8px;
                width: 100%;
            }

            #creditDays {
                width: 100% !important;
            }

            .invoice-total-box {
                margin-top: 14px;
            }

            .invoice-action-btn {
                min-height: 48px;
                min-width: 0;
                width: 100%;
            }

            .customer-card {
                left: 10px !important;
                max-width: calc(100vw - 20px);
                right: auto !important;
                width: calc(100vw - 20px);
            }

            .modal-box {
                width: min(94vw, 560px);
            }

            .modal-result-wrapper,
            .result-wrapper {
                max-width: calc(100vw - 28px);
            }
        }

        @media (max-width: 480px) {
            .invoicetable {
                min-width: 860px;
            }

            .old-price-result-box {
                left: 12px !important;
                min-width: 0;
                width: calc(100vw - 24px) !important;
            }

            .invoice-total-box .input-group {
                align-items: stretch;
                flex-direction: column;
            }

            .invoice-total-box .input-group-text,
            .invoice-total-box .form-control {
                border-radius: 6px !important;
                width: 100%;
            }
        }
        
    </style>

@stop
