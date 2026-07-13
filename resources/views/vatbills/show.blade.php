@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container-fluid py-3 vat-ledger-page">
        @if (Session::has('vat_success'))
            <div class="alert alert-success fw-bold">{{ Session::get('vat_success') }}</div>
        @endif

        <form method="GET" action="{{ route('vat-bills.show', $invoice) }}" class="ledger-filter card card-body mb-3">
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
                    <label for="from_date" class="form-label">From Date (A.D.)</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-2">
                    <label for="to_date" class="form-label">To Date (A.D.)</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Generate Ledger</button>
                    <a class="btn btn-danger" target="_blank"
                        href="{{ route('vat-bills.ledger.pdf', ['invoice' => $invoice->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
                        PDF
                    </a>
                    <button class="btn btn-secondary" type="button" onclick="window.print()">Print</button>
                    <a class="btn btn-success" target="_blank"
                        href="{{ route('vat-bills.confirmation', ['invoice' => $invoice->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
                        Confirmation Letter
                    </a>
                </div>
            </div>
        </form>

        @include('vatbills._ledger')

        <div class="mt-3 no-print">
            <a href="{{ route('onlyviewbillafterbill', ['invoiceid' => $invoice->id]) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Invoice
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .navbar, .breadcrumb, .ledger-filter, .no-print { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    .vat-ledger-page { padding: 0 !important; }
}
</style>
@stop
