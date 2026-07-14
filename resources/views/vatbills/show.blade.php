@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container-fluid py-3 vat-ledger-page">
        @if (Session::has('vat_success'))
            <div class="alert alert-success fw-bold">{{ Session::get('vat_success') }}</div>
        @endif

        @if ($errors->has('from_date_bs') || $errors->has('to_date_bs') || $errors->has('to_date'))
            <div class="alert alert-danger fw-bold">
                {{ $errors->first('from_date_bs') ?: ($errors->first('to_date_bs') ?: $errors->first('to_date')) }}
            </div>
        @endif

        <form method="GET" action="{{ route('vat-party-ledgers.show', $anchorVatBill) }}" class="ledger-filter card card-body mb-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="firm_type" class="form-label">Firm</label>
                    <select name="firm_type" id="firm_type" class="form-select">
                        @foreach ($firms as $firm)
                            <option value="{{ $firm }}" {{ $firmType === $firm ? 'selected' : '' }}>{{ $firm }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from_date_bs" class="form-label">From Date (B.S.)</label>
                    <div class="input-group bs-date-input">
                        <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                        <input type="text" name="from_date_bs" id="from_date_bs" class="form-control"
                            value="{{ old('from_date_bs', $fromDateBs) }}" placeholder="YYYY-MM-DD"
                            inputmode="numeric" pattern="\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}" title="Enter B.S. date as YYYY-MM-DD">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="to_date_bs" class="form-label">To Date (B.S.)</label>
                    <div class="input-group bs-date-input">
                        <span class="input-group-text"><i class="fa-solid fa-calendar-check"></i></span>
                        <input type="text" name="to_date_bs" id="to_date_bs" class="form-control"
                            value="{{ old('to_date_bs', $toDateBs) }}" placeholder="YYYY-MM-DD"
                            inputmode="numeric" pattern="\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}" title="Enter B.S. date as YYYY-MM-DD">
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Generate Ledger</button>
                    <a class="btn btn-danger" target="_blank"
                        href="{{ route('vat-party-ledgers.pdf', ['vatBill' => $anchorVatBill->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
                        PDF
                    </a>
                    <button class="btn btn-secondary" type="button" onclick="window.print()">Print</button>
                    <a class="btn btn-success" target="_blank"
                        href="{{ route('vat-party-ledgers.confirmation', ['vatBill' => $anchorVatBill->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
                        Confirmation Letter
                    </a>
                    <button class="btn btn-warning" type="button" id="toggleInlineVatForm">
                        <i class="fa-solid fa-plus"></i> Add VAT Bill
                    </button>
                </div>
            </div>
        </form>

        <div class="inline-vat-card {{ $errors->any() ? '' : 'd-none' }}" id="inlineVatCard">
            <div class="inline-vat-heading">
                <div>
                    <h5><i class="fa-solid fa-file-circle-plus"></i> Add VAT Bill to This Party Ledger</h5>
                    <p>Party details and firm are copied automatically.</p>
                </div>
                <button type="button" class="btn-close" id="closeInlineVatForm" aria-label="Close"></button>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="copied-party-details">
                <span><strong>Party:</strong> {{ $customer->name }}</span>
                <span><strong>VAT No:</strong> {{ $partyVatNo }}</span>
                <span><strong>Phone:</strong> {{ $customer->phoneno ?: '-' }}</span>
                <span><strong>Firm:</strong> {{ $firmType }}</span>
            </div>

            <form method="POST" action="{{ route('vat-party-ledgers.add', $anchorVatBill) }}" id="inlineVatForm">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="inline_vat_date" class="form-label">VAT Date</label>
                        <input type="date" id="inline_vat_date" name="date" class="form-control"
                            value="{{ old('date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label for="inline_bill_no" class="form-label">VAT Bill No.</label>
                        <input type="text" id="inline_bill_no" name="bill_no" class="form-control"
                            value="{{ old('bill_no') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label for="inline_taxable_amount" class="form-label">Taxable Amount</label>
                        <input type="number" min="0" step="0.01" id="inline_taxable_amount" name="amount_without_tax"
                            class="form-control" value="{{ old('amount_without_tax') }}" required>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">VAT 13%</label>
                        <input type="text" id="inline_vat_preview" class="form-control bg-light" value="0.00" readonly>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Total</label>
                        <input type="text" id="inline_total_preview" class="form-control bg-light fw-bold" value="0.00" readonly>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100" id="inlineVatSubmit">Confirm & Add</button>
                    </div>
                </div>
            </form>
        </div>

        @include('vatbills._ledger', ['showActions' => true])

        <div class="mt-3 no-print">
            @if($invoice)
                <a href="{{ route('onlyviewbillafterbill', ['invoiceid' => $invoice->id]) }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to Invoice</a>
            @else
                <a href="{{ route('vat-bills.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back to VAT Ledgers</a>
            @endif
        </div>
    </div>
</div>

<style>
.inline-vat-card{background:#fff;border:1px solid #f5c26b;border-left:6px solid #d97706;border-radius:8px;margin-bottom:16px;padding:16px}.inline-vat-heading{align-items:flex-start;display:flex;justify-content:space-between}.inline-vat-heading h5{color:#92400e;font-weight:900;margin:0}.inline-vat-heading p{color:#64748b;font-size:13px;margin:2px 0 10px}.copied-party-details{background:#fff7e6;border-radius:5px;display:flex;flex-wrap:wrap;gap:8px 25px;margin-bottom:13px;padding:9px 12px}.copied-party-details span{font-size:13px}.ledger-filter .col-md-4{flex-wrap:wrap}.ledger-filter .btn{white-space:nowrap}
.bs-date-input .input-group-text{background:#eff6ff;border-color:#bfdbfe;color:#2563eb}.bs-date-input .form-control{border-color:#bfdbfe;font-weight:700;letter-spacing:.3px}.bs-date-input .form-control:focus{border-color:#3b82f6;box-shadow:0 0 0 .2rem rgba(59,130,246,.15)}
@media print {
    .sidebar, .navbar, .breadcrumb, .ledger-filter, .inline-vat-card, .no-print { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    .vat-ledger-page { padding: 0 !important; }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var card = document.getElementById('inlineVatCard');
    var toggle = document.getElementById('toggleInlineVatForm');
    var close = document.getElementById('closeInlineVatForm');
    var form = document.getElementById('inlineVatForm');
    var taxable = document.getElementById('inline_taxable_amount');
    var vatPreview = document.getElementById('inline_vat_preview');
    var totalPreview = document.getElementById('inline_total_preview');

    toggle.addEventListener('click', function () {
        card.classList.toggle('d-none');
        if (!card.classList.contains('d-none')) document.getElementById('inline_bill_no').focus();
    });
    close.addEventListener('click', function () { card.classList.add('d-none'); });

    function updatePreview() {
        var amount = parseFloat(taxable.value) || 0;
        var vat = Math.round((amount * 0.13 + Number.EPSILON) * 100) / 100;
        vatPreview.value = vat.toFixed(2);
        totalPreview.value = (amount + vat).toFixed(2);
    }
    taxable.addEventListener('input', updatePreview);
    updatePreview();

    form.addEventListener('submit', function (event) {
        var partyName = @json($customer->name);
        var message = 'Confirm VAT bill #' + document.getElementById('inline_bill_no').value
            + ' for ' + partyName + '?\nTaxable: Rs ' + (parseFloat(taxable.value) || 0).toFixed(2)
            + '\nVAT 13%: Rs ' + vatPreview.value + '\nTotal: Rs ' + totalPreview.value;
        if (!window.confirm(message)) event.preventDefault();
    });
});
</script>
@stop
