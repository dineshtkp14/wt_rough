@extends('layouts.master')

@section('content')
<style>
    #vatStockTable, #vatMovementTable { table-layout: fixed !important; }
    #vatStockTable thead, #vatMovementTable thead { display: table-header-group !important; width: auto !important; }
    #vatStockTable tbody, #vatMovementTable tbody { display: table-row-group !important; width: auto !important; }
    #vatStockTable tr, #vatMovementTable tr { display: table-row !important; width: auto !important; }
    .vat-stock-summary { border-left: 5px solid #3348d4 !important; }
    .vat-stock-summary strong { color: #172033; display: block; font-size: 25px; margin-top: 5px; }
    .stock-status { border-radius: 999px; display: inline-block; font-size: 12px; font-weight: 900; padding: 5px 9px; text-transform: uppercase; }
    .stock-status.available { background: #dcfce7; color: #166534; }
    .stock-status.low { background: #fef3c7; color: #92400e; }
    .stock-status.out { background: #fee2e2; color: #991b1b; }
</style>
<div class="main-content"><div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h2 class="mb-1">Independent VAT Stock</h2><p class="text-muted mb-0">Used only by the VAT system. It is not linked to the project’s normal stock management.</p></div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('supplier-vat-bills.index') }}" class="btn btn-outline-secondary">VAT System</a>
            <a href="{{ route('customer-vat-sales.index') }}" class="btn btn-outline-primary">Customer VAT Sales</a>
            <a href="{{ route('customer-vat-sales.stock.create', ['myfirm_id' => $firmId]) }}" class="btn btn-success"><i class="fa fa-box me-1"></i>Add VAT Stock</a>
            <a href="{{ route('customer-vat-sales.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Create VAT Sale</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <div class="card mb-4"><div class="card-header">VAT Stock Filters</div><div class="card-body">
        <form method="get" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6"><label class="form-label">Firm</label><select name="myfirm_id" class="form-select" onchange="this.form.submit()">@foreach($firms as $option)<option value="{{ $option->id }}" @selected($firmId == $option->id)>{{ $option->firm_name }}</option>@endforeach</select></div>
            <div class="col-lg-4 col-md-6"><label class="form-label">Item Search</label><input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="VAT stock item name or ID"></div>
            <div class="col-lg-3 col-md-6"><label class="form-label">Stock Status</label><select name="status" class="form-select"><option value="">All Stock</option><option value="available" @selected($status === 'available')>Available</option><option value="low" @selected($status === 'low')>Low Stock</option><option value="out" @selected($status === 'out')>Out of Stock</option></select></div>
            <div class="col-lg-2 col-md-6 d-flex gap-2"><button class="btn btn-primary flex-grow-1">Filter</button><a href="{{ route('customer-vat-sales.stock', ['myfirm_id' => $firmId]) }}" class="btn btn-outline-secondary"><i class="fa fa-rotate-left"></i></a></div>
        </form>
    </div></div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card vat-stock-summary h-100"><div class="card-body"><span class="text-muted">VAT Stock Items</span><strong>{{ number_format((int) ($summary->item_count ?? 0)) }}</strong></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card vat-stock-summary h-100"><div class="card-body"><span class="text-muted">Total VAT Quantity</span><strong>{{ number_format((float) ($summary->stock_quantity ?? 0), 2) }}</strong></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card vat-stock-summary h-100"><div class="card-body"><span class="text-muted">Low Stock Items</span><strong class="text-warning">{{ number_format((int) ($summary->low_count ?? 0)) }}</strong></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card vat-stock-summary h-100"><div class="card-body"><span class="text-muted">Out of Stock</span><strong class="text-danger">{{ number_format((int) ($summary->out_count ?? 0)) }}</strong></div></div></div>
    </div>

    <div class="card mb-4"><div class="card-header"><span>{{ $firm->firm_name }} VAT Stock</span><small class="text-muted">Customer VAT sales reduce only this quantity</small></div><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="vatStockTable">
            <thead><tr><th>ID</th><th>VAT Stock Item</th><th>Unit</th><th class="text-end">Current Stock</th><th class="text-end">Warning At</th><th class="text-end">Sale Rate</th><th class="text-end">VAT Qty Sold</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($stockItems as $stockItem)
                @php $stockStatus = (float) $stockItem->quantity <= 0 ? 'out' : ((float) $stockItem->quantity <= (float) $stockItem->warning_quantity ? 'low' : 'available'); @endphp
                <tr>
                    <td>{{ $stockItem->id }}</td>
                    <td><strong>{{ $stockItem->item_name }}</strong>@if($stockItem->last_vat_sale_date)<small class="text-muted d-block">Last VAT sale: {{ \App\Support\NepaliDate::adToBsString($stockItem->last_vat_sale_date, 'en') }} B.S.</small>@endif</td>
                    <td>{{ $stockItem->unit ?: '-' }}</td>
                    <td class="text-end fw-bold">{{ number_format((float) $stockItem->quantity, 3) }}</td>
                    <td class="text-end">{{ number_format((float) $stockItem->warning_quantity, 3) }}</td>
                    <td class="text-end">{{ number_format((float) $stockItem->sale_rate, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $stockItem->vat_sold_quantity, 3) }}</td>
                    <td><span class="stock-status {{ $stockStatus }}">{{ $stockStatus === 'out' ? 'Out of Stock' : ($stockStatus === 'low' ? 'Low Stock' : 'Available') }}</span></td>
                    <td><div class="d-flex gap-1"><a href="{{ route('customer-vat-sales.stock.edit', $stockItem) }}" class="btn btn-sm btn-warning" title="Edit VAT stock"><i class="fa fa-pen"></i></a><form method="post" action="{{ route('customer-vat-sales.stock.destroy', $stockItem) }}" onsubmit="return confirm('Delete this VAT stock item?');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" title="Delete VAT stock"><i class="fa fa-trash"></i></button></form></div></td>
                </tr>
            @empty<tr><td colspan="9" class="text-center py-5 text-muted">No independent VAT stock items found. Use “Add VAT Stock” to create one.</td></tr>@endforelse
            </tbody>
        </table>
    </div></div></div>
    <div class="mb-4">{{ $stockItems->links() }}</div>

    <div class="card"><div class="card-header">Recent VAT Stock Movements</div><div class="card-body p-0"><div class="table-responsive">
        <table class="table align-middle mb-0" id="vatMovementTable">
            <thead><tr><th>Date (B.S.)</th><th>Bill No</th><th>Customer</th><th>VAT Stock Item</th><th class="text-end">Quantity Out</th><th class="text-end">Amount</th></tr></thead>
            <tbody>@forelse($recentMovements as $movement)<tr><td>{{ \App\Support\NepaliDate::adToBsString($movement->sale->bill_date, 'en') }}</td><td><a href="{{ route('customer-vat-sales.show', $movement->sale) }}">{{ $movement->sale->bill_no }}</a></td><td>{{ $movement->sale->customer_display_name }}</td><td>{{ $movement->item_name }}</td><td class="text-end text-danger">-{{ number_format((float) $movement->quantity, 3) }}</td><td class="text-end">{{ number_format((float) $movement->amount, 2) }}</td></tr>@empty<tr><td colspan="6" class="text-center py-4 text-muted">No VAT stock movements yet.</td></tr>@endforelse</tbody>
        </table>
    </div></div></div>
</div></div>
@endsection
