@extends('layouts.master')

@section('content')
<style>
    #customerVatItemsTable { table-layout: fixed !important; }
    #customerVatItemsTable thead { display: table-header-group !important; width: auto !important; }
    #customerVatItemsTable tbody { display: table-row-group !important; width: auto !important; }
    #customerVatItemsTable tfoot { display: table-footer-group !important; width: auto !important; }
    #customerVatItemsTable tr { display: table-row !important; width: auto !important; }
    #customerVatItemsTable tfoot th { background: #f8fafc; border-bottom: 1px solid #d5deea; padding: 12px 14px; }
    #customerVatItemsTable tfoot tr:last-child th { background: #eef2ff; font-size: 19px; }
    @media print {
        .side-nav, .sidebar-collapse-btn, .no-print { display: none !important; }
        .main-content { padding: 0 !important; width: 100% !important; }
        .main-content > div { padding: 0 !important; }
        body { background: #fff !important; display: block !important; }
        .card { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
    }
</style>
<div class="main-content"><div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h2 class="mb-1">Customer VAT Bill #{{ $sale->bill_no }}</h2><p class="text-muted mb-0">{{ $sale->customer_display_name }}</p></div>
        <div class="d-flex flex-wrap gap-2 no-print">
            <a href="{{ route('customer-vat-sales.index') }}" class="btn btn-outline-secondary">VAT Sales List</a>
            <a href="{{ route('customer-vat-sales.edit', $sale) }}" class="btn btn-warning"><i class="fa fa-pen me-1"></i>Edit</a>
            <form method="post" action="{{ route('customer-vat-sales.destroy', $sale) }}" onsubmit="return confirm('Delete this VAT sale and restore its stock?');">@csrf @method('DELETE')<button class="btn btn-danger"><i class="fa fa-trash me-1"></i>Delete</button></form>
            <button type="button" class="btn btn-success" onclick="window.print()"><i class="fa fa-print me-1"></i>Print VAT Bill</button>
            <a href="{{ route('customer-vat-sales.create') }}" class="btn btn-primary">Create Another</a>
        </div>
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card mb-4"><div class="card-header">VAT Bill Information</div><div class="card-body"><div class="row g-3">
        <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Firm</span><strong>{{ $sale->firm->firm_name }}</strong></div>
        <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Firm VAT No</span><strong>{{ $firmVatNo }}</strong></div>
        <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Customer</span><strong>{{ $sale->customer_display_name }}</strong></div>
        <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Bill No</span><strong>{{ $sale->bill_no }}</strong></div>
        <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Date</span><strong>{{ \App\Support\NepaliDate::adToBsString($sale->bill_date, 'en') }} B.S. / {{ $sale->bill_date->format('Y-m-d') }} A.D.</strong></div>
        <div class="col-lg-3 col-md-6"><span class="text-muted d-block">VAT/PAN</span><strong>{{ $sale->customer_display_vat_no ?: '-' }}</strong></div>
        <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Address</span><strong>{{ $sale->customer?->address ?: '-' }}</strong></div>
        @if ($sale->notes)<div class="col-lg-6"><span class="text-muted d-block">Notes</span>{{ $sale->notes }}</div>@endif
    </div></div></div>

    <div class="card"><div class="card-header">Stock Items Sold</div><div class="card-body p-0"><div class="table-responsive">
        <table class="table align-middle mb-0" id="customerVatItemsTable">
            <colgroup><col style="width:6%"><col style="width:38%"><col style="width:14%"><col style="width:12%"><col style="width:15%"><col style="width:15%"></colgroup>
            <thead><tr><th>#</th><th>Item</th><th class="text-end">Quantity</th><th>Unit</th><th class="text-end">Rate</th><th class="text-end">Amount</th></tr></thead>
            <tbody>@foreach ($sale->items as $line)<tr><td>{{ $loop->iteration }}</td><td>{{ $line->item_name }}</td><td class="text-end">{{ rtrim(rtrim(number_format((float) $line->quantity, 3, '.', ''), '0'), '.') }}</td><td>{{ $line->unit }}</td><td class="text-end">{{ number_format((float) $line->rate, 2) }}</td><td class="text-end">{{ number_format((float) $line->amount, 2) }}</td></tr>@endforeach</tbody>
            <tfoot><tr><th colspan="5" class="text-end">Taxable Amount</th><th class="text-end">{{ number_format((float) $sale->taxable_amount, 2) }}</th></tr><tr><th colspan="5" class="text-end">VAT (13%)</th><th class="text-end">{{ number_format((float) $sale->vat_amount, 2) }}</th></tr><tr><th colspan="5" class="text-end">Grand Total</th><th class="text-end">{{ number_format((float) $sale->total_amount, 2) }}</th></tr></tfoot>
        </table>
    </div></div></div>
</div></div>
@endsection
