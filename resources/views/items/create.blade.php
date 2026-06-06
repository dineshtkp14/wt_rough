@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content item-create-page">
    <div class="container-fluid">
        @yield('breadcrumb')

        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <b>Please check the item entry.</b>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $oldItemRows = old('items', [[
                'itemsname' => old('itemsname', ''),
                'quantity' => old('quantity', ''),
                'unit' => old('unit', ''),
                'showwarning' => old('showwarning', ''),
                'costprice' => old('costprice', ''),
                'mrp' => old('mrp', ''),
                'itemstorearea' => old('itemstorearea', ''),
                'wp' => old('wp', ''),
                'competetiveretail' => old('competetiveretail', ''),
                'competetivewholesale' => old('competetivewholesale', ''),
            ]]);
        @endphp

        <form action="{{ route('items.store') }}" method="post" id="itemBulkForm">
            @csrf

            <div class="item-panel mb-3">
                <div class="item-panel-header">
                    <div>
                        <span>Step 1</span>
                        <strong>Bill And Company</strong>
                    </div>
                    <a href="{{ route('companys.create') }}" class="item-secondary-btn">
                        <i class="fas fa-plus-circle"></i> Add New Company
                    </a>
                </div>

                <div class="item-panel-body">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror"
                                name="date" value="{{ old('date', now()->format('Y-m-d')) }}">
                            @error('date')
                                <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Bill No</label>
                            <input autocomplete="off" type="text" class="form-control @error('billno') is-invalid @enderror"
                                name="billno" value="{{ old('billno') }}" placeholder="Enter bill no">
                            @error('billno')
                                <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">My Firm Name <span>*</span></label>
                            <select class="form-select @error('firm_name') is-invalid @enderror" name="firm_name">
                                @foreach($all as $firm)
                                    <option value="{{ $firm->nick_name }}" {{ old('firm_name') == $firm->nick_name ? 'selected' : '' }}>
                                        {{ $firm->firm_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('firm_name')
                                <p class="invalid-feedback">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label">Company <span>*</span></label>
                            <div class="search-box item-company-search">
                                <input id="customerIdInput" name="companyid" value="{{ old('companyid') }}" hidden>
                                <input type="text" class="search-input @error('companyid') is-invalid @enderror"
                                    placeholder="Search Company Name" id="searchCustomerInput" data-api="company_search"
                                    autocomplete="off">
                                <i class="fas fa-search search-icon"></i>
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
                            @error('companyid')
                                <p class="invalid-feedback d-block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="item-panel">
                <div class="item-panel-header">
                    <div>
                        <span>Step 2</span>
                        <strong>Bulk Item Entry</strong>
                    </div>
                    <button type="button" class="item-icon-btn" id="addItemRowBtn" title="Add row">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle item-entry-table">
                        <colgroup>
                            <col style="width: 82px;">
                            <col style="width: 260px;">
                            <col style="width: 120px;">
                            <col style="width: 120px;">
                            <col style="width: 140px;">
                            <col style="width: 140px;">
                            <col style="width: 140px;">
                            <col style="width: 180px;">
                            <col style="width: 150px;">
                            <col style="width: 170px;">
                            <col style="width: 190px;">
                            <col style="width: 150px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Warning</th>
                                <th>Cost Rate</th>
                                <th>Sale Price</th>
                                <th>Store Area</th>
                                <th>Wholesale</th>
                                <th>Comp Retail</th>
                                <th>Comp Wholesale</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody id="itemRows"></tbody>
                    </table>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-lg-7">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control item-notes @error('notes') is-invalid @enderror" name="notes"
                        rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div>
                <div class="col-lg-5">
                    <div class="item-summary">
                        <div><span>Rows</span><b id="itemRowCount">0 / 12</b></div>
                        <div><span>Total Quantity</span><b id="itemTotalQuantity">0.00</b></div>
                        <div><span>Total Cost</span><b id="itemTotalCost">0.00</b></div>
                    </div>
                </div>
            </div>

            <div class="item-savebar">
                <a href="{{ route('items.index') }}" class="item-secondary-btn">
                    <i class="fa-solid fa-list"></i> View Items
                </a>
                <button type="submit" id="submitBtn" class="item-save-btn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Items
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var maxRows = 12;
        var tbody = document.getElementById('itemRows');
        var addBtn = document.getElementById('addItemRowBtn');
        var rowCount = document.getElementById('itemRowCount');
        var totalQuantity = document.getElementById('itemTotalQuantity');
        var totalCost = document.getElementById('itemTotalCost');
        var oldItems = @json($oldItemRows);

        function money(value) {
            return Number(value || 0).toFixed(2);
        }

        function renumberRows() {
            tbody.querySelectorAll('tr').forEach(function (row, index) {
                row.querySelector('.item-row-number').textContent = index + 1;
                row.querySelectorAll('[data-field]').forEach(function (input) {
                    input.name = 'items[' + index + '][' + input.getAttribute('data-field') + ']';
                });
            });

            var count = tbody.querySelectorAll('tr').length;
            rowCount.textContent = count + ' / ' + maxRows;
            addBtn.disabled = count >= maxRows;
        }

        function calculateSummary() {
            var qtyTotal = 0;
            var costTotal = 0;

            tbody.querySelectorAll('tr').forEach(function (row) {
                var qty = parseFloat(row.querySelector('[data-field="quantity"]').value) || 0;
                var cost = parseFloat(row.querySelector('[data-field="costprice"]').value) || 0;
                var total = qty * cost;
                row.querySelector('.item-total-display').value = money(total);
                qtyTotal += qty;
                costTotal += total;
            });

            totalQuantity.textContent = money(qtyTotal);
            totalCost.textContent = money(costTotal);
        }

        function addRow(values) {
            if (tbody.querySelectorAll('tr').length >= maxRows) return;

            var row = document.createElement('tr');
            row.innerHTML = [
                '<td><span class="item-row-number"></span><button type="button" class="item-remove-btn" title="Remove row"><i class="fa-solid fa-trash"></i></button></td>',
                '<td><input type="text" class="form-control" data-field="itemsname" required autocomplete="off"></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control item-calc" data-field="quantity" required></td>',
                '<td><select class="form-select" data-field="unit" required><option value="">Unit</option><option value="pcs">pcs</option><option value="kg">kg</option><option value="feet">feet</option><option value="Pc">Pc</option><option value="Pcs">Pcs</option></select></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="showwarning" required></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control item-calc" data-field="costprice" required></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="mrp" required></td>',
                '<td><input type="text" class="form-control" data-field="itemstorearea" required autocomplete="off"></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="wp"></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="competetiveretail"></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="competetivewholesale"></td>',
                '<td><input type="text" class="form-control item-total-display" readonly></td>'
            ].join('');

            tbody.appendChild(row);

            Object.keys(values || {}).forEach(function (field) {
                var input = row.querySelector('[data-field="' + field + '"]');
                if (input) input.value = values[field] || '';
            });

            renumberRows();
            calculateSummary();
        }

        addBtn.addEventListener('click', function () {
            addRow({});
        });

        tbody.addEventListener('input', function (event) {
            if (event.target.classList.contains('item-calc')) {
                calculateSummary();
            }
        });

        tbody.addEventListener('click', function (event) {
            var removeBtn = event.target.closest('.item-remove-btn');
            if (!removeBtn) return;
            if (tbody.querySelectorAll('tr').length === 1) return;
            removeBtn.closest('tr').remove();
            renumberRows();
            calculateSummary();
        });

        document.getElementById('itemBulkForm').addEventListener('submit', function () {
            document.getElementById('submitBtn').disabled = true;
        });

        oldItems.slice(0, maxRows).forEach(function (item) {
            addRow(item || {});
        });

        if (!tbody.querySelectorAll('tr').length) {
            addRow({});
        }
    })();
