@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
@php($isEdit = !empty($vatBill))
<div class="main-content">
    @yield('breadcrumb')

    <div class="container py-4">
        <div class="card shadow-sm mx-auto" style="max-width: 720px;">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ $isEdit ? 'Edit' : 'Add' }} VAT Bill for Invoice #{{ $invoice->id }}</h4>
            </div>

            <div class="card-body p-4">
                <p class="text-muted mb-4">
                    Original invoice customer: <strong>{{ optional($invoice->customer)->name }}</strong>
                    <span class="badge bg-secondary">{{ strtoupper($invoice->inv_type) }}</span>
                </p>

                <form action="{{ $isEdit ? route('vat-bills.update', $invoice) : route('vat-bills.store', $invoice) }}" method="POST" id="vatBillForm">
                    @csrf
                    @if ($isEdit)
                        @method('PUT')
                    @endif

                    <div class="party-details-panel mb-4">
                        <h5>VAT Party Details</h5>
                        <p>For an old cash sale, enter the actual person or business receiving the VAT bill.</p>
                        <div class="mb-3">
                            <label for="party_name" class="form-label">Party Name</label>
                            <input type="text" id="party_name" name="party_name" class="form-control @error('party_name') is-invalid @enderror"
                                value="{{ old('party_name', $isEdit ? $vatBill->party_name : optional($invoice->customer)->name) }}" required>
                            @error('party_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="party_address" class="form-label">Party Address</label>
                                <input type="text" id="party_address" name="party_address" class="form-control"
                                    value="{{ old('party_address', $isEdit ? $vatBill->party_address : optional($invoice->customer)->address) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="party_vat_no" class="form-label">Party VAT No.</label>
                                <input type="text" id="party_vat_no" name="party_vat_no" class="form-control"
                                    value="{{ old('party_vat_no', $isEdit ? $vatBill->party_vat_no : optional($invoice->customer)->vat_no) }}">
                            </div>
                        </div>
                        <div>
                            <label for="party_phone" class="form-label">Party Phone</label>
                            <input type="text" id="party_phone" name="party_phone" class="form-control"
                                value="{{ old('party_phone', $isEdit ? $vatBill->party_phone : optional($invoice->customer)->phoneno) }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" id="date" name="date"
                            class="form-control @error('date') is-invalid @enderror"
                            value="{{ old('date', $isEdit ? $vatBill->date->format('Y-m-d') : $invoice->inv_date) }}" required>
                        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="bill_no" class="form-label">Bill No.</label>
                        <input type="text" id="bill_no" name="bill_no"
                            class="form-control @error('bill_no') is-invalid @enderror"
                            value="{{ old('bill_no', $isEdit ? $vatBill->bill_no : '') }}" maxlength="100" required autofocus>
                        @error('bill_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount_without_tax" class="form-label">Amount Without Tax</label>
                        <input type="number" id="amount_without_tax" name="amount_without_tax"
                            class="form-control @error('amount_without_tax') is-invalid @enderror"
                            value="{{ old('amount_without_tax', $isEdit ? $vatBill->amount_without_tax : '') }}" min="0" step="0.01" required>
                        @error('amount_without_tax')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="vat_preview" class="form-label">VAT (13%)</label>
                            <input type="text" id="vat_preview" class="form-control bg-light" value="0.00" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="total_preview" class="form-label">Total Amount</label>
                            <input type="text" id="total_preview" class="form-control bg-light fw-bold" value="0.00" readonly>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="firm_type" class="form-label">Firm Type</label>
                        <select id="firm_type" name="firm_type"
                            class="form-select @error('firm_type') is-invalid @enderror" required>
                            <option value="">Select Firm</option>
                            @foreach ($firms as $firm)
                                <option value="{{ $firm }}"
                                    data-vat-no="{{ $firmVatNumbers[$firm] }}"
                                    data-contact-numbers="{{ $firmContactNumbers[$firm] }}"
                                    {{ old('firm_type', $isEdit ? $vatBill->firm_type : '') === $firm ? 'selected' : '' }}>
                                    {{ $firm }} (VAT No: {{ $firmVatNumbers[$firm] }})
                                </option>
                            @endforeach
                        </select>
                        @error('firm_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="firm_vat_no" class="form-label">Firm VAT No.</label>
                        <input type="text" id="firm_vat_no" class="form-control bg-light" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="firm_contact_numbers" class="form-label">Firm Contact No.</label>
                        <input type="text" id="firm_contact_numbers" class="form-control bg-light" readonly>
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('onlyviewbillafterbill', ['invoiceid' => $invoice->id]) }}"
                            class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success px-4" id="confirmVatBillButton">
                            {{ $isEdit ? 'Confirm & Update Ledger' : 'Confirm & Add to Ledger' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var taxableInput = document.getElementById('amount_without_tax');
    var vatPreview = document.getElementById('vat_preview');
    var totalPreview = document.getElementById('total_preview');
    var firmSelect = document.getElementById('firm_type');
    var firmVatNo = document.getElementById('firm_vat_no');
    var firmContactNumbers = document.getElementById('firm_contact_numbers');
    var vatBillForm = document.getElementById('vatBillForm');
    var confirmButton = document.getElementById('confirmVatBillButton');
    var isConfirmed = false;

    function updateVatPreview() {
        var taxable = parseFloat(taxableInput.value) || 0;
        var vat = Math.round((taxable * 0.13 + Number.EPSILON) * 100) / 100;

        vatPreview.value = vat.toFixed(2);
        totalPreview.value = (taxable + vat).toFixed(2);
    }

    taxableInput.addEventListener('input', updateVatPreview);

    function updateFirmVatNo() {
        var option = firmSelect.options[firmSelect.selectedIndex];
        firmVatNo.value = option ? (option.dataset.vatNo || '') : '';
        firmContactNumbers.value = option ? (option.dataset.contactNumbers || '') : '';
    }

    firmSelect.addEventListener('change', updateFirmVatNo);

    vatBillForm.addEventListener('submit', function (event) {
        if (isConfirmed) {
            return;
        }

        event.preventDefault();

        var selectedFirm = firmSelect.options[firmSelect.selectedIndex];
        var message = [
            'Please confirm the VAT bill details:',
            '',
            'Firm: ' + (selectedFirm ? selectedFirm.value : ''),
            'Firm VAT No: ' + firmVatNo.value,
            'Contact No: ' + firmContactNumbers.value,
            'Party: ' + document.getElementById('party_name').value,
            'Party VAT No: ' + (document.getElementById('party_vat_no').value || '-'),
            'Date: ' + document.getElementById('date').value,
            'Bill No: ' + document.getElementById('bill_no').value,
            'Taxable Amount: Rs ' + (parseFloat(taxableInput.value) || 0).toFixed(2),
            'VAT (13%): Rs ' + vatPreview.value,
            'Total Amount: Rs ' + totalPreview.value,
            '',
            @json($isEdit ? 'Update this bill in the party ledger?' : 'Add this bill to the party ledger?')
        ].join('\n');

        if (!window.confirm(message)) {
            return;
        }

        isConfirmed = true;
        confirmButton.disabled = true;
        confirmButton.textContent = @json($isEdit ? 'Updating Ledger...' : 'Adding to Ledger...');
        vatBillForm.submit();
    });

    updateVatPreview();
    updateFirmVatNo();
});
</script>
<style>.party-details-panel{background:#f0f7ff;border:1px solid #bfdbfe;border-left:5px solid #2563eb;border-radius:7px;padding:16px}.party-details-panel h5{color:#1e3a8a;font-weight:900;margin:0}.party-details-panel p{color:#64748b;font-size:13px;margin:3px 0 14px}</style>
@stop
