@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container-fluid invoice-create-page">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h3 class="m-0">Edit Full Invoice #{{ $invoice->id }}</h3>
            <a href="{{ route('onlyviewbillafterbill', ['invoiceid' => $invoice->id]) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Back To Invoice
            </a>
        </div>

        @if (Session::has('error'))
            <div class="alert alert-danger text-white bg-danger">{{ Session::get('error') }}</div>
        @endif

        <form action="{{ route('invoice.update', $invoice->id) }}" method="post">
            @csrf
            @method('put')

            <div class="invoice-top-controls pt-0 pb-4 d-flex justify-content-between align-items-start flex-wrap gap-3" style="margin-top: 2px;">
                <div class="invoice-control customer-control" style="width: min(620px, 100%)">
                    <div class="search-box">
                        <input id="customerIdInput" name="customerid" hidden>
                        <input type="text" class="search-input" placeholder="Search Customer"
                            id="searchCustomerInput" data-api="customer_search" autocomplete="off">
                        <i class="fas fa-search search-icon"></i>
                        <div class="selected-customer-inline" id="selectedCustomerInline" style="display: none;">
                            <span>Address:</span> <strong id="selectedCustomerAddress">-</strong>
                            <span class="selected-customer-separator">Contact No:</span> <strong id="selectedCustomerPhone">-</strong>
                        </div>
                        <div class="result-wrapper" id="customerResultWrapper" style="display: none;">
                            <div class="result-box d-flex justify-content-start align-items-center" id="customerLoadingResultBox">
                                <i class="fas fa-spinner" id="spinnerIcon"></i>
                                <h1 class="m-0 px-2">Loading</h1>
                            </div>
                            <div class="result-box d-flex justify-content-start align-items-center d-none" id="customerNotFoundResultBox">
                                <i class="fas fa-triangle-exclamation"></i>
                                <h1 class="m-0 px-2">Record Not Found</h1>
                            </div>
                            <div id="customerResultList"></div>
                        </div>
                    </div>
                </div>

                <div class="invoice-control type-control" style="width: 350px">
                    <select id="invoice_type" name="invoice_type" class="d-inline form-select select-background" onchange="changeBackgroundColor(this)">
                        <option value="">--Choose Invoice Type--</option>
                        <option value="cash">CASH</option>
                        <option value="credit">CREDIT</option>
                    </select>
                    <small style="font-size: 14px; padding:20px; color:#02090f;">Choose mode of invoice &nbsp; (cash / Credit)</small>
                </div>

                <div class="invoice-control date-control" style="width: 250px;">
                    <div class="input-group mb-1">
                        <span class="input-group-text">Date:</span>
                        <input type="date" class="form-control" id="salesDate" name="date">
                    </div>
                </div>
            </div>

            <input type="hidden" id="salesArrInput" name="sales_arr" value="">
            <input type="hidden" id="finalArrInput" name="final_arr" value="">

            <div class="invoice-table-shell">
                <table class="invoicetable table-responsive bg-white">
                    <tbody id="invoiceTableBody" style="max-height: none;">
                        <tr>
                            <th>#</th>
                            <th><a class="btn btn-success" id="addRowBtn"><i class="fa-solid fa-plus"></i></a></th>
                            <th>Item</th>
                            <th class="unstockedth">Unstocked Item</th>
                            <th>Quantity</th>
                            <th>Unit (pcs/kg)</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="invoice-bottom-grid row mt-5 mb-4 p-0">
                <div class="col-md-9 invoice-notes-panel">
                    <label class="my-3"><b>Amount in words: </b><span id="totalAmountWords" style="text-transform: capitalize;">...</span></label><br>
                    <textarea autocomplete="off" placeholder="Additional notes" class="form-control" id="noteInput" rows="3" cols="20"></textarea>
                    <div class="d-flex justify-content-center credit-days-holder">
                        <div class="d-inline-flex align-items-center mt-3 px-3 py-2 border rounded shadow-sm" id="creditDaysWrapper" style="display:none; background:#f8f9fa;">
                            <span class="fw-semibold me-2" style="font-size:16px;">Credit Days</span>
                            <input type="number" class="form-control" id="creditDays" name="credit_days" placeholder="Enter days" min="1" step="1"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                style="width:130px; font-size:16px; font-weight:600; text-align:center;">
                        </div>
                    </div>
                </div>
                <div class="col-md-3 invoice-total-panel">
                    <div class="invoice-total-box">
                        <div class="input-group mb-1">
                            <span class="input-group-text">Sub Total (Rs.)</span>
                            <input autocomplete="off" type="text" class="form-control" placeholder="0.00" id="subTotalInputFinal" data-name="subtotal" disabled>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">Discount (Rs.)</span>
                            <input autocomplete="off" type="text" class="form-control sales-input-final" placeholder="0.00" id="discountInputFinal" data-name="discount">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">Total (Rs.)</span>
                            <input autocomplete="off" type="text" class="form-control" placeholder="0.00" id="totalInputFinal" data-name="total" disabled>
                        </div>
                        <br>
                        <div class="error-message mb-2" id="invoiceErrorBox">
                            <small class="fw-bold" id="errorText">Ready to verify invoice.</small>
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
                    <i class="fas fa-search search-icon modal-search-icon"></i>
                    <div class="result-wrapper modal-result-wrapper" id="productResultWrapper" style="display: none;">
                        <div class="result-box d-flex justify-content-start align-items-center" id="productLoadingResultBox">
                            <i class="fas fa-spinner" id="spinnerIcon"></i>
                            <h1 class="m-0 px-2">Loading</h1>
                        </div>
                        <div class="result-box d-flex justify-content-start align-items-center d-none" id="productNotFoundResultBox">
                            <i class="fas fa-triangle-exclamation"></i>
                            <h1 class="m-0 px-2">Record Not Found</h1>
                        </div>
                        <div id="productResultList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function changeBackgroundColor(selectElement) {
        if (!selectElement) return;

        if (selectElement.value === 'cash') {
            selectElement.classList.remove('credit-bg');
            selectElement.classList.add('cash-bg');
        } else if (selectElement.value === 'credit') {
            selectElement.classList.remove('cash-bg');
            selectElement.classList.add('credit-bg');
        } else {
            selectElement.classList.remove('cash-bg', 'credit-bg');
        }

        if (typeof updateCreditDaysVisibility === 'function') {
            updateCreditDaysVisibility();
        }
    }

    window.INVOICE_EDIT_DATA = @json($editData);
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

    .invoice-action-btn {
        min-width: 300px;
        padding-left: 24px;
        padding-right: 24px;
    }

    .invoice-top-controls {
        gap: 18px;
    }

    .invoice-control .search-box,
    .invoice-control .search-input,
    .invoice-control .form-select {
        width: 100%;
    }

    .invoice-table-shell {
        overflow: visible;
    }

    .invoicetable {
        width: 100%;
    }

    .invoicetable td {
        overflow: visible !important;
        vertical-align: middle;
    }

    .invoicetable th {
        font-weight: 900;
        white-space: nowrap;
        vertical-align: middle;
    }

    .invoice-total-box .input-group-text,
    .invoice-total-box .form-control {
        font-weight: 800;
    }

    .invoice-total-box .form-control {
        text-align: right;
    }
</style>
@stop
