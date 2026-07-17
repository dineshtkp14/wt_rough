@extends('layouts.master')

@section('content')
<style>
    #billItemsTable {
        table-layout: fixed !important;
    }

    #billItemsTable thead {
        display: table-header-group !important;
        width: auto !important;
    }

    #billItemsTable tbody {
        display: table-row-group !important;
        width: auto !important;
    }

    #billItemsTable tfoot {
        display: table-footer-group !important;
        width: auto !important;
    }

    #billItemsTable tr {
        display: table-row !important;
        width: auto !important;
    }

    #billItemsTable th,
    #billItemsTable td {
        box-sizing: border-box;
    }

    #billItemsTable tfoot th {
        border-bottom: 1px solid #d5deea;
        padding: 12px 14px;
        background: #f8fafc;
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
        vertical-align: middle;
    }

    #billItemsTable tfoot tr:last-child th {
        background: #eef2ff;
        border-bottom: 0;
        font-size: 19px;
    }

    #billItemsTable tfoot th:last-child {
        border-left: 1px solid #d5deea;
        white-space: nowrap;
    }
</style>
<div class="main-content">
<div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Supplier VAT Bill #{{ $bill->bill_no }}</h2>
            <p class="text-muted mb-0">{{ $bill->company->name }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('supplier-vat-bills.index') }}" class="btn btn-outline-secondary">VAT Bill List</a>
            <a href="{{ route('supplier-vat-bills.edit', $bill) }}" class="btn btn-warning"><i class="fa fa-pen me-1"></i>Edit Bill</a>
            <form action="{{ route('supplier-vat-bills.destroy', $bill) }}" method="post" onsubmit="return confirm('Delete this supplier VAT bill? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fa fa-trash me-1"></i>Delete Bill</button>
            </form>
            <a href="{{ route('supplier-vat-bills.create') }}" class="btn btn-primary">Create Another</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Bill Information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Our Firm</span><strong>{{ $bill->firm_type ?: 'Unassigned - please edit' }}</strong></div>
                <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Supplier</span><strong>{{ $bill->company->name }}</strong></div>
                <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Bill No</span><strong>{{ $bill->bill_no }}</strong></div>
                <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Date</span><strong>{{ $bill->bill_date->format('Y-m-d') }}</strong></div>
                @if ($bill->company->address)
                    <div class="col-md-4"><span class="text-muted d-block">Address</span><strong>{{ $bill->company->address }}</strong></div>
                @endif
                @if ($bill->notes)
                    <div class="col-md-8"><span class="text-muted d-block">Notes</span>{{ $bill->notes }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Items</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" id="billItemsTable">
                    <colgroup>
                        <col style="width: 6%">
                        <col style="width: 34%">
                        <col style="width: 14%">
                        <col style="width: 14%">
                        <col style="width: 16%">
                        <col style="width: 16%">
                    </colgroup>
                    <thead>
                        <tr><th>#</th><th>Item</th><th class="text-end">Quantity</th><th>Unit</th><th class="text-end">Rate</th><th class="text-end">Amount</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($bill->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td class="text-end">{{ rtrim(rtrim(number_format((float) $item->quantity, 3, '.', ''), '0'), '.') }}</td>
                                <td>{{ $item->unit }}</td>
                                <td class="text-end">{{ number_format((float) $item->rate, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th colspan="5" class="text-end">Taxable Amount</th><th class="text-end">{{ number_format((float) $bill->taxable_amount, 2) }}</th></tr>
                        <tr><th colspan="5" class="text-end">VAT (13%)</th><th class="text-end">{{ number_format((float) $bill->vat_amount, 2) }}</th></tr>
                        <tr class="fs-5"><th colspan="5" class="text-end">Grand Total</th><th class="text-end">{{ number_format((float) $bill->total_amount, 2) }}</th></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
