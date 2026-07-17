@extends('layouts.master')

@section('content')
<style>
    .supplier-ledger-search,
    .supplier-ledger-search .card-body {
        overflow: visible !important;
    }

    .supplier-ledger-search {
        position: relative;
        z-index: 10;
    }

    .ledger-summary-card {
        border-left: 5px solid #3348d4 !important;
    }

    .ledger-summary-card .summary-value {
        color: #172033;
        font-size: 24px;
        font-weight: 900;
    }
</style>
<div class="main-content">
<div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Supplier VAT System</h2>
            <p class="text-muted mb-0">Purchase VAT bills entered from your suppliers.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('customer-vat-sales.monthly-book') }}" class="btn btn-outline-warning btn-lg"><i class="fa fa-book me-2"></i>Monthly VAT Book</a>
            <a href="{{ route('customer-vat-sales.stock') }}" class="btn btn-outline-success btn-lg"><i class="fa fa-boxes-stacked me-2"></i>VAT Stock</a>
            <a href="{{ route('customer-vat-sales.index') }}" class="btn btn-success btn-lg"><i class="fa fa-cash-register me-2"></i>Customer VAT Sales</a>
            <a href="{{ route('customer-vat-sales.create') }}" class="btn btn-warning btn-lg"><i class="fa fa-plus me-2"></i>Create Customer VAT Bill</a>
            <a href="{{ route('supplier-vat-bills.create') }}" class="btn btn-primary btn-lg"><i class="fa fa-plus me-2"></i>Create Supplier VAT Bill</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card supplier-ledger-search mb-4">
        <div class="card-header">Search Supplier VAT Ledger</div>
        <div class="card-body">
            <form method="get" action="{{ route('supplier-vat-bills.index') }}" id="supplierLedgerSearchForm">
                <div class="row g-3 align-items-end">
                    <div class="col-xl-3 col-md-6">
                        <label for="firm_type" class="form-label">Our Firm</label>
                        <select name="firm_type" id="firm_type" class="form-select">
                            <option value="">All Firms</option>
                            @foreach ($firms as $firm)
                                <option value="{{ $firm }}" @selected(old('firm_type', $firmType) === $firm)>{{ $firm }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <label for="searchCustomerInput" class="form-label">Supplier Name</label>
                        <div class="search-box w-100">
                            <input type="hidden" name="company_id" id="customerIdInput" value="{{ old('company_id', $selectedCompany?->id) }}">
                            <input type="text"
                                name="supplier_name"
                                id="searchCustomerInput"
                                class="search-input form-control"
                                value="{{ old('supplier_name', $selectedCompany?->name) }}"
                                data-api="company_search"
                                placeholder="Type and select supplier"
                                autocomplete="off">
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
                    </div>
                    <div class="col-xl-2 col-md-6">
                        <label for="bill_no" class="form-label">Bill / Invoice No</label>
                        <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no', $billNo) }}" class="form-control" placeholder="All bills">
                    </div>
                    <div class="col-xl-2 col-md-4">
                        <label for="from_date_bs" class="form-label">From Date (B.S.)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i></span>
                            <input type="text" name="from_date_bs" id="from_date_bs"
                                value="{{ old('from_date_bs', $fromDateBs) }}" class="form-control"
                                placeholder="YYYY-MM-DD" inputmode="numeric"
                                pattern="\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}"
                                title="Enter B.S. date as YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4">
                        <label for="to_date_bs" class="form-label">To Date (B.S.)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-calendar-check"></i></span>
                            <input type="text" name="to_date_bs" id="to_date_bs"
                                value="{{ old('to_date_bs', $toDateBs) }}" class="form-control"
                                placeholder="YYYY-MM-DD" inputmode="numeric"
                                pattern="\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}"
                                title="Enter B.S. date as YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fa fa-search me-1"></i>Show Ledger</button>
                        <a href="{{ route('supplier-vat-bills.index') }}" class="btn btn-outline-secondary" title="Clear filters"><i class="fa fa-rotate-left"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedCompany)
        <div class="card mb-4">
            <div class="card-header">Supplier Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Supplier Name</span><strong>{{ $selectedCompany->name }}</strong></div>
                    <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Address</span><strong>{{ $selectedCompany->address ?: '-' }}</strong></div>
                    <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Phone</span><strong>{{ $selectedCompany->phoneno ?: '-' }}</strong></div>
                    <div class="col-lg-3 col-md-6"><span class="text-muted d-block">Email</span><strong>{{ $selectedCompany->email ?: '-' }}</strong></div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card ledger-summary-card h-100"><div class="card-body"><span class="text-muted d-block">Bills Found</span><span class="summary-value">{{ number_format((int) $ledgerSummary->bill_count) }}</span></div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card ledger-summary-card h-100"><div class="card-body"><span class="text-muted d-block">Taxable Amount</span><span class="summary-value">{{ number_format((float) $ledgerSummary->taxable_total, 2) }}</span></div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card ledger-summary-card h-100"><div class="card-body"><span class="text-muted d-block">VAT Amount</span><span class="summary-value">{{ number_format((float) $ledgerSummary->vat_total, 2) }}</span></div></div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card ledger-summary-card h-100"><div class="card-body"><span class="text-muted d-block">Grand Total</span><span class="summary-value">{{ number_format((float) $ledgerSummary->grand_total, 2) }}</span></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span>
                {{ $selectedCompany ? $selectedCompany->name.' - VAT Invoices' : 'Supplier VAT Invoices' }}
                @if ($firmType) — {{ $firmType }} @endif
            </span>
            @if ($fromDateBs || $toDateBs)
                <span class="text-muted small">B.S. {{ $fromDateBs ?: 'Beginning' }} to {{ $toDateBs ?: 'Today' }}</span>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Date (B.S.)</th>
                            <th>Our Firm</th>
                            <th>Supplier</th>
                            <th>Bill No</th>
                            <th class="text-end">Taxable</th>
                            <th class="text-end">VAT 13%</th>
                            <th class="text-end">Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bills as $bill)
                            <tr>
                                <td>{{ \App\Support\NepaliDate::adToBsString($bill->bill_date, 'en') }}</td>
                                <td>{{ $bill->firm_type ?: 'Unassigned' }}</td>
                                <td>{{ $bill->company->name }}</td>
                                <td>{{ $bill->bill_no }}</td>
                                <td class="text-end">{{ number_format((float) $bill->taxable_amount, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $bill->vat_amount, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format((float) $bill->total_amount, 2) }}</td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('supplier-vat-bills.show', $bill) }}" class="btn btn-sm btn-outline-primary" title="View bill"><i class="fa fa-eye"></i></a>
                                        <a href="{{ route('supplier-vat-bills.edit', $bill) }}" class="btn btn-sm btn-outline-warning" title="Edit bill"><i class="fa fa-pen"></i></a>
                                        <form action="{{ route('supplier-vat-bills.destroy', $bill) }}" method="post" onsubmit="return confirm('Delete this supplier VAT bill? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete bill"><i class="fa fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No supplier VAT bills found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $bills->links() }}</div>
</div>
</div>
@endsection
