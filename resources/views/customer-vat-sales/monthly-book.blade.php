@extends('layouts.master')

@section('content')
@php
    $selectedMonth = $monthlySummary->firstWhere('month', $bsMonth);
    $yearTotals = [
        'purchase_taxable' => $monthlySummary->sum('purchase_taxable'),
        'purchase_vat' => $monthlySummary->sum('purchase_vat'),
        'purchase_total' => $monthlySummary->sum('purchase_total'),
        'sales_taxable' => $monthlySummary->sum('sales_taxable'),
        'sales_vat' => $monthlySummary->sum('sales_vat'),
        'sales_total' => $monthlySummary->sum('sales_total'),
    ];
@endphp
<style>
    .vat-book-summary { border-left: 5px solid #3348d4; }
    .vat-book-summary strong { display: block; font-size: 24px; margin-top: 5px; }
    .vat-book-table { white-space: nowrap; }
    .selected-bs-month > * { background: #eaf1ff !important; font-weight: 800; }
    .vat-payable { color: #b91c1c; }
    .vat-credit { color: #15803d; }
    @media print {
        .main-sidebar, .navbar, .no-print, .pagination { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; }
        .card { break-inside: avoid; box-shadow: none !important; }
    }
</style>
<div class="main-content"><div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Monthly VAT Purchase &amp; Sales Book</h2>
            <p class="text-muted mb-0">Nepali month-wise details for {{ $firm->firm_name }}.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 no-print">
            <button type="button" class="btn btn-outline-dark" onclick="window.print()"><i class="fa fa-print me-1"></i>Print Book</button>
            <a href="{{ route('supplier-vat-bills.index') }}" class="btn btn-outline-secondary">VAT System</a>
            <a href="{{ route('customer-vat-sales.stock') }}" class="btn btn-outline-success">VAT Stock</a>
            <a href="{{ route('customer-vat-sales.index') }}" class="btn btn-primary">Customer VAT Sales</a>
        </div>
    </div>

    @if($errors->any())<div class="alert alert-danger no-print">{{ $errors->first() }}</div>@endif

    <div class="card mb-4 no-print"><div class="card-header">Book Period</div><div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-lg-4 col-md-6"><label class="form-label">Firm</label><select name="myfirm_id" class="form-select" required>@foreach($firms as $option)<option value="{{ $option->id }}" @selected($firmId == $option->id)>{{ $option->firm_name }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-3"><label class="form-label">B.S. Year</label><input type="number" name="bs_year" value="{{ $bsYear }}" min="2000" max="2089" class="form-control" required></div>
            <div class="col-lg-4 col-md-6"><label class="form-label">Nepali Month</label><select name="bs_month" class="form-select" required>@foreach($monthlySummary as $month)<option value="{{ $month['month'] }}" @selected($month['month'] == $bsMonth)>{{ $month['month'] }} - {{ $month['month_name'] }}</option>@endforeach</select></div>
            <div class="col-lg-2 col-md-3"><button class="btn btn-primary w-100"><i class="fa fa-search me-1"></i>Show Book</button></div>
        </form>
    </div></div>

    <div class="alert alert-info py-2">
        <strong>{{ $selectedMonth['month_name'] }} {{ $bsYear }} B.S.</strong>
        <span class="ms-2">({{ \App\Support\NepaliDate::adToBsString($monthStart, 'en') }} to {{ \App\Support\NepaliDate::adToBsString($monthEnd, 'en') }})</span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card vat-book-summary h-100"><div class="card-body"><span class="text-muted">Purchase Taxable</span><strong>{{ number_format($selectedMonth['purchase_taxable'], 2) }}</strong><small>Input VAT: {{ number_format($selectedMonth['purchase_vat'], 2) }}</small></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card vat-book-summary h-100"><div class="card-body"><span class="text-muted">Purchase Total</span><strong>{{ number_format($selectedMonth['purchase_total'], 2) }}</strong><small>{{ $selectedMonth['purchase_count'] }} supplier bill(s)</small></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card vat-book-summary h-100"><div class="card-body"><span class="text-muted">Sales Taxable</span><strong>{{ number_format($selectedMonth['sales_taxable'], 2) }}</strong><small>Output VAT: {{ number_format($selectedMonth['sales_vat'], 2) }}</small></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card vat-book-summary h-100"><div class="card-body"><span class="text-muted">Sales Total</span><strong>{{ number_format($selectedMonth['sales_total'], 2) }}</strong><small>{{ $selectedMonth['sales_count'] }} customer bill(s)</small></div></div></div>
    </div>

    <div class="card mb-4"><div class="card-header">{{ $bsYear }} B.S. Month-wise Summary</div><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0 vat-book-table">
            <thead class="table-primary"><tr><th>Month</th><th class="text-end">Purchase Bills</th><th class="text-end">Purchase Taxable</th><th class="text-end">Input VAT</th><th class="text-end">Purchase Total</th><th class="text-end">Sales Bills</th><th class="text-end">Sales Taxable</th><th class="text-end">Output VAT</th><th class="text-end">Sales Total</th><th class="text-end">VAT Payable/(Credit)</th></tr></thead>
            <tbody>
            @foreach($monthlySummary as $month)
                <tr class="{{ $month['month'] == $bsMonth ? 'selected-bs-month' : '' }}">
                    <td><a class="text-decoration-none" href="{{ route('customer-vat-sales.monthly-book', ['myfirm_id' => $firmId, 'bs_year' => $bsYear, 'bs_month' => $month['month']]) }}">{{ $month['month'] }}. {{ $month['month_name'] }}</a></td>
                    <td class="text-end">{{ $month['purchase_count'] }}</td><td class="text-end">{{ number_format($month['purchase_taxable'], 2) }}</td><td class="text-end">{{ number_format($month['purchase_vat'], 2) }}</td><td class="text-end">{{ number_format($month['purchase_total'], 2) }}</td>
                    <td class="text-end">{{ $month['sales_count'] }}</td><td class="text-end">{{ number_format($month['sales_taxable'], 2) }}</td><td class="text-end">{{ number_format($month['sales_vat'], 2) }}</td><td class="text-end">{{ number_format($month['sales_total'], 2) }}</td>
                    <td class="text-end {{ $month['vat_difference'] >= 0 ? 'vat-payable' : 'vat-credit' }}">{{ number_format($month['vat_difference'], 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="table-dark"><tr><th>B.S. Year Total</th><th class="text-end">{{ $monthlySummary->sum('purchase_count') }}</th><th class="text-end">{{ number_format($yearTotals['purchase_taxable'], 2) }}</th><th class="text-end">{{ number_format($yearTotals['purchase_vat'], 2) }}</th><th class="text-end">{{ number_format($yearTotals['purchase_total'], 2) }}</th><th class="text-end">{{ $monthlySummary->sum('sales_count') }}</th><th class="text-end">{{ number_format($yearTotals['sales_taxable'], 2) }}</th><th class="text-end">{{ number_format($yearTotals['sales_vat'], 2) }}</th><th class="text-end">{{ number_format($yearTotals['sales_total'], 2) }}</th><th class="text-end">{{ number_format($yearTotals['sales_vat'] - $yearTotals['purchase_vat'], 2) }}</th></tr></tfoot>
        </table>
    </div></div></div>

    <div class="card mb-4"><div class="card-header">Purchase Book — {{ $selectedMonth['month_name'] }}</div><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0 vat-book-table"><thead><tr><th>S.N.</th><th>Date (B.S.)</th><th>Supplier</th><th>Bill No</th><th class="text-end">Items</th><th class="text-end">Taxable</th><th class="text-end">VAT (13%)</th><th class="text-end">Total</th></tr></thead><tbody>
            @forelse($purchaseBills as $bill)<tr><td>{{ $purchaseBills->firstItem() + $loop->index }}</td><td>{{ \App\Support\NepaliDate::adToBsString($bill->bill_date, 'en') }}</td><td>{{ $bill->company->name }}</td><td><a href="{{ route('supplier-vat-bills.show', $bill) }}">{{ $bill->bill_no }}</a></td><td class="text-end">{{ $bill->items_count }}</td><td class="text-end">{{ number_format((float) $bill->taxable_amount, 2) }}</td><td class="text-end">{{ number_format((float) $bill->vat_amount, 2) }}</td><td class="text-end fw-bold">{{ number_format((float) $bill->total_amount, 2) }}</td></tr>
            @empty<tr><td colspan="8" class="text-center text-muted py-4">No supplier VAT purchases in this Nepali month.</td></tr>@endforelse
        </tbody><tfoot class="table-light"><tr><th colspan="5" class="text-end">Month Purchase Total</th><th class="text-end">{{ number_format($selectedMonth['purchase_taxable'], 2) }}</th><th class="text-end">{{ number_format($selectedMonth['purchase_vat'], 2) }}</th><th class="text-end">{{ number_format($selectedMonth['purchase_total'], 2) }}</th></tr></tfoot></table>
    </div></div></div><div class="mb-4 no-print">{{ $purchaseBills->links() }}</div>

    <div class="card"><div class="card-header">Sales Book — {{ $selectedMonth['month_name'] }}</div><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0 vat-book-table"><thead><tr><th>S.N.</th><th>Date (B.S.)</th><th>Customer</th><th>Bill No</th><th class="text-end">Items</th><th class="text-end">Taxable</th><th class="text-end">VAT (13%)</th><th class="text-end">Total</th></tr></thead><tbody>
            @forelse($salesBills as $bill)<tr><td>{{ $salesBills->firstItem() + $loop->index }}</td><td>{{ \App\Support\NepaliDate::adToBsString($bill->bill_date, 'en') }}</td><td>{{ $bill->customer_display_name }}</td><td><a href="{{ route('customer-vat-sales.show', $bill) }}">{{ $bill->bill_no }}</a></td><td class="text-end">{{ $bill->items_count }}</td><td class="text-end">{{ number_format((float) $bill->taxable_amount, 2) }}</td><td class="text-end">{{ number_format((float) $bill->vat_amount, 2) }}</td><td class="text-end fw-bold">{{ number_format((float) $bill->total_amount, 2) }}</td></tr>
            @empty<tr><td colspan="8" class="text-center text-muted py-4">No customer VAT sales in this Nepali month.</td></tr>@endforelse
        </tbody><tfoot class="table-light"><tr><th colspan="5" class="text-end">Month Sales Total</th><th class="text-end">{{ number_format($selectedMonth['sales_taxable'], 2) }}</th><th class="text-end">{{ number_format($selectedMonth['sales_vat'], 2) }}</th><th class="text-end">{{ number_format($selectedMonth['sales_total'], 2) }}</th></tr></tfoot></table>
    </div></div></div><div class="mt-4 no-print">{{ $salesBills->links() }}</div>
</div></div>
@endsection
