@extends('layouts.master')

@section('content')
@php
    $isEditing = $bill !== null;
    $savedItems = $isEditing
        ? $bill->items->map(fn ($item) => [
            'item_name' => $item->item_name,
            'quantity' => $item->quantity,
            'unit' => $item->unit,
            'rate' => $item->rate,
        ])->all()
        : [['item_name' => '', 'quantity' => 1, 'unit' => 'pcs', 'rate' => '']];
    $formItems = old('items', $savedItems);
@endphp
<style>
    .supplier-info-card,
    .supplier-info-card .card-body {
        overflow: visible !important;
    }

    .supplier-info-card {
        position: relative;
        z-index: 10;
    }

    .supplier-info-card .search-box,
    .supplier-info-card .search-input {
        width: 100%;
    }

    /* app.css makes every thead and tbody a separate table. Keep this form's
       header and rows in one table so all six columns share the same grid. */
    #itemsTable {
        table-layout: fixed !important;
    }

    #itemsTable thead {
        display: table-header-group !important;
        width: auto !important;
    }

    #itemsTable tbody {
        display: table-row-group !important;
        width: auto !important;
    }

    #itemsTable tr {
        display: table-row !important;
        width: auto !important;
    }

    #itemsTable th,
    #itemsTable td {
        box-sizing: border-box;
    }

    #itemsTable .form-control {
        min-width: 0;
        width: 100%;
    }
