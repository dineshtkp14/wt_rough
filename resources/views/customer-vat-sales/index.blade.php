@extends('layouts.master')

@section('content')
<div class="main-content">
<div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Customer VAT Sales</h2>
            <p class="text-muted mb-0">VAT sales created from stocked items.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('supplier-vat-bills.index') }}" class="btn btn-outline-secondary">Supplier VAT Records</a>
            <a href="{{ route('customer-vat-sales.monthly-book') }}" class="btn btn-warning btn-lg"><i class="fa fa-book me-2"></i>Monthly VAT Book</a>
            <a href="{{ route('customer-vat-sales.stock') }}" class="btn btn-success btn-lg"><i class="fa fa-boxes-stacked me-2"></i>VAT Stock</a>
            <a href="{{ route('customer-vat-sales.create') }}" class="btn btn-primary btn-lg"><i class="fa fa-plus me-2"></i>Create VAT Sale</a>
        </div>
    </div>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card mb-4">
        <div class="card-header">Search VAT Sales</div>
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Firm</label>
                    <select name="myfirm_id" class="form-select">
                        <option value="">All Firms</option>
                        @foreach ($firms as $firm)<option value="{{ $firm->id }}" @selected($firmId == $firm->id)>{{ $firm->firm_name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Customer or Bill No</label>
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Search customer or VAT bill">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1">Search</button>
                    <a href="{{ route('customer-vat-sales.index') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">VAT Sales Invoices</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Date</th><th>Firm</th><th>Bill No</th><th>Customer</th><th class="text-end">Taxable</th><th class="text-end">VAT</th><th class="text-end">Total</th><th>Action</th></tr></thead>
                    <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ \App\Support\NepaliDate::adToBsString($sale->bill_date, 'en') }} B.S.</td>
                            <td>{{ $sale->firm->firm_name }}</td>
                            <td>{{ $sale->bill_no }}</td>
                            <td>{{ $sale->customer_display_name }}</td>
                            <td class="text-end">{{ number_format((float) $sale->taxable_amount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $sale->vat_amount, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format((float) $sale->total_amount, 2) }}</td>
                            <td><div class="d-flex gap-1">
                                <a href="{{ route('customer-vat-sales.show', $sale) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('customer-vat-sales.edit', $sale) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fa fa-pen"></i></a>
                                <form method="post" action="{{ route('customer-vat-sales.destroy', $sale) }}" onsubmit="return confirm('Delete this VAT sale and restore its stock?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">No customer VAT sales found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">{{ $sales->links() }}</div>
</div>
</div>
@endsection
