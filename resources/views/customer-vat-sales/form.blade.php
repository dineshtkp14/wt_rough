@extends('layouts.master')

@section('content')
@php
    $isEditing = $sale !== null;
    $savedItems = $isEditing
        ? $sale->items->map(fn ($line) => [
            'vat_stock_item_id' => $line->vat_stock_item_id,
            'item_name' => $line->item_name,
            'quantity' => $line->quantity,
            'unit' => $line->unit,
            'rate' => $line->rate,
            'available' => (float) ($line->vatStockItem?->quantity ?? 0) + (float) $line->quantity,
        ])->all()
        : [['vat_stock_item_id' => '', 'item_name' => '', 'quantity' => 1, 'unit' => 'pcs', 'rate' => '', 'available' => 0]];
    $formItems = old('items', $savedItems);
    $customerNameValue = old('customer_name', $sale?->customer_name ?? $selectedCustomer?->name);
    $customerVatNoValue = old('customer_vat_no', $sale?->customer_vat_no ?? $selectedCustomer?->vat_no);
@endphp
<style>
    .vat-sale-info-card, .vat-sale-info-card .card-body { overflow: visible !important; }
    .vat-sale-info-card { position: relative; z-index: 20; }
    #vatSaleItemsTable { table-layout: fixed !important; }
    #vatSaleItemsTable thead { display: table-header-group !important; width: auto !important; }
    #vatSaleItemsTable tbody { display: table-row-group !important; width: auto !important; }
    #vatSaleItemsTable tr { display: table-row !important; width: auto !important; }
    #vatSaleItemsTable .form-control { min-width: 0; width: 100%; }
    .stock-search-cell { position: relative; }
    .stock-results { background: #fff; border: 2px solid #3348d4; border-radius: 8px; box-shadow: 0 18px 38px rgba(15,23,42,.28); display: none; left: 0; max-height: 320px; max-width: calc(100vw - 24px); min-width: 330px; overflow-y: auto; position: fixed; top: 0; width: 440px; z-index: 10000; }
    .stock-result { border-bottom: 1px solid #dbe3ef; cursor: pointer; padding: 13px 14px; }
    .stock-result:hover { background: #eef6ff; }
    .stock-result strong, .stock-result small { display: block; }
    .stock-result strong { color: #0f172a; font-size: 16px; }
    .stock-result small { color: #475569; font-size: 13px; font-weight: 700; margin-top: 5px; }
    .stock-result-message { color: #475569; font-weight: 800; padding: 15px; }
    .stock-available { color: #15803d; display: block; font-size: 12px; font-weight: 800; margin-top: 4px; }
    .quick-customer-overlay { align-items: center; background: rgba(15, 23, 42, .45); display: none; inset: 0; justify-content: center; padding: 18px; position: fixed; z-index: 10050; }
    .quick-customer-modal { background: #fff; border-radius: 8px; box-shadow: 0 24px 70px rgba(15, 23, 42, .35); max-width: 620px; overflow: hidden; width: 100%; }
    .quick-customer-header { align-items: center; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; padding: 15px 18px; }
    .quick-customer-header h5 { font-size: 18px; font-weight: 900; margin: 0; }
    .quick-customer-body { padding: 18px; }
    .quick-customer-footer { border-top: 1px solid #e2e8f0; display: flex; gap: 10px; justify-content: flex-end; padding: 14px 18px; }
    .quick-customer-errors { color: #b91c1c; display: none; font-size: 13px; font-weight: 700; margin-top: 10px; }
</style>
<div class="main-content"><div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h2 class="mb-1">{{ $isEditing ? 'Edit Customer VAT Sale' : 'Create Customer VAT Sale' }}</h2><p class="text-muted mb-0">Independent VAT stock reduces automatically. Normal project stock is not affected. VAT is fixed at 13%.</p></div>
        <a href="{{ $isEditing ? route('customer-vat-sales.show', $sale) : route('customer-vat-sales.index') }}" class="btn btn-outline-secondary">{{ $isEditing ? 'Cancel Edit' : 'Back to VAT Sales' }}</a>
    </div>
    @if ($errors->any())<div class="alert alert-danger"><strong>Please correct these fields:</strong><ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form method="post" action="{{ $isEditing ? route('customer-vat-sales.update', $sale) : route('customer-vat-sales.store') }}" id="customerVatSaleForm">
        @csrf @if($isEditing) @method('PUT') @endif
        <div class="card mb-4 vat-sale-info-card"><div class="card-header">VAT Bill Information</div><div class="card-body"><div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <label class="form-label" for="myfirm_id">Our Firm <span class="text-danger">*</span></label>
                <select name="myfirm_id" id="myfirm_id" class="form-select @error('myfirm_id') is-invalid @enderror" required>
                    <option value="">Choose firm</option>
                    @foreach ($firms as $firm)<option value="{{ $firm->id }}" @selected(old('myfirm_id', $sale?->myfirm_id) == $firm->id)>{{ $firm->firm_name }}</option>@endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <label class="form-label mb-0" for="searchCustomerInput">Customer Name <span class="text-danger">*</span></label>
                    <button type="button" class="btn btn-sm btn-outline-success" id="openQuickCustomerBtn"><i class="fa fa-plus me-1"></i>Add New</button>
                </div>
                <div class="search-box w-100">
                    <input type="hidden" name="customer_id" id="customerIdInput" value="{{ old('customer_id', $sale?->customer_id) }}">
                    <input type="text" name="customer_name" id="searchCustomerInput" class="search-input form-control @error('customer_name') is-invalid @enderror" value="{{ $customerNameValue }}" data-api="customer_search" placeholder="Type customer name or select saved customer" autocomplete="off" required>
                    <i class="fas fa-search search-icon"></i>
                    <div class="result-wrapper" id="customerResultWrapper" style="display:none;">
                        <div class="result-box d-flex align-items-center" id="customerLoadingResultBox"><i class="fas fa-spinner fa-spin"></i><h1 class="m-0 px-2">Loading</h1></div>
                        <div class="result-box d-flex align-items-center d-none" id="customerNotFoundResultBox"><i class="fas fa-triangle-exclamation"></i><h1 class="m-0 px-2">Customer Not Found</h1></div>
                        <div id="customerResultList"></div>
                    </div>
                </div>
                <div id="selectedVatCustomer" class="small text-success fw-bold mt-2" @if(!$selectedCustomer) style="display:none" @endif>Selected: <span id="selectedVatCustomerName">{{ $selectedCustomer?->name }}</span></div>
            </div>
            <div class="col-lg-2 col-md-6"><label class="form-label" for="customerVatNoInput">VAT No</label><input id="customerVatNoInput" name="customer_vat_no" value="{{ $customerVatNoValue }}" class="form-control @error('customer_vat_no') is-invalid @enderror" placeholder="Optional VAT/PAN"></div>
            <div class="col-lg-2 col-md-6"><label class="form-label">VAT Bill No <span class="text-danger">*</span></label><input name="bill_no" value="{{ old('bill_no', $sale?->bill_no) }}" class="form-control" required></div>
            <div class="col-lg-2 col-md-6"><label class="form-label">Date <span class="text-danger">*</span></label><input type="date" name="bill_date" value="{{ old('bill_date', $sale?->bill_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control" required></div>
        </div></div></div>

        <div class="card mb-4"><div class="card-header">Stocked Items</div><div class="card-body p-0"><div class="table-responsive">
            <table class="table align-middle mb-0" id="vatSaleItemsTable">
                <colgroup><col style="width:5%"><col style="width:7%"><col style="width:31%"><col style="width:12%"><col style="width:11%"><col style="width:16%"><col style="width:18%"></colgroup>
                <thead><tr><th class="text-center">S.N</th><th class="text-center"><button type="button" class="btn btn-success" id="addVatSaleRow" title="Add row"><i class="fa-solid fa-plus"></i></button></th><th>Stock Item</th><th>Quantity</th><th>Unit</th><th>Rate</th><th>Amount</th></tr></thead>
                <tbody>
                @foreach ($formItems as $index => $line)
                    <tr class="vat-sale-row">
                        <td class="serial-number text-center">{{ $index + 1 }}</td>
                        <td class="text-center"><button type="button" class="btn btn-danger remove-vat-row" title="Delete row"><i class="fa-solid fa-xmark"></i></button></td>
                        <td class="stock-search-cell">
                            <input type="hidden" class="stock-item-id" name="items[{{ $index }}][vat_stock_item_id]" value="{{ $line['vat_stock_item_id'] ?? '' }}">
                            <input type="text" class="form-control stock-item-search" name="items[{{ $index }}][item_name]" value="{{ $line['item_name'] ?? '' }}" placeholder="Search stocked item" autocomplete="off" required>
                            <span class="stock-available" data-available="{{ $line['available'] ?? 0 }}">@if(!empty($line['vat_stock_item_id']))Available: {{ number_format((float) ($line['available'] ?? 0), 3) }}@endif</span>
                            <div class="stock-results"></div>
                        </td>
                        <td><input type="number" class="form-control quantity" name="items[{{ $index }}][quantity]" value="{{ $line['quantity'] ?? 1 }}" min="0.001" step="0.001" required></td>
                        <td><input class="form-control unit" name="items[{{ $index }}][unit]" value="{{ $line['unit'] ?? 'pcs' }}" required></td>
                        <td><input type="number" class="form-control rate" name="items[{{ $index }}][rate]" value="{{ $line['rate'] ?? '' }}" min="0" step="0.01" required></td>
                        <td><input class="form-control line-amount text-end" value="0.00" readonly tabindex="-1"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div></div></div>

        <div class="row g-4"><div class="col-lg-7"><div class="card h-100"><div class="card-header">Notes</div><div class="card-body"><textarea name="notes" rows="4" maxlength="2000" class="form-control" placeholder="Optional notes">{{ old('notes', $sale?->notes) }}</textarea></div></div></div>
        <div class="col-lg-5"><div class="card h-100"><div class="card-header">VAT Total</div><div class="card-body"><div class="d-flex justify-content-between py-2 border-bottom"><span>Taxable Amount</span><strong id="taxableTotal">0.00</strong></div><div class="d-flex justify-content-between py-2 border-bottom"><span>VAT (13%)</span><strong id="vatTotal">0.00</strong></div><div class="d-flex justify-content-between py-3 fs-4"><span>Grand Total</span><strong id="customerVatGrandTotal">0.00</strong></div><button class="btn btn-primary btn-lg w-100" id="saveVatSale"><i class="fa fa-floppy-disk me-2"></i>{{ $isEditing ? 'Update VAT Sale' : 'Save VAT Sale' }}</button></div></div></div></div>
    </form>

    <div class="quick-customer-overlay" id="quickCustomerOverlay" aria-hidden="true">
        <div class="quick-customer-modal">
            <div class="quick-customer-header">
                <h5>Add Customer</h5>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="closeQuickCustomerBtn"><i class="fa fa-xmark"></i></button>
            </div>
            <div class="quick-customer-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="quickCustomerName" autocomplete="off"></div>
                    <div class="col-md-6"><label class="form-label">Address <span class="text-danger">*</span></label><input type="text" class="form-control" id="quickCustomerAddress" autocomplete="off"></div>
                    <div class="col-md-6"><label class="form-label">Phone No <span class="text-danger">*</span></label><input type="text" class="form-control" id="quickCustomerPhone" maxlength="10" autocomplete="off"></div>
                    <div class="col-md-6"><label class="form-label">VAT No</label><input type="text" class="form-control" id="quickCustomerVatNo" autocomplete="off"></div>
                    <div class="col-md-6"><label class="form-label">Type</label><select class="form-select" id="quickCustomerType"><option value="shop">Shop</option><option value="customer">Customer</option></select></div>
                    <div class="col-md-6"><label class="form-label">Remarks</label><input type="text" class="form-control" id="quickCustomerRemarks" autocomplete="off"></div>
                </div>
                <div class="quick-customer-errors" id="quickCustomerErrors"></div>
            </div>
            <div class="quick-customer-footer">
                <button type="button" class="btn btn-outline-secondary" id="cancelQuickCustomerBtn">Cancel</button>
                <button type="button" class="btn btn-success" id="saveQuickCustomerBtn"><i class="fa fa-floppy-disk me-1"></i>Save Customer</button>
            </div>
        </div>
    </div>
</div></div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const body = document.querySelector('#vatSaleItemsTable tbody');
    const firm = document.getElementById('myfirm_id');
    const form = document.getElementById('customerVatSaleForm');
    const endpoint = @json(route('customer-vat-sales.stock-items'));
    const quickCustomerUrl = @json(route('customerinfos.quick-store'));
    const csrfToken = @json(csrf_token());
    const customerNameInput = document.getElementById('searchCustomerInput');
    const customerIdInput = document.getElementById('customerIdInput');
    const customerVatNoInput = document.getElementById('customerVatNoInput');
    const quickCustomerOverlay = document.getElementById('quickCustomerOverlay');
    const quickCustomerErrors = document.getElementById('quickCustomerErrors');
    const quickCustomerSaveBtn = document.getElementById('saveQuickCustomerBtn');
    let timer;

    function escapeHtml(value) { const div = document.createElement('div'); div.textContent = value == null ? '' : value; return div.innerHTML; }
    function showQuickCustomerErrors(messages) { quickCustomerErrors.innerHTML = messages.map(escapeHtml).join('<br>'); quickCustomerErrors.style.display = 'block'; }
    function openQuickCustomer() { document.getElementById('quickCustomerName').value = customerNameInput.value.trim(); document.getElementById('quickCustomerVatNo').value = customerVatNoInput.value.trim(); quickCustomerErrors.style.display = 'none'; quickCustomerErrors.innerHTML = ''; quickCustomerOverlay.style.display = 'flex'; quickCustomerOverlay.setAttribute('aria-hidden', 'false'); setTimeout(function () { document.getElementById('quickCustomerName').focus(); }, 30); }
    function closeQuickCustomer() { quickCustomerOverlay.style.display = 'none'; quickCustomerOverlay.setAttribute('aria-hidden', 'true'); }
    function selectVatCustomer(customer) { customerNameInput.value = customer.name || ''; customerIdInput.value = customer.id || ''; customerVatNoInput.value = customer.vat_no || ''; document.getElementById('selectedVatCustomerName').textContent = customer.name || ''; $('#selectedVatCustomer').slideDown(120); }
    function showStockResults(row, html) { const input = row.querySelector('.stock-item-search'); const box = row.querySelector('.stock-results'); const rect = input.getBoundingClientRect(); const width = Math.max(330, rect.width); const left = Math.min(rect.left, window.innerWidth - width - 12); box.style.left = Math.max(12, left) + 'px'; box.style.top = Math.min(rect.bottom + 5, window.innerHeight - 180) + 'px'; box.style.width = width + 'px'; box.innerHTML = html; box.style.display = 'block'; }
    function reindex() { body.querySelectorAll('.vat-sale-row').forEach(function (row, index) { row.querySelector('.serial-number').textContent = index + 1; row.querySelector('.stock-item-id').name = `items[${index}][vat_stock_item_id]`; row.querySelector('.stock-item-search').name = `items[${index}][item_name]`; row.querySelector('.quantity').name = `items[${index}][quantity]`; row.querySelector('.unit').name = `items[${index}][unit]`; row.querySelector('.rate').name = `items[${index}][rate]`; }); }
    function totals() { let taxable = 0; body.querySelectorAll('.vat-sale-row').forEach(function (row) { const amount = Math.round((((parseFloat(row.querySelector('.quantity').value) || 0) * (parseFloat(row.querySelector('.rate').value) || 0)) + Number.EPSILON) * 100) / 100; row.querySelector('.line-amount').value = amount.toFixed(2); taxable += amount; }); taxable = Math.round((taxable + Number.EPSILON) * 100) / 100; const vat = Math.round((taxable * .13 + Number.EPSILON) * 100) / 100; document.getElementById('taxableTotal').textContent = taxable.toFixed(2); document.getElementById('vatTotal').textContent = vat.toFixed(2); document.getElementById('customerVatGrandTotal').textContent = (taxable + vat).toFixed(2); }
    function clearRow(row) { row.querySelector('.stock-item-id').value = ''; row.querySelector('.stock-item-search').value = ''; row.querySelector('.stock-available').textContent = ''; row.querySelector('.stock-available').dataset.available = '0'; row.querySelector('.unit').value = 'pcs'; row.querySelector('.rate').value = ''; row.querySelector('.quantity').value = '1'; row.querySelector('.stock-results').style.display = 'none'; totals(); }
    function newRow() { const row = document.createElement('tr'); row.className = 'vat-sale-row'; row.innerHTML = `<td class="serial-number text-center"></td><td class="text-center"><button type="button" class="btn btn-danger remove-vat-row" title="Delete row"><i class="fa-solid fa-xmark"></i></button></td><td class="stock-search-cell"><input type="hidden" class="stock-item-id"><input type="text" class="form-control stock-item-search" placeholder="Search stocked item" autocomplete="off" required><span class="stock-available" data-available="0"></span><div class="stock-results"></div></td><td><input type="number" class="form-control quantity" value="1" min="0.001" step="0.001" required></td><td><input class="form-control unit" value="pcs" required></td><td><input type="number" class="form-control rate" min="0" step="0.01" required></td><td><input class="form-control line-amount text-end" value="0.00" readonly tabindex="-1"></td>`; body.appendChild(row); reindex(); row.querySelector('.stock-item-search').focus(); }

    document.getElementById('addVatSaleRow').addEventListener('click', function () { if (body.children.length < 50) newRow(); });
    document.getElementById('openQuickCustomerBtn').addEventListener('click', openQuickCustomer);
    document.getElementById('closeQuickCustomerBtn').addEventListener('click', closeQuickCustomer);
    document.getElementById('cancelQuickCustomerBtn').addEventListener('click', closeQuickCustomer);
    quickCustomerOverlay.addEventListener('click', function (event) { if (event.target === quickCustomerOverlay) closeQuickCustomer(); });
    quickCustomerSaveBtn.addEventListener('click', function () {
        const payload = {
            name: document.getElementById('quickCustomerName').value.trim(),
            address: document.getElementById('quickCustomerAddress').value.trim(),
            phoneno: document.getElementById('quickCustomerPhone').value.trim(),
            type: document.getElementById('quickCustomerType').value,
            vat_no: document.getElementById('quickCustomerVatNo').value.trim(),
            remarks: document.getElementById('quickCustomerRemarks').value.trim(),
        };

        quickCustomerSaveBtn.disabled = true;
        quickCustomerSaveBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i>Saving...';
        quickCustomerErrors.style.display = 'none';

        fetch(quickCustomerUrl, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload),
        }).then(function (response) {
            return response.json().then(function (data) {
                if (!response.ok) throw data;
                return data;
            });
        }).then(function (data) {
            selectVatCustomer(data.customer || {});
            closeQuickCustomer();
        }).catch(function (error) {
            const errors = error.errors ? Object.values(error.errors).flat() : [error.message || 'Unable to save customer.'];
            showQuickCustomerErrors(errors);
        }).finally(function () {
            quickCustomerSaveBtn.disabled = false;
            quickCustomerSaveBtn.innerHTML = '<i class="fa fa-floppy-disk me-1"></i>Save Customer';
        });
    });
    body.addEventListener('click', function (event) { const remove = event.target.closest('.remove-vat-row'); if (remove) { if (body.children.length === 1) { alert('At least one item is required.'); return; } remove.closest('.vat-sale-row').remove(); reindex(); totals(); return; } const result = event.target.closest('.stock-result'); if (result) { const row = result.closest('.vat-sale-row'); const data = JSON.parse(decodeURIComponent(result.dataset.item)); row.querySelector('.stock-item-id').value = data.id; row.querySelector('.stock-item-search').value = data.name; row.querySelector('.quantity').value = 1; row.querySelector('.unit').value = data.unit; row.querySelector('.rate').value = Number(data.suggested_rate).toFixed(2); row.querySelector('.stock-available').dataset.available = data.available_quantity; row.querySelector('.stock-available').textContent = `Available: ${Number(data.available_quantity).toFixed(3)}`; row.querySelector('.stock-results').style.display = 'none'; totals(); } });
    body.addEventListener('input', function (event) { totals(); if (!event.target.classList.contains('stock-item-search')) return; const input = event.target; const row = input.closest('.vat-sale-row'); row.querySelector('.stock-item-id').value = ''; row.querySelector('.stock-available').textContent = ''; clearTimeout(timer); const query = input.value.trim(); if (!firm.value) { showStockResults(row, '<div class="stock-result-message"><i class="fa-solid fa-circle-info me-1"></i>Choose Malika or Durga first.</div>'); return; } if (query.length < 1) { row.querySelector('.stock-results').style.display = 'none'; return; } showStockResults(row, '<div class="stock-result-message"><i class="fa-solid fa-spinner fa-spin me-1"></i>Searching stocked items...</div>'); timer = setTimeout(function () { const url = new URL(endpoint); url.searchParams.set('myfirm_id', firm.value); url.searchParams.set('search', query); fetch(url, { headers: { Accept: 'application/json' } }).then(response => response.json()).then(function (items) { showStockResults(row, items.length ? items.map(item => `<div class="stock-result" data-item="${encodeURIComponent(JSON.stringify(item))}"><strong>${escapeHtml(item.name)}</strong><small><i class="fa-solid fa-boxes-stacked me-1"></i>Available: ${Number(item.available_quantity).toFixed(3)} ${escapeHtml(item.unit)} &nbsp; | &nbsp; Suggested rate: ${Number(item.suggested_rate).toFixed(2)}</small></div>`).join('') : '<div class="stock-result-message"><i class="fa-solid fa-triangle-exclamation me-1"></i>No matching stocked item found for this firm.</div>'); }).catch(function () { showStockResults(row, '<div class="stock-result-message text-danger">Unable to load stock suggestions. Please try again.</div>'); }); }, 220); });
    firm.addEventListener('change', function () { body.querySelectorAll('.vat-sale-row').forEach(clearRow); });
    $(document).on('customer:selected', function (event, customer) { selectVatCustomer(customer); });
    customerNameInput.addEventListener('input', function () { $('#selectedVatCustomer').hide(); });
    document.addEventListener('click', function (event) { if (!event.target.closest('.stock-search-cell')) document.querySelectorAll('.stock-results').forEach(box => box.style.display = 'none'); });
    form.addEventListener('submit', function (event) { const invalid = Array.from(body.querySelectorAll('.stock-item-id')).find(input => !input.value); if (invalid) { event.preventDefault(); alert('Please select each item from the stock search results.'); invalid.closest('td').querySelector('.stock-item-search').focus(); return; } const button = document.getElementById('saveVatSale'); button.disabled = true; button.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Saving...'; });
    reindex(); totals();
});
</script>
@endsection
