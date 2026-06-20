@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

    <div class="main-content cn-create-page">
        
        <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
            <div class="card-body">
                <h5 class="card-title">Customer Information</h5>
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
      
        <div class="container-fluid">
            <div class="cn-page-banner">
                <div>
                    <div class="cn-kicker">
                        <i class="fa-solid fa-rotate-left"></i>
                        Sales Return Mode
                    </div>
                    <h2>Credit Note / Sales Return</h2>
                    <p>This page creates return credit only, not a normal sales invoice.</p>
                </div>
                <div class="cn-number-box">
                    <span>Credit Note No</span>
                    <strong>{{ $nextgenid }}</strong>
                </div>
            </div>

            <div class="cn-action-row">
                <a href="{{ route('customerinfos.create') }}" class="btn cn-secondary-btn">
                    <i class="fa-solid fa-plus"></i> Add New Customer
                </a>
                <a href="{{ route('creditnotescustomeronlyview.billno') }}" class="btn cn-dark-btn">
                    <i class="fa-solid fa-eye"></i> Search Credit Note
                </a>
            </div>
            <form action="{{ route('creditnotes.store') }}" method="post">
                @csrf
                <div class="cn-form-shell">
                <div class="py-4 d-flex justify-content-between align-items-start cn-entry-bar">
                    {{-- <a href="{{ route('customerinfos.create') }}" class="btn btn-primary"> <i class="fa-solid fa-plus"></i> Add New Customer</a> --}}
                    <div class="cn-customer-picker">
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

                    <div class="selected-customer-inline cn-selected-customer" id="selectedCustomerInline" style="display: none;">
                        <div>
                            <span>Address</span>
                            <b id="selectedCustomerAddress">-</b>
                        </div>
                        <div>
                            <span>Contact</span>
                            <b id="selectedCustomerPhone">-</b>
                        </div>
                    </div>

                    
                            <input type="hidden" class="form-control" placeholder="type old invoice id here" id=""
                                class="form-control " value="{{ $nextgenid }}" name="creditnoteinvoiceid">
                    
                    
                    <div style="width: 400px" hidden>
                        <div class="input-group mb-1" hidden>
                            <span class="input-group-text">Invoice No for Reference:</span>
                            <input   autocomplete="off" type="text" class="form-control" placeholder="type  invoice No here" id=""
                                class="form-control " value="" name="bilinvoiceid">
                        </div>
                        
                    </div>


            
    

                    <div style="width: 300px">
                        <div class="input-group mb-1">
                            <span class="input-group-text">Date:</span>
                            <input  autocomplete="off" type="date" class="form-control" placeholder="" id="salesDate"
                                class="form-control foritemsaledatecss" value="{{ now()->format('Y-m-d') }}" name="date">
                        </div>
                        
                    </div>

                    
                   

                </div>
                <input type="hidden" id="salesArrInput" name="sales_arr" value="" />
                <input type="hidden" id="finalArrInput" name="final_arr" value="" />
                <table class="invoicetable table-responsive bg-white cn-return-table">
                    <tbody id="invoiceTableBody" style="max-height: none;">
                        <tr>  <th>S.N</th>
                            <th>
                                <a class=" btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></a>
                            </th>
                            <th>Items</th>
                            <th>Unstocked Name</th>
                            <th>Quantity</th>
                            <th>Unit (pcs/kg)</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </tbody>
                </table>

                <div class="row mt-5 mb-4 p-0 cn-summary-row">
                    <div class="col-md-9">
                        <div class="">
                            <label class="my-3"><b>Amount in words: </b><span id="totalAmountWords"
                                    style="text-transform: capitalize;">...</span></label><br>
                            <textarea  autocomplete="off" placeholder="Additional notes" class="form-control" id="noteInput" rows="3" cols="20"></textarea>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="">
                            <div class="input-group mb-1">
                                <span class="input-group-text">Sub Total (Rs.)</span>
                                <input  autocomplete="off" type="text" class="form-control" placeholder="0.00" id="subTotalInputFinal"
                                    data-name="subtotal" name="test" disabled>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Discount (Rs.)</span>
                                <input autocomplete="off" type="text" class="form-control sales-input-final" placeholder="0.00"
                                    id="discountInputFinal" data-name="discount">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Total (Rs.)</span>
                                <input  autocomplete="off" type="text" class="form-control" placeholder="0.00" id="totalInputFinal"
                                    data-name="total" disabled>
                            </div>
                            <br>
                            <div class="error-message mb-2">
                                <small class="text-danger fw-bold" id="errorText"></small>
                            </div>
                            <button class="btn cn-verify-btn btn-md" id="verifyBtn">Verify Return</button>
                            <button class="btn cn-submit-btn btn-md" type="submit" id="submitBtn" style="display: none;" disabled>Save</button>
                        </div>
                    </div>
                </div>
                </div>
            </form>
        </div>

        <div class="modal-wrapper" id="modalWrapper" style="display: none;">
            <div class="modal-container flex-css" id="modalContainer" data-close="true">
                <div class="modal-box">
                    <div class="title flex-css mb-4">
                        <h1>Select Item</h1>
                    </div>
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="Search Item" id="searchProductInput">
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

        var enableQuantityInput = {{ $enableQuantityInput ? 'true' : 'false' }};

    </script>
    
    <style>
        .cn-create-page {
            background:
                linear-gradient(180deg, rgba(255, 247, 237, .9), rgba(255, 255, 255, 0) 340px),
                #f8fafc;
        }

        .cn-page-banner {
            align-items: center;
            background: #7c2d12;
            border-left: 12px solid #f97316;
            box-shadow: 0 18px 38px rgba(124, 45, 18, .18);
            color: #fff;
            display: flex;
            justify-content: space-between;
            margin: 18px 0;
            padding: 18px 22px;
        }

        .cn-page-banner h2 {
            color: #fff;
            font-size: 32px;
            font-weight: 900;
            letter-spacing: 0;
            margin: 4px 0;
            text-transform: uppercase;
        }

        .cn-page-banner p {
            color: #ffedd5;
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .cn-kicker {
            align-items: center;
            color: #fed7aa;
            display: flex;
            font-size: 14px;
            font-weight: 900;
            gap: 8px;
            text-transform: uppercase;
        }

        .cn-number-box {
            background: #fff7ed;
            border: 3px solid #fb923c;
            color: #7c2d12;
            min-width: 190px;
            padding: 12px 16px;
            text-align: center;
        }

        .cn-number-box span {
            display: block;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .cn-number-box strong {
            display: block;
            font-size: 38px;
            line-height: 1;
        }

        .cn-action-row {
            align-items: center;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-bottom: 12px;
        }

        .cn-secondary-btn,
        .cn-dark-btn,
        .cn-verify-btn,
        .cn-submit-btn {
            border: 0;
            color: #fff !important;
            font-weight: 800;
        }

        .cn-secondary-btn {
            background: #ea580c;
        }

        .cn-secondary-btn:hover {
            background: #c2410c;
        }

        .cn-dark-btn {
            background: #3f2d16;
        }

        .cn-dark-btn:hover {
            background: #2b1d0f;
        }

        .cn-form-shell {
            background: #ffffff;
            border: 1px solid #fed7aa;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .08);
            padding: 16px;
        }

        .cn-entry-bar {
            align-items: center !important;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            gap: 14px;
            margin-bottom: 16px;
            padding: 16px !important;
        }

        .cn-customer-picker {
            flex: 0 1 430px;
            max-width: 520px;
            min-width: 360px;
        }

        .cn-create-page #searchCustomerInput {
            font-size: 18px;
            font-weight: 800;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cn-selected-customer {
            align-items: stretch;
            display: none;
            flex: 1 1 360px;
            gap: 10px;
            margin: 0;
            min-width: 320px;
            padding-left: 0;
        }

        .cn-create-page #selectedCustomerInline[style*="display: block"] {
            display: flex !important;
        }

        .cn-selected-customer div {
            background: #ffffff;
            border: 1px solid #fdba74;
            border-radius: 8px;
            display: grid;
            gap: 3px;
            min-width: 0;
            padding: 9px 12px;
        }

        .cn-selected-customer span {
            color: #9a3412;
            font-size: 11px;
            font-weight: 900;
            line-height: 1;
            text-transform: uppercase;
        }

        .cn-selected-customer b {
            color: #111827;
            display: block;
            font-size: 15px;
            font-weight: 900;
            line-height: 1.2;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .cn-create-page .search-input,
        .cn-create-page .form-control,
        .cn-create-page .form-select {
            border-color: #fdba74;
        }

        .cn-create-page .input-group-text {
            background: #ffedd5;
            border-color: #fdba74;
            color: #7c2d12;
            font-weight: 900;
        }

        .cn-return-table {
            border: 1px solid #fb923c;
            overflow: hidden;
        }

        .cn-return-table th {
            background: #f97316 !important;
            border-color: #c2410c !important;
            color: #ffffff !important;
            font-size: 16px;
            text-transform: uppercase;
        }

        .cn-return-table td {
            background: #fffaf5;
            border-color: #fed7aa !important;
        }

        .cn-create-page .old-price-wrapper {
            position: relative;
        }

        .cn-create-page .old-price-result-box {
            background: #ffffff;
            border: 1px solid #fdba74;
            border-radius: 8px;
            box-shadow: 0 16px 34px rgba(124, 45, 18, .22);
            max-height: 280px;
            min-width: 380px;
            overflow: hidden;
            padding: 6px;
            position: fixed;
            z-index: 99999;
        }

        .cn-create-page .old-price-result-box::before {
            background: linear-gradient(90deg, #f97316, #fb923c, #fed7aa);
            content: "";
            display: block;
            height: 3px;
            margin: -6px -6px 6px;
        }

        .cn-create-page .old-price-result-item {
            background: #fffaf5;
            border: 1px solid transparent;
            border-radius: 6px;
            color: #111827;
            cursor: pointer;
            display: grid;
            gap: 5px;
            margin-bottom: 5px;
            padding: 10px 12px;
            text-align: left;
            transition: background .16s ease, border-color .16s ease, transform .16s ease;
            width: 100%;
        }

        .cn-create-page .old-price-result-item:last-child {
            margin-bottom: 0;
        }

        .cn-create-page .old-price-result-item:hover,
        .cn-create-page .old-price-result-item:focus {
            background: #ffedd5;
            border-color: #fb923c;
            outline: none;
            transform: translateY(-1px);
        }

        .cn-create-page .old-price-item-line {
            align-items: center;
            color: #111827;
            display: flex;
            flex-wrap: wrap;
            font-size: 15px;
            gap: 7px;
            line-height: 1.2;
        }

        .cn-create-page .old-price-item-line b {
            font-size: 16px;
            font-weight: 900;
        }

        .cn-create-page .old-price-price-line {
            color: #7c2d12;
            font-size: 17px;
            font-weight: 900;
            line-height: 1.1;
        }

        .cn-create-page .old-price-meta-line {
            align-items: center;
            color: #64748b;
            display: flex;
            flex-wrap: wrap;
            font-size: 12px;
            font-weight: 800;
            gap: 6px;
            line-height: 1.25;
        }

        .cn-create-page .old-price-smart-badge,
        .cn-create-page .old-price-customer-pill {
            border-radius: 999px;
            display: inline-flex;
            font-size: 11px;
            font-weight: 900;
            line-height: 1;
            padding: 4px 7px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .cn-create-page .old-price-smart-badge {
            background: #7c2d12;
            color: #ffffff;
        }

        .cn-create-page .old-price-customer-pill {
            background: #e0f2fe;
            border: 1px solid #7dd3fc;
            color: #075985;
        }

        .cn-create-page .old-price-result-item-other {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
        }

        .cn-create-page .old-price-result-item-other:hover,
        .cn-create-page .old-price-result-item-other:focus {
            background: #e0f2fe;
            border-color: #38bdf8;
        }

        .cn-create-page .old-price-empty {
            background: #fff7ed;
            border-radius: 6px;
            color: #9a3412;
            font-size: 13px;
            font-weight: 900;
            padding: 10px 12px;
        }

        .cn-return-table #addRowBtn {
            background: #16a34a;
            border: 0;
            color: #fff;
            font-weight: 900;
        }

        .cn-summary-row {
            background: #fff7ed;
            border-top: 4px solid #fb923c;
            margin-left: 0;
            margin-right: 0;
            padding: 16px !important;
        }

        .cn-summary-row textarea {
            border: 1px solid #fdba74;
        }

        .cn-verify-btn {
            background: #7c2d12;
            min-width: 300px;
            padding-left: 24px;
            padding-right: 24px;
        }

        .cn-verify-btn:hover {
            background: #9a3412;
        }

        .cn-submit-btn {
            background: #15803d;
            min-width: 300px;
            padding-left: 24px;
            padding-right: 24px;
        }

        .cn-submit-btn:hover {
            background: #166534;
        }

        .select-background {
            background-color: white;
            font-size: 25px;
        }
    
        .cash-bg {
            background-color: white;
        }
    
        .credit-bg {
            background-color: red;
            color: white;
        }

        @media (max-width: 900px) {
            .cn-page-banner,
            .cn-entry-bar {
                align-items: stretch;
                flex-direction: column;
                gap: 14px;
            }

            .cn-customer-picker,
            .cn-selected-customer {
                flex-basis: auto;
                max-width: none;
                min-width: 0;
                width: 100%;
            }

            .cn-selected-customer {
                flex-direction: column;
            }

            .cn-action-row {
                align-items: stretch;
                flex-direction: column;
            }

            .cn-action-row .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .cn-create-page .old-price-result-box {
                left: 12px !important;
                min-width: 0;
                width: calc(100vw - 24px) !important;
            }
        }
    </style>

   
@stop
