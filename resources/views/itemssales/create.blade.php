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

        <div class="invoice-quick-actions">
            <button type="button" class="btn btn-primary m" data-bs-toggle="modal" data-bs-target="#quickCustomerModal">
                <i class="fa-solid fa-plus"></i> Add New Customer
            </button>
            <a href="{{ route('onlyviewbillafterbill') }}" class="btn" style="background-color: #556B2F; border-color:rgb(29, 3, 3); color: #ffffff;"> <i class="fa-solid fa-eye"></i> Search Invoice</a>
        </div>
            <form action="{{ route('itemsales.store') }}" method="post">


                

               



                @csrf
                <div class="invoice-top-controls">
                    
                    <div class="invoice-control customer-control">
                        <label class="invoice-field-label" for="searchCustomerInput">Customer</label>
                        <div class="search-box">
                            <input type="text" class="search-input" placeholder="Search Customer"
                                id="searchCustomerInput" data-api="customer_search" autocomplete="off">
                            <i class="fas fa-search search-icon"> </i>
                            <div class="selected-customer-inline" id="selectedCustomerInline" style="display: none;">
                                <span>Address:</span> <strong id="selectedCustomerAddress">-</strong>
                                <span class="selected-customer-separator">Contact No:</span> <strong id="selectedCustomerPhone">-</strong>
                            </div>
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


                    



                    <div class="invoice-control type-control invoice-step-after-customer">
                        <label class="invoice-field-label" for="invoice_type">Invoice Type</label>
                        <select id="invoice_type" name="invoice_type" class="d-inline form-select select-background"  onchange="changeBackgroundColor(this)">
                            <option value="">--Choose Invoice Type--</option>
                            <option value="cash">CASH </option>
                            <option value="credit">CREDIT </option>
                           
                        </select>
                        <small class="invoice-help-text">Cash / Credit</small>
                    </div>

                    
                    <div class="invoice-control date-control invoice-step-after-customer">
                        <label class="invoice-field-label" for="salesDate">Date</label>
                        <div class="input-group mb-1">
                            <span class="input-group-text">Date:</span>
                            <input type="date" class="form-control" placeholder="" id="salesDate"
                                class="form-control foritemsaledatecss" value="{{ now()->format('Y-m-d') }}" name="date">
                        </div>
                        <small class="text-muted invoice-help-text">
                            Nepali Date: {{ \App\Support\NepaliDate::adToBsString(now()->toDateString(), 'en') }}
                        </small>
                        
                    </div>
                  
                   
                
                   

                </div>
                <input type="hidden" id="salesArrInput" name="sales_arr" value="" />
                <input type="hidden" id="finalArrInput" name="final_arr" value="" />
                <div class="invoice-table-shell invoice-work-field">
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
                <div class="invoice-row-pager invoice-work-field" id="invoiceRowPager" style="display: none;">
                    <button type="button" class="invoice-row-page-btn" id="invoiceRowsPrevBtn">
                        <i class="fa-solid fa-chevron-left"></i>
                        Previous Page
                    </button>
                    <strong id="invoiceRowsPageText">Page 1 of 1</strong>
                    <button type="button" class="invoice-row-page-btn" id="invoiceRowsNextBtn">
                        Next Page
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>

                <div class="invoice-bottom-grid invoice-step-bottom-grid">
                    <div class="invoice-notes-panel">
                        <div class="">
                            <label class="my-3 invoice-work-field"><b>Amount in words: </b><span id="totalAmountWords"
                                    style="text-transform: capitalize;">...</span></label><br>
                            <textarea autocomplete="off" placeholder="Additional notes" class="form-control invoice-work-field" id="noteInput" rows="3" cols="20"></textarea>

                            {{-- /forcreditdaystextbox --}}
                            <div class="d-flex justify-content-center credit-days-holder invoice-credit-days-field">
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
                                        min="1"
                                        step="1"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        style="width:130px; font-size:16px; font-weight:600; text-align:center;">

                                    </div>
                            </div>
                            
                       {{-- //endforcredidays --}}

                        </div>
                    </div>
                    <div class="invoice-total-panel invoice-work-field">
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
                            <div class="error-message mb-2" id="invoiceErrorBox">
                                <i class="fa-solid fa-circle-info"></i>
                                <small class="fw-bold" id="errorText">Ready to verify invoice.</small>
                            </div>
                            <button class="btn btn-primary btn-md invoice-action-btn" id="verifyBtn">
                                <i class="fa-solid fa-check"></i> Verify
                            </button>
                            <button class="btn btn-success btn-md invoice-action-btn" type="submit" id="submitBtn" style="display: none;" disabled>
                                <i class="fa-solid fa-floppy-disk"></i> Save Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-labelledby="quickCustomerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content quick-customer-modal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="quickCustomerModalLabel">
                            <i class="fa-solid fa-user-plus"></i>
                            Quick Add Customer
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="quickCustomerForm">
                        <div class="modal-body">
                            <div class="quick-customer-status" id="quickCustomerStatus" style="display:none;"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" id="quickCustomerName" placeholder="Customer name">
                                    <span class="quick-customer-error" data-error-for="name"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Address <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address" placeholder="Address">
                                    <span class="quick-customer-error" data-error-for="address"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone No <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="phoneno" placeholder="10 digit phone" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <span class="quick-customer-error" data-error-for="phoneno"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Alternate Phone</label>
                                    <input type="text" class="form-control" name="alternate_phoneno" placeholder="Alternate phone" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <span class="quick-customer-error" data-error-for="alternate_phoneno"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" placeholder="Email">
                                    <span class="quick-customer-error" data-error-for="email"></span>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Customer Type</label>
                                    <select class="form-select" name="type">
                                        <option value="">-- Select Type --</option>
                                        <option value="shop">Shop</option>
                                        <option value="customer">Customer</option>
                                    </select>
                                    <span class="quick-customer-error" data-error-for="type"></span>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" name="remarks" rows="2" placeholder="Notes"></textarea>
                                    <span class="quick-customer-error" data-error-for="remarks"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="quickCustomerSaveBtn">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Save & Select
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="invoice-submit-overlay" id="invoiceSubmitOverlay" aria-live="polite" style="display: none;">
            <div class="invoice-submit-card">
                <div class="invoice-submit-receipt">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <h2>Creating invoice</h2>
                <p>Please wait...</p>
            </div>
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
        $('.invoice-create-page form').on('submit', function () {
            $('#invoiceSubmitOverlay').css('display', 'flex');
            $('#submitBtn')
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Creating...');
            $('#verifyBtn').prop('disabled', true);
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
            } else {
                selectElement.classList.remove('cash-bg', 'credit-bg');
            }

            if (typeof updateCreditDaysVisibility === 'function') {
                updateCreditDaysVisibility();
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
            font-size: 18px;
        }
    
        .cash-bg {
            background-color: white;
        }
    
        .credit-bg {
            background-color: rgb(216, 18, 141) !important;
            color: white;
        }

        .invoice-submit-overlay {
            align-items: center;
            backdrop-filter: blur(3px);
            background: rgba(15, 23, 42, .36);
            inset: 0;
            justify-content: center;
            position: fixed;
            z-index: 9999;
        }

        .quick-customer-modal .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid #dbe4f0;
        }

        .quick-customer-modal .modal-title {
            align-items: center;
            color: #111827;
            display: inline-flex;
            font-size: 20px;
            font-weight: 900;
            gap: 8px;
        }

        .quick-customer-modal .form-label {
            color: #172033;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .quick-customer-modal .form-control,
        .quick-customer-modal .form-select {
            min-height: 44px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
        }

        .quick-customer-error {
            color: #b91c1c;
            display: block;
            font-size: 12px;
            font-weight: 900;
            margin-top: 5px;
        }

        .quick-customer-status {
            border-radius: 8px;
            font-size: 14px;
            font-weight: 900;
            margin-bottom: 12px;
            padding: 10px 12px;
        }

        .quick-customer-status.is-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }

        .quick-customer-status.is-success {
            background: #ecfdf5;
            border: 1px solid #86efac;
            color: #166534;
        }

        .invoice-submit-card {
            align-items: center;
            background: #ffffff;
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
            display: grid;
            gap: 8px;
            justify-items: center;
            max-width: 320px;
            padding: 28px 32px;
            text-align: center;
            width: calc(100% - 32px);
        }

        .invoice-submit-card h2 {
            color: #111827;
            font-size: 22px;
            font-weight: 900;
            margin: 0;
        }

        .invoice-submit-card p {
            color: #64748b;
            font-size: 15px;
            font-weight: 700;
            margin: 0;
        }

        .invoice-submit-receipt {
            animation: invoiceReceiptFloat 1.15s ease-in-out infinite;
            background: #f8fafc;
            border: 2px solid #1d4ed8;
            border-radius: 8px;
            display: grid;
            gap: 7px;
            height: 74px;
            padding: 14px 12px;
            position: relative;
            width: 64px;
        }

        .invoice-submit-receipt::after {
            background:
                linear-gradient(135deg, transparent 7px, #ffffff 0) left,
                linear-gradient(225deg, transparent 7px, #ffffff 0) right;
            background-repeat: no-repeat;
            background-size: 50% 100%;
            bottom: -2px;
            content: "";
            height: 10px;
            left: 0;
            position: absolute;
            right: 0;
        }

        .invoice-submit-receipt span {
            animation: invoiceLinePulse 1.15s ease-in-out infinite;
            background: #2563eb;
            border-radius: 999px;
            height: 6px;
        }

        .invoice-submit-receipt span:nth-child(2) {
            animation-delay: .15s;
            width: 78%;
        }

        .invoice-submit-receipt span:nth-child(3) {
            animation-delay: .3s;
            width: 55%;
        }

        @keyframes invoiceReceiptFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes invoiceLinePulse {
            0%, 100% {
                opacity: .35;
                transform: scaleX(.72);
                transform-origin: left;
            }
            50% {
                opacity: 1;
                transform: scaleX(1);
            }
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

        .old-price-result-item-other {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding-left: 12px;
        }

        .old-price-result-item-other:hover {
            background: #e0f2fe;
        }

        .old-price-item-line {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .old-price-price-line {
            color: #0f172a;
            font-size: 17px;
            font-weight: 900;
        }

        .old-price-smart-badge {
            background: #0369a1;
            border-radius: 999px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            line-height: 1;
            padding: 4px 7px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .old-price-meta-line {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .old-price-customer-pill {
            background: #e0f2fe;
            border: 1px solid #7dd3fc;
            border-radius: 999px;
            color: #075985;
            display: inline-flex;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.1;
            padding: 3px 7px;
        }

        .old-price-empty {
            color: #dc3545;
            font-size: 14px;
            padding: 8px 10px;
        }

        .invoice-action-btn {
            align-items: center;
            display: inline-flex;
            font-size: 18px;
            font-weight: 900;
            gap: 8px;
            justify-content: center;
            min-height: 54px;
            width: 100%;
        }

        .invoice-step-after-customer,
        .invoice-step-bottom-grid,
        .invoice-work-field {
            display: none;
        }

        .invoice-create-page {
            padding-bottom: 24px;
            width: 100%;
            max-width: none;
            margin: 0;
        }

        .main-content {
            flex: 1 1 auto;
            min-width: 0;
            width: 100%;
            padding-right: 24px;
        }

        .invoice-quick-actions {
            display: flex;
            gap: 14px;
            justify-content: flex-end;
            margin: -86px 0 24px;
        }

        .invoice-quick-actions .btn {
            align-items: center;
            display: inline-flex;
            gap: 7px;
            min-height: 42px;
            white-space: nowrap;
        }

        .invoice-top-controls {
            align-items: start;
            display: grid;
            gap: 14px;
            grid-template-columns: minmax(340px, 1.7fr) minmax(230px, .72fr) minmax(210px, .58fr);
            margin: 0 0 14px;
        }

        .invoice-control {
            min-width: 0;
        }

        .invoice-control .search-box,
        .invoice-control .search-input,
        .invoice-control .form-select {
            width: 100%;
        }

        .invoice-field-label {
            color: #172033;
            display: block;
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .invoice-help-text {
            color: #64748b !important;
            display: block;
            font-size: 13px !important;
            font-weight: 800;
            margin-top: 5px;
            padding: 0 !important;
        }

        .invoice-control .search-input,
        .invoice-control .form-select,
        .invoice-control .form-control {
            min-height: 44px;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 700;
        }

        .invoice-table-shell {
            background: #ffffff;
            border: 1px solid #d5deea;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
            overflow-x: auto;
            overflow-y: visible;
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
            display: table !important;
            min-width: 1360px;
            width: 100%;
            margin: 0;
            table-layout: auto;
        }

        .invoicetable th,
        .invoicetable td {
            padding: 4px 8px;
            vertical-align: middle;
        }

        .invoicetable th {
            background: #f1f5f9;
            color: #111827;
            font-size: 13px;
            font-weight: 900;
            line-height: 1.1;
            white-space: nowrap;
        }

        .invoice-create-page .invoicetable th:nth-child(1),
        .invoice-create-page .invoicetable td:nth-child(1) {
            width: 42px;
            text-align: center;
        }

        .invoice-create-page .invoicetable th:nth-child(2),
        .invoice-create-page .invoicetable td:nth-child(2) {
            width: 56px;
            text-align: center;
        }

        .invoice-create-page .invoicetable th:nth-child(3),
        .invoice-create-page .invoicetable td:nth-child(3) {
            width: 95px;
        }

        .invoice-create-page .invoicetable th:nth-child(4),
        .invoice-create-page .invoicetable td:nth-child(4) {
            width: 28%;
        }

        .invoice-create-page .invoicetable th:nth-child(5),
        .invoice-create-page .invoicetable td:nth-child(5) {
            min-width: 170px;
            width: 170px;
        }

        .invoice-create-page .invoicetable th:nth-child(6),
        .invoice-create-page .invoicetable td:nth-child(6) {
            min-width: 130px;
            width: 130px;
        }

        .invoice-create-page .invoicetable th:nth-child(7),
        .invoice-create-page .invoicetable td:nth-child(7) {
            min-width: 260px;
            width: 260px;
        }

        .invoice-create-page .invoicetable th:nth-child(8),
        .invoice-create-page .invoicetable td:nth-child(8) {
            min-width: 210px;
            width: 210px;
        }

        .invoicetable .form-control,
        .invoicetable .form-select {
            min-height: 34px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            padding-bottom: 4px;
            padding-top: 4px;
        }

        .invoicetable .btn {
            min-height: 32px;
            min-width: 32px;
            padding: 4px 8px;
        }

        .invoicetable .input-group {
            flex-wrap: nowrap;
            min-width: 0;
        }

        .invoicetable .input-group-text {
            flex: 0 0 auto;
            padding-bottom: 4px;
            padding-left: 8px;
            padding-right: 8px;
            padding-top: 4px;
            font-weight: 800;
        }

        .invoicetable #priceInput,
        .invoicetable #subTotalInput {
            min-width: 120px;
            padding-left: 8px;
            padding-right: 8px;
            text-align: right;
        }

        .unstocked-cell,
        .price-cell,
        .subtotal-cell {
            position: relative;
        }

        .unstocked-cell #unstockedInput {
            font-weight: 500;
            line-height: 1;
        }

        .price-cell #priceInput,
        .subtotal-cell #subTotalInput {
            font-weight: 700;
            line-height: 1;
        }

        .price-cell #priceInput,
        .subtotal-cell #subTotalInput {
            font-size: clamp(13px, 1vw, 16px);
            letter-spacing: 0;
        }

        .quantity-cell {
            display: grid;
            gap: 3px;
        }

        .quantity-cell #quantityInput {
            min-width: 140px;
        }

        .invoice-row-pager {
            align-items: center;
            background: #ffffff;
            border: 1px solid #d5deea;
            border-radius: 8px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .06);
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 10px;
            padding: 10px 12px;
        }

        .invoice-row-pager strong {
            color: #111827;
            font-size: 16px;
            font-weight: 900;
            min-width: 130px;
            text-align: center;
        }

        .invoice-row-page-btn {
            align-items: center;
            background: #1f2937;
            border: 0;
            border-radius: 6px;
            color: #ffffff;
            display: inline-flex;
            font-size: 15px;
            font-weight: 900;
            gap: 8px;
            min-height: 40px;
            padding: 0 14px;
        }

        .invoice-row-page-btn:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            opacity: .75;
        }

        .select-product-link h6 {
            display: -webkit-box;
            font-size: 11px !important;
            line-height: 1.05;
            max-height: 24px;
            overflow: hidden;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .select-product-link p {
            font-size: 11px !important;
            line-height: 1;
            margin-top: 0 !important;
        }

        .invoice-total-box {
            background: #ffffff;
            border: 1px solid #d5deea;
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .1);
            padding: 14px;
            position: sticky;
            top: 14px;
        }

        .invoice-total-box .input-group-text {
            font-weight: 800;
        }

        .invoice-total-box .form-control {
            min-height: 46px;
            font-size: 18px;
            font-weight: 800;
            text-align: right;
        }

        .invoice-bottom-grid {
            align-items: start;
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(0, 1fr) minmax(340px, 390px);
            margin: 22px 0 24px;
        }

        .invoice-notes-panel textarea {
            min-height: 96px;
            font-size: 16px;
        }

        .error-message {
            align-items: flex-start;
            background: #f8fafc;
            border: 1px solid #d5deea;
            border-radius: 8px;
            color: #64748b;
            display: flex;
            gap: 8px;
            min-height: 42px;
            padding: 10px 11px;
        }

        .error-message.has-error {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #b91c1c;
        }

        .error-message.has-success {
            background: #ecfdf5;
            border-color: #86efac;
            color: #166534;
        }

        .invoice-field-invalid,
        .invoice-field-invalid:focus {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, .12) !important;
        }

        .field-error-text {
            color: #b91c1c;
            display: block;
            font-size: 12px;
            font-weight: 900;
            line-height: 1.2;
            margin-top: 5px;
        }

        #creditDaysWrapper {
            max-width: 100%;
        }

        @media (max-width: 1200px) {
            .invoice-quick-actions {
                display: flex;
                justify-content: flex-end;
                gap: 14px;
                margin: -72px 0 22px;
                padding-right: 0;
            }

            .invoice-top-controls {
                grid-template-columns: 1fr 1fr;
            }

            .customer-control {
                grid-column: 1 / -1;
            }

            .invoice-bottom-grid {
                grid-template-columns: minmax(0, 1fr) minmax(300px, 340px);
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
                margin: 0 0 12px;
            }

            .invoice-quick-actions .btn {
                justify-content: center;
                margin-left: 0 !important;
                width: 100%;
            }

            .invoice-top-controls {
                grid-template-columns: 1fr;
            }

            .select-background {
                font-size: 18px;
                min-height: 48px;
            }

            .invoice-control small {
                display: block;
                padding: 6px 0 0 !important;
            }

            .invoice-table-shell {
                overflow-x: auto;
                overflow-y: visible;
                -webkit-overflow-scrolling: touch;
            }

            .invoice-table-shell::after {
                display: block;
            }

            .invoicetable {
                min-width: 1220px;
            }

            .invoicetable th,
            .invoicetable td {
                padding: 5px 7px;
            }

            .invoicetable th {
                background: #f1f5f9;
                color: #111827;
                font-size: 14px;
            }

            .invoicetable .form-control,
            .invoicetable .form-select {
                min-height: 36px;
                font-size: 14px;
            }

            .invoicetable .btn {
                min-height: 34px;
                min-width: 34px;
            }

            .invoice-bottom-grid {
                grid-template-columns: 1fr;
                margin-top: 18px !important;
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
                position: static;
            }

            .invoice-total-box .form-control {
                min-height: 44px;
                font-size: 17px;
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
                min-width: 1160px;
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