</style>
<div class="main-content">
<div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ $isEditing ? 'Edit Supplier VAT Bill' : 'Create Supplier VAT Bill' }}</h2>
            <p class="text-muted mb-0">VAT is calculated automatically at 13%.</p>
        </div>
        <a href="{{ $isEditing ? route('supplier-vat-bills.show', $bill) : route('supplier-vat-bills.index') }}" class="btn btn-outline-secondary">
            {{ $isEditing ? 'Cancel Edit' : 'Back to VAT System' }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the highlighted fields.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $isEditing ? route('supplier-vat-bills.update', $bill) : route('supplier-vat-bills.store') }}" method="post" id="vatBillForm">
        @csrf
        @if ($isEditing)
            @method('PUT')
        @endif
        <div class="card mb-4 supplier-info-card">
            <div class="card-header">Bill Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label for="firm_type" class="form-label">Our Firm <span class="text-danger">*</span></label>
                        <select name="firm_type" id="firm_type" class="form-select @error('firm_type') is-invalid @enderror" required>
                            <option value="">Choose firm</option>
                            @foreach ($firms as $firm)
                                <option value="{{ $firm }}" @selected(old('firm_type', $bill?->firm_type) === $firm)>{{ $firm }}</option>
                            @endforeach
                        </select>
                        @error('firm_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <label for="searchCustomerInput" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <div class="search-box w-100">
                            <input type="hidden" name="company_id" id="customerIdInput" value="{{ old('company_id', $bill?->company_id) }}">
                            <input type="text"
                                id="searchCustomerInput"
                                class="search-input form-control @error('company_id') is-invalid @enderror"
                                value="{{ $selectedCompany?->name }}"
                                data-api="company_search"
                                placeholder="Type supplier name to search"
                                autocomplete="off"
                                required>
                            <i class="fas fa-search search-icon"></i>
                            <div class="result-wrapper" id="customerResultWrapper" style="display: none;">
                                <div class="result-box d-flex justify-content-start align-items-center" id="customerLoadingResultBox">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <h1 class="m-0 px-2">Loading</h1>
                                </div>
                                <div class="result-box d-flex justify-content-start align-items-center d-none" id="customerNotFoundResultBox">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <h1 class="m-0 px-2">Supplier Not Found</h1>
                                </div>
                                <div id="customerResultList"></div>
                            </div>
                        </div>
                        @error('company_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        <div id="supplierSelectedInfo" class="small text-success fw-bold mt-2" @if (!$selectedCompany) style="display: none;" @endif>
                            Selected: <span id="supplierSelectedName">{{ $selectedCompany?->name }}</span>
                            <span id="supplierSelectedAddress">{{ $selectedCompany?->address ? ' - '.$selectedCompany->address : '' }}</span>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="bill_no" class="form-label">Bill No <span class="text-danger">*</span></label>
                        <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no', $bill?->bill_no) }}" class="form-control @error('bill_no') is-invalid @enderror" maxlength="100" required>
                        @error('bill_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="bill_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="bill_date" id="bill_date" value="{{ old('bill_date', $bill?->bill_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control @error('bill_date') is-invalid @enderror" required>
                        @error('bill_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <span>Items</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="itemsTable">
                        <colgroup>
                            <col style="width: 5%">
                            <col style="width: 7%">
                            <col style="width: 28%">
                            <col style="width: 12%">
                            <col style="width: 12%">
                            <col style="width: 17%">
                            <col style="width: 19%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">S.N</th>
                                <th class="text-center">
                                    <button type="button" class="btn btn-success" id="addItem" title="Add new item row">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($formItems as $index => $item)
                                <tr class="item-row">
                                    <td class="serial-number text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger remove-item" title="Delete this item row">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </td>
                                    <td><input type="text" name="items[{{ $index }}][item_name]" value="{{ $item['item_name'] ?? '' }}" class="form-control item-name" required></td>
                                    <td><input type="number" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" class="form-control quantity" min="0.001" step="0.001" required></td>
                                    <td><input type="text" name="items[{{ $index }}][unit]" value="{{ $item['unit'] ?? 'pcs' }}" class="form-control unit" maxlength="30" required></td>
                                    <td><input type="number" name="items[{{ $index }}][rate]" value="{{ $item['rate'] ?? '' }}" class="form-control rate" min="0" step="0.01" required></td>
                                    <td><input type="text" class="form-control line-amount text-end" value="0.00" readonly tabindex="-1"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header">Notes</div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control" rows="4" maxlength="2000" placeholder="Optional notes">{{ old('notes', $bill?->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-header">Bill Total</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between py-2 border-bottom"><span>Taxable Amount</span><strong id="taxableAmount">0.00</strong></div>
                        <div class="d-flex justify-content-between py-2 border-bottom"><span>VAT (13%)</span><strong id="vatAmount">0.00</strong></div>
                        <div class="d-flex justify-content-between py-3 fs-4"><span>Grand Total</span><strong id="grandTotal">0.00</strong></div>
                        <button type="submit" class="btn btn-primary btn-lg w-100" id="saveButton"><i class="fa fa-floppy-disk me-2"></i>{{ $isEditing ? 'Update VAT Bill' : 'Save VAT Bill' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.querySelector('#itemsTable tbody');
    const addButton = document.getElementById('addItem');
    const form = document.getElementById('vatBillForm');

    $(document).on('customer:selected', function (event, supplier) {
        document.getElementById('supplierSelectedName').textContent = supplier.name || '';
        document.getElementById('supplierSelectedAddress').textContent = supplier.address ? ' - ' + supplier.address : '';
        $('#supplierSelectedInfo').slideDown(150);
    });

    document.getElementById('searchCustomerInput').addEventListener('input', function () {
        $('#supplierSelectedInfo').hide();
    });

    function reindexRows() {
        body.querySelectorAll('.item-row').forEach(function (row, index) {
            row.querySelector('.serial-number').textContent = index + 1;
            row.querySelector('.item-name').name = `items[${index}][item_name]`;
            row.querySelector('.quantity').name = `items[${index}][quantity]`;
            row.querySelector('.unit').name = `items[${index}][unit]`;
            row.querySelector('.rate').name = `items[${index}][rate]`;
        });
    }

    function calculateTotals() {
        let taxable = 0;
        body.querySelectorAll('.item-row').forEach(function (row) {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const rate = parseFloat(row.querySelector('.rate').value) || 0;
            const amount = Math.round((quantity * rate + Number.EPSILON) * 100) / 100;
            row.querySelector('.line-amount').value = amount.toFixed(2);
            taxable += amount;
        });
        taxable = Math.round((taxable + Number.EPSILON) * 100) / 100;
        const vat = Math.round((taxable * 0.13 + Number.EPSILON) * 100) / 100;
        document.getElementById('taxableAmount').textContent = taxable.toFixed(2);
        document.getElementById('vatAmount').textContent = vat.toFixed(2);
        document.getElementById('grandTotal').textContent = (taxable + vat).toFixed(2);
    }

    addButton.addEventListener('click', function () {
        if (body.querySelectorAll('.item-row').length >= 50) return;
        const row = document.createElement('tr');
        row.className = 'item-row';
        row.innerHTML = `
            <td class="serial-number text-center"></td>
            <td class="text-center"><button type="button" class="btn btn-danger remove-item" title="Delete this item row"><i class="fa-solid fa-xmark"></i></button></td>
            <td><input type="text" class="form-control item-name" required></td>
            <td><input type="number" class="form-control quantity" value="1" min="0.001" step="0.001" required></td>
            <td><input type="text" class="form-control unit" value="pcs" maxlength="30" required></td>
            <td><input type="number" class="form-control rate" min="0" step="0.01" required></td>
            <td><input type="text" class="form-control line-amount text-end" value="0.00" readonly tabindex="-1"></td>`;
        body.appendChild(row);
        reindexRows();
        row.querySelector('.item-name').focus();
    });

    body.addEventListener('input', calculateTotals);
    body.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-item');
        if (!button) return;
        if (body.querySelectorAll('.item-row').length === 1) {
            alert('At least one item is required.');
            return;
        }
        button.closest('.item-row').remove();
        reindexRows();
        calculateTotals();
    });

    form.addEventListener('submit', function () {
        const button = document.getElementById('saveButton');
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Saving...';
    });

    reindexRows();
    calculateTotals();
});
</script>
@endsection
