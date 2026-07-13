@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header vat-index-header">
                <div>
                    <h4 class="mb-1"><i class="fa-solid fa-book-open"></i> VAT Party Ledgers</h4>
                    <small>Select a party to generate its ledger or confirmation letter.</small>
                </div>

                <form action="{{ route('vat-bills.index') }}" method="GET" class="vat-ledger-search">
                    <input type="search" name="search" value="{{ $search }}" class="form-control"
                        placeholder="Search party, VAT no, phone or firm">
                    <button type="submit" class="btn btn-light"><i class="fa-solid fa-search"></i> Search</button>
                    @if ($search !== '')
                        <a href="{{ route('vat-bills.index') }}" class="btn btn-outline-light">Clear</a>
                    @endif
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table vat-party-list mb-0">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th>Party</th>
                                <th>Party VAT No.</th>
                                <th>Phone</th>
                                <th>Our Firm</th>
                                <th>Bills</th>
                                <th>Taxable Total</th>
                                <th>Latest Bill</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($partyLedgers as $ledger)
                                <tr>
                                    <td>{{ $partyLedgers->firstItem() + $loop->index }}</td>
                                    <td>
                                        <strong>{{ $ledger->party_name }}</strong>
                                        <small>{{ $ledger->address ?: '-' }}</small>
                                    </td>
                                    <td><span class="vat-number">{{ $ledger->vat_no ?: '-' }}</span></td>
                                    <td>{{ $ledger->phoneno ?: '-' }}</td>
                                    <td>{{ $ledger->firm_type }}</td>
                                    <td><span class="bill-count">{{ $ledger->bill_count }}</span></td>
                                    <td class="amount">Rs {{ number_format((float) $ledger->taxable_total, 2) }}</td>
                                    <td>{{ $ledger->latest_bill_date }}</td>
                                    <td>
                                        <a href="{{ route('vat-bills.show', ['invoice' => $ledger->invoice_id, 'firm_type' => $ledger->firm_type]) }}"
                                            class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-book"></i> Open Ledger
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fa-solid fa-folder-open fa-2x text-muted mb-2"></i>
                                        <div>No confirmed VAT party ledgers found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($partyLedgers->hasPages())
                <div class="card-footer">{{ $partyLedgers->links() }}</div>
            @endif
        </div>
    </div>
</div>

<style>
.vat-index-header{align-items:center;background:linear-gradient(135deg,#064b19,#0f7b38)!important;color:#fff;display:flex;justify-content:space-between;padding:18px 22px!important}.vat-index-header h4{font-weight:900}.vat-index-header small{color:#dcfce7}.vat-ledger-search{display:flex;gap:8px;min-width:520px}.vat-ledger-search .form-control{min-width:290px}.vat-party-list tbody{display:table-row-group!important}.vat-party-list thead{display:table-header-group!important}.vat-party-list tr{display:table-row!important}.vat-party-list td{vertical-align:middle!important}.vat-party-list td strong,.vat-party-list td small{display:block}.vat-party-list td small{color:#64748b;margin-top:3px}.vat-number{background:#e0f2fe;border-radius:5px;color:#075985;font-weight:900;padding:5px 8px}.bill-count{align-items:center;background:#dcfce7;border-radius:50%;color:#166534;display:inline-flex;font-weight:900;height:34px;justify-content:center;width:34px}.vat-party-list .amount{font-weight:900;white-space:nowrap}@media(max-width:900px){.vat-index-header{align-items:stretch;flex-direction:column;gap:14px}.vat-ledger-search{min-width:0;width:100%}.vat-ledger-search .form-control{min-width:0}}
</style>
@stop