</script>

<style>
    .item-create-page {
        box-sizing: border-box;
        flex: 1 1 auto;
        width: 100%;
    }

    .item-create-page .container-fluid {
        max-width: 1680px;
        width: 100%;
    }

    .item-panel {
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
        overflow: visible;
    }

    .item-panel-header {
        align-items: center;
        background: #f8fafc;
        border-bottom: 1px solid #dbe3ef;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: 10px 14px;
    }

    .item-panel-header span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .item-panel-header strong {
        color: #172033;
        display: block;
        font-size: 18px;
        font-weight: 900;
    }

    .item-panel-body {
        padding: 12px 14px;
    }

    .item-create-page .form-label {
        color: #334155;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .item-create-page .form-label span {
        color: #dc2626;
    }

    .item-create-page .form-control,
    .item-create-page .form-select,
    .item-create-page .search-input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        min-height: 38px;
    }

    .item-company-search {
        position: relative;
    }

    .item-company-search .search-input {
        padding-left: 40px;
        width: 100%;
    }

    .item-company-search .search-icon {
        left: 14px;
        position: absolute;
        top: 12px;
        z-index: 2;
    }

    .item-company-search .result-wrapper {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
        left: 0;
        position: absolute;
        right: 0;
        top: calc(100% + 4px);
        z-index: 10000;
    }

    .item-entry-table {
        margin: 0;
        min-width: 1720px;
        table-layout: fixed;
        width: 100%;
    }

    .item-entry-table thead {
        display: table-header-group !important;
    }

    .item-entry-table tbody {
        display: table-row-group !important;
    }

    .item-entry-table tr {
        display: table-row !important;
    }

    .item-entry-table th,
    .item-entry-table td {
        display: table-cell !important;
        padding: 4px 6px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .item-entry-table .form-control,
    .item-entry-table .form-select {
        min-height: 34px;
        padding: 4px 8px;
        width: 100%;
    }

    .item-row-number {
        display: inline-block;
        font-weight: 900;
        min-width: 20px;
    }

    .item-remove-btn {
        align-items: center;
        background: #ffffff;
        border: 1px solid #ef4444;
        border-radius: 8px;
        color: #dc2626;
        display: inline-flex;
        height: 32px;
        justify-content: center;
        margin-left: 4px;
        width: 32px;
    }

    .item-icon-btn,
    .item-save-btn,
    .item-secondary-btn {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        justify-content: center;
        text-decoration: none !important;
    }

    .item-icon-btn,
    .item-save-btn {
        background: #0f766e;
        border: 0;
        color: #ffffff !important;
    }

    .item-icon-btn {
        height: 42px;
        width: 44px;
    }

    .item-icon-btn:disabled {
        background: #cbd5e1;
        color: #64748b !important;
    }

    .item-secondary-btn {
        background: #ffffff;
        border: 1px solid #94a3b8;
        color: #334155 !important;
        min-height: 42px;
        padding: 0 14px;
    }

    .item-save-btn {
        min-height: 44px;
        padding: 0 18px;
    }

    .item-notes {
        min-height: 112px;
        resize: vertical;
    }

    .item-summary {
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        overflow: hidden;
    }

    .item-summary div {
        align-items: center;
        border-bottom: 1px solid #dbe3ef;
        display: grid;
        grid-template-columns: 170px 1fr;
        min-height: 42px;
    }

    .item-summary div:last-child {
        border-bottom: 0;
    }

    .item-summary span {
        background: #e9ecef;
        border-right: 1px solid #cbd5e1;
        height: 100%;
        padding: 10px 12px;
    }

    .item-summary b {
        padding: 10px 12px;
    }

    .item-savebar {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        bottom: 0;
        box-shadow: 0 -8px 24px rgba(15, 23, 42, .08);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 14px;
        padding: 10px;
        position: sticky;
        z-index: 20;
    }

    @media (max-width: 900px) {
        .item-panel-header,
        .item-savebar {
            align-items: stretch;
            flex-direction: column;
        }

        .item-icon-btn,
        .item-save-btn,
        .item-secondary-btn {
            width: 100%;
        }

        .item-summary div {
            grid-template-columns: 140px 1fr;
        }
    }
</style>
@stop
