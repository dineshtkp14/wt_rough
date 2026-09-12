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
                    <div class="item-header-actions">
                        <input type="file" id="billDocumentInput" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" hidden>
                        <button type="button" class="item-scan-btn" id="scanBillBtn">
                            <i class="fa-solid fa-file-arrow-up"></i> Scan / Upload Bill
                        </button>
                        <a href="{{ route('companys.create') }}" class="item-secondary-btn">
                            <i class="fas fa-plus-circle"></i> Add New Company
                        </a>
                    </div>
                </div>

                <div class="item-panel-body">
                    <div class="item-scan-status" id="billScanStatus" role="status" aria-live="polite" hidden></div>
                    <p class="item-scan-help">
                        <i class="fa-solid fa-circle-info"></i>
                        PDF, JPEG or PNG up to 10 MB. OCR runs in this browser, the bill is not uploaded or stored, and all filled values should be reviewed.
                    </p>
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

                <div class="item-rows" id="itemRows"></div>
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
                <button type="submit" id="saveItemsBtn" class="item-save-btn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Items
                </button>
                <a href="{{ route('items.index') }}" class="item-secondary-btn">
                    <i class="fa-solid fa-list"></i> View Items
                </a>
            </div>
        </form>
    </div>
</div>

@vite('resources/js/bill-ocr.js')
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
            tbody.querySelectorAll('.item-entry-card').forEach(function (row, index) {
                row.querySelector('.item-row-number').textContent = index + 1;
                row.querySelectorAll('[data-field]').forEach(function (input) {
                    input.name = 'items[' + index + '][' + input.getAttribute('data-field') + ']';
                });
            });

            var count = tbody.querySelectorAll('.item-entry-card').length;
            rowCount.textContent = count + ' / ' + maxRows;
            addBtn.disabled = count >= maxRows;
        }

        function calculateSummary() {
            var qtyTotal = 0;
            var costTotal = 0;

            tbody.querySelectorAll('.item-entry-card').forEach(function (row) {
                var qty = parseFloat(row.querySelector('[data-field="quantity"]').value) || 0;
                var cost = parseFloat(row.querySelector('[data-field="costprice"]').value) || 0;
                var total = qty * cost;
                row.querySelector('.item-total-display').value = money(total);
                row.querySelector('.item-total-text').textContent = money(total);
                qtyTotal += qty;
                costTotal += total;
            });

            totalQuantity.textContent = money(qtyTotal);
            totalCost.textContent = money(costTotal);
        }

        function addRow(values) {
            if (tbody.querySelectorAll('.item-entry-card').length >= maxRows) return;
            values = Object.assign({ showwarning: 0 }, values || {});

            var row = document.createElement('article');
            row.className = 'item-entry-card';
            row.innerHTML = [
                '<header class="item-card-header">',
                    '<div class="item-card-title"><span class="item-row-badge">Item <b class="item-row-number"></b></span><span class="item-card-name-preview">New item</span></div>',
                    '<div class="item-card-total"><small>Total cost</small><strong class="item-total-text">0.00</strong></div>',
                    '<button type="button" class="item-remove-btn" title="Remove this item" aria-label="Remove this item"><i class="fa-solid fa-trash"></i></button>',
                '</header>',
                '<div class="item-primary-grid">',
                    '<label class="item-field item-name-field"><span>Item Name *</span><input type="text" class="form-control" data-field="itemsname" required autocomplete="off" placeholder="Enter full item name"></label>',
                    '<label class="item-field"><span>Quantity *</span><input type="number" step="0.01" min="0" class="form-control item-calc" data-field="quantity" required placeholder="0"></label>',
                    '<label class="item-field"><span>Unit *</span><select class="form-select" data-field="unit" required><option value="">Select</option><option value="pcs">pcs</option><option value="kg">kg</option><option value="g">g</option><option value="litre">litre</option><option value="ml">ml</option><option value="box">box</option><option value="packet">packet</option><option value="dozen">dozen</option><option value="metre">metre</option><option value="feet">feet</option></select></label>',
                    '<label class="item-field"><span>Cost Rate *</span><input type="number" step="0.01" min="0" class="form-control item-calc" data-field="costprice" required placeholder="0.00"></label>',
                    '<label class="item-field"><span>Sale Price *</span><input type="number" step="0.01" min="0" class="form-control" data-field="mrp" required placeholder="0.00"></label>',
                    '<label class="item-field item-store-field"><span>Store Area *</span><input type="text" class="form-control" data-field="itemstorearea" required autocomplete="off" placeholder="Shelf / area"></label>',
                '</div>',
                '<details class="item-more-fields">',
                    '<summary><i class="fa-solid fa-sliders"></i> More pricing and stock options</summary>',
                    '<div class="item-secondary-grid">',
                        '<label class="item-field"><span>Low Stock Warning *</span><input type="number" step="0.01" min="0" class="form-control" data-field="showwarning" required placeholder="0"></label>',
                        '<label class="item-field"><span>Wholesale Price</span><input type="number" step="0.01" min="0" class="form-control" data-field="wp" placeholder="0.00"></label>',
                        '<label class="item-field"><span>Competitor Retail</span><input type="number" step="0.01" min="0" class="form-control" data-field="competetiveretail" placeholder="0.00"></label>',
                        '<label class="item-field"><span>Competitor Wholesale</span><input type="number" step="0.01" min="0" class="form-control" data-field="competetivewholesale" placeholder="0.00"></label>',
                    '</div>',
                '</details>',
                '<input type="hidden" class="item-total-display">'
            ].join('');

            tbody.appendChild(row);

            Object.keys(values || {}).forEach(function (field) {
                var input = row.querySelector('[data-field="' + field + '"]');
                if (input) input.value = values[field] !== null && values[field] !== undefined ? values[field] : '';
            });

            var nameInput = row.querySelector('[data-field="itemsname"]');
            var namePreview = row.querySelector('.item-card-name-preview');
            function updateNamePreview() {
                namePreview.textContent = nameInput.value.trim() || 'New item';
            }
            nameInput.addEventListener('input', updateNamePreview);
            updateNamePreview();

            renumberRows();
            calculateSummary();
        }

        addBtn.addEventListener('click', function () {
            addRow({});
        });

        tbody.addEventListener('input', function (event) {
            event.target.classList.remove('scan-needs-review');
            if (event.target.classList.contains('item-calc')) {
                calculateSummary();
            }
        });

        tbody.addEventListener('change', function (event) {
            event.target.classList.remove('scan-needs-review');
        });

        tbody.addEventListener('click', function (event) {
            var removeBtn = event.target.closest('.item-remove-btn');
            if (!removeBtn) return;
            if (tbody.querySelectorAll('.item-entry-card').length === 1) return;
            removeBtn.closest('.item-entry-card').remove();
            renumberRows();
            calculateSummary();
        });

        document.getElementById('itemBulkForm').addEventListener('submit', function () {
            document.getElementById('saveItemsBtn').disabled = true;
        });

        var scanButton = document.getElementById('scanBillBtn');
        var billInput = document.getElementById('billDocumentInput');
        var scanStatus = document.getElementById('billScanStatus');

        function setScanStatus(message, type) {
            scanStatus.hidden = false;
            scanStatus.className = 'item-scan-status is-' + type;
            scanStatus.textContent = message;
        }

        function normalizeUnit(unit) {
            var value = String(unit || '').trim().toLowerCase();
            var aliases = {
                pc: 'pcs', piece: 'pcs', pieces: 'pcs', nos: 'pcs', no: 'pcs',
                kilogram: 'kg', kilograms: 'kg', gram: 'g', grams: 'g',
                liter: 'litre', liters: 'litre', litres: 'litre',
                pkt: 'packet', packets: 'packet', boxes: 'box', dz: 'dozen',
                meter: 'metre', meters: 'metre', metres: 'metre', ft: 'feet'
            };

            return aliases[value] || value;
        }

        function fillInvoice(invoice, company) {
            if (invoice.invoice_date && /^\d{4}-\d{2}-\d{2}$/.test(invoice.invoice_date)) {
                document.querySelector('[name="date"]').value = invoice.invoice_date;
            }

            if (invoice.bill_no) {
                document.querySelector('[name="billno"]').value = invoice.bill_no;
            }

            if (invoice.notes) {
                document.querySelector('[name="notes"]').value = invoice.notes;
            }

            if (company) {
                document.getElementById('customerIdInput').value = company.id;
                document.getElementById('searchCustomerInput').value = company.name;
            } else if (invoice.supplier_name) {
                document.getElementById('customerIdInput').value = '';
                document.getElementById('searchCustomerInput').value = invoice.supplier_name;
            }

            tbody.innerHTML = '';
            (invoice.items || []).slice(0, maxRows).forEach(function (item) {
                addRow({
                    itemsname: item.name,
                    quantity: item.quantity,
                    unit: normalizeUnit(item.unit),
                    showwarning: 0,
                    costprice: item.cost_rate,
                    mrp: item.sale_price,
                    itemstorearea: '',
                    wp: '',
                    competetiveretail: '',
                    competetivewholesale: ''
                });
            });

            if (!tbody.querySelector('.item-entry-card')) {
                addRow({});
            }

            var missing = 0;
            tbody.querySelectorAll('[required]').forEach(function (field) {
                if (String(field.value || '').trim() === '') {
                    field.classList.add('scan-needs-review');
                    missing++;
                }
            });

            calculateSummary();
            return missing;
        }

        async function matchCompany(supplierName) {
            if (!supplierName) return null;

            try {
                var response = await fetch('/api/company_search/' + encodeURIComponent(supplierName), {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) return null;

                var companies = await response.json();
                var normalizedSupplier = supplierName.toLowerCase().replace(/[^a-z0-9]/g, '');
                return companies.find(function (company) {
                    return String(company.name || '').toLowerCase().replace(/[^a-z0-9]/g, '') === normalizedSupplier;
                }) || null;
            } catch (error) {
                return null;
            }
        }

        scanButton.addEventListener('click', function () {
            billInput.click();
        });

        billInput.addEventListener('change', async function () {
            var file = billInput.files[0];
            if (!file) return;

            if (file.size > 10 * 1024 * 1024) {
                setScanStatus('The selected bill is larger than 10 MB.', 'error');
                billInput.value = '';
                return;
            }

            var originalButtonHtml = scanButton.innerHTML;
            scanButton.disabled = true;
            scanButton.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Reading bill...';
            setScanStatus('Preparing local OCR for ' + file.name + '...', 'loading');

            try {
                if (!window.BillOCR) {
                    throw new Error('The local OCR module did not load. Refresh the page and try again.');
                }

                var invoice = await window.BillOCR.extract(file, function (progress) {
                    setScanStatus('Reading bill locally... ' + progress + '%', 'loading');
                });
                var company = await matchCompany(invoice.supplier_name);
                var missing = fillInvoice(invoice, company);
                var confidence = Math.round(Number(invoice.confidence || 0) * 100);
                var message = 'Bill details filled (' + confidence + '% scan confidence).';

                if (!company && invoice.supplier_name) {
                    message += ' Select or add the company.';
                }
                if (!invoice.items || !invoice.items.length) {
                    message += ' No item rows were clear enough; enter them manually or try a sharper image.';
                }
                if (missing) {
                    message += ' Complete the highlighted fields before saving.';
                } else {
                    message += ' Please review all values before saving.';
                }

                setScanStatus(message, missing || !company ? 'warning' : 'success');
            } catch (error) {
                setScanStatus(error.message || 'The bill could not be scanned. Please try again.', 'error');
            } finally {
                scanButton.disabled = false;
                scanButton.innerHTML = originalButtonHtml;
                billInput.value = '';
            }
        });

        oldItems.slice(0, maxRows).forEach(function (item) {
            addRow(item || {});
        });

        if (!tbody.querySelectorAll('.item-entry-card').length) {
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
        max-width: 1540px;
        padding-left: 18px;
        padding-right: 18px;
        width: 100%;
    }

    .item-panel {
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
        overflow: visible;
    }

    .item-panel-header {
        align-items: center;
        background: #f8fafc;
        border-bottom: 1px solid #dbe3ef;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: 14px 18px;
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
        font-size: 17px;
        font-weight: 900;
    }

    .item-header-actions {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .item-panel-body {
        padding: 18px;
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

    .item-rows {
        background: #eef2f7;
        display: grid;
        gap: 14px;
        padding: 16px;
    }

    .item-entry-card {
        background: #ffffff;
        border: 1px solid #d6deea;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .05);
        overflow: hidden;
        transition: border-color .16s ease, box-shadow .16s ease;
    }

    .item-entry-card:focus-within {
        border-color: #8093ee;
        box-shadow: 0 0 0 3px rgba(51, 72, 212, .09);
    }

    .item-card-header {
        align-items: center;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: grid;
        gap: 12px;
        grid-template-columns: minmax(0, 1fr) auto auto;
        padding: 10px 14px;
    }

    .item-card-title {
        align-items: center;
        display: flex;
        min-width: 0;
    }

    .item-row-badge {
        background: #3348d4;
        border-radius: 7px;
        color: #ffffff;
        flex: 0 0 auto;
        font-size: 12px;
        font-weight: 800;
        margin-right: 10px;
        padding: 5px 9px;
    }

    .item-card-name-preview {
        color: #172033;
        font-size: 16px;
        font-weight: 900;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .item-card-total {
        align-items: flex-end;
        display: flex;
        flex-direction: column;
        min-width: 90px;
    }

    .item-card-total small {
        color: #64748b;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .item-card-total strong {
        color: #0f766e;
        font-size: 16px;
    }

    .item-primary-grid,
    .item-secondary-grid {
        display: grid;
        gap: 12px;
    }

    .item-primary-grid {
        grid-template-columns: minmax(260px, 2.2fr) repeat(4, minmax(105px, .8fr)) minmax(145px, 1fr);
        padding: 14px;
    }

    .item-secondary-grid {
        grid-template-columns: repeat(4, minmax(150px, 1fr));
        padding: 12px 14px 14px;
    }

    .item-field {
        display: block;
        min-width: 0;
    }

    .item-field > span {
        color: #475569;
        display: block;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .02em;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .item-field .form-control,
    .item-field .form-select {
        font-size: 15px;
        min-height: 42px;
        width: 100%;
    }

    .item-name-field .form-control {
        font-size: 16px;
        font-weight: 800;
    }

    .item-more-fields {
        border-top: 1px solid #e2e8f0;
    }

    .item-more-fields summary {
        color: #475569;
        cursor: pointer;
        font-size: 12px;
        font-weight: 800;
        list-style-position: inside;
        padding: 10px 14px;
        user-select: none;
    }

    .item-more-fields summary:hover {
        background: #f8fafc;
        color: #3348d4;
    }

    .item-more-fields summary i {
        margin: 0 5px;
    }

    .item-remove-btn {
        align-items: center;
        background: #ffffff;
        border: 1px solid #ef4444;
        border-radius: 8px;
        color: #dc2626;
        display: inline-flex;
        height: 36px;
        justify-content: center;
        margin-left: 0;
        width: 36px;
    }

    .item-icon-btn,
    .item-save-btn,
    .item-scan-btn,
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

    .item-scan-btn {
        background: #3348d4;
        border: 0;
        color: #ffffff;
        min-height: 42px;
        padding: 0 15px;
    }

    .item-scan-btn:disabled {
        cursor: wait;
        opacity: .7;
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

    .item-scan-status {
        border: 1px solid transparent;
        border-radius: 9px;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 16px;
        padding: 11px 13px;
    }

    .item-scan-help {
        color: #64748b;
        font-size: 12px;
        margin: 0 0 14px;
    }

    .item-scan-help i {
        color: #3348d4;
        margin-right: 4px;
    }

    .item-scan-status.is-loading {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .item-scan-status.is-success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .item-scan-status.is-warning {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }

    .item-scan-status.is-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .item-entry-card .scan-needs-review {
        background: #fff7ed;
        border-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, .12);
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
        grid-template-columns: 145px 1fr;
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
        flex-wrap: wrap;
        gap: 12px;
        justify-content: flex-start;
        margin-top: 14px;
        padding: 10px;
        position: sticky;
        z-index: 20;
    }

    @media (max-width: 900px) {
        .item-create-page .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .item-panel-header,
        .item-savebar {
            align-items: stretch;
            flex-direction: column;
        }

        .item-icon-btn,
        .item-save-btn,
        .item-scan-btn,
        .item-secondary-btn {
            width: 100%;
        }

        .item-header-actions {
            align-items: stretch;
            flex-direction: column;
            width: 100%;
        }

        .item-summary div {
            grid-template-columns: 140px 1fr;
        }

        .item-primary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .item-name-field,
        .item-store-field {
            grid-column: 1 / -1;
        }

        .item-secondary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .item-panel-body {
            padding: 14px;
        }

        .item-rows {
            gap: 10px;
            padding: 10px;
        }

        .item-card-header {
            grid-template-columns: minmax(0, 1fr) auto;
        }

        .item-card-total {
            display: none;
        }

        .item-primary-grid,
        .item-secondary-grid {
            grid-template-columns: 1fr;
            padding: 12px;
        }

        .item-name-field,
        .item-store-field {
            grid-column: auto;
        }
    }
</style>
@stop
