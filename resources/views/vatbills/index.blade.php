@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container-fluid py-3">
        @if (Session::has('vat_success'))
            <div class="alert alert-success fw-bold">{{ Session::get('vat_success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header vat-index-header">
                <div>
                    <h4 class="mb-1"><i class="fa-solid fa-book-open"></i> VAT Party Ledgers</h4>
                    <small>Select a party to generate its ledger or confirmation letter.</small>
                </div>

                <form action="{{ route('vat-bills.index') }}" method="GET" class="vat-ledger-search" id="vatLedgerSearchForm">
                    <input type="search" name="search" id="vatLedgerSearch" value="{{ $search }}" class="form-control"
                        placeholder="Search party, VAT no, phone or firm" autocomplete="off">
                    <a href="{{ route('vat-bills.index') }}" id="vatLedgerSearchClear"
                        class="btn btn-outline-light {{ $search === '' ? 'd-none' : '' }}">Clear</a>
                </form>
            </div>

            <div class="live-search-status" id="vatLedgerSearchStatus">{{ $partyLedgers->total() }} party ledger(s) found</div>

            <div class="bulk-print-bar">
                <span><i class="fa-solid fa-print"></i> Bulk Printing</span>
                <a href="{{ route('vat-bills.standalone.create') }}" class="btn btn-success"><i class="fa-solid fa-plus"></i> Create VAT Bill</a>
                <a href="{{ route('vat-bills.print-all', ['search' => $search]) }}" target="_blank" class="btn btn-primary" id="printAllPartyLedgers">
                    <i class="fa-solid fa-book-open"></i> Print All Party Ledgers
                </a>
                <a href="{{ route('vat-bills.print-all-confirmations', ['search' => $search]) }}" target="_blank" class="btn btn-success" id="printAllConfirmationLetters">
                    <i class="fa-solid fa-envelope-open-text"></i> Print All Confirmation Letters
                </a>
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
                                <th>Latest Bill (B.S.)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="vatLedgerRows">@include('vatbills._party_ledger_rows')</tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer {{ $partyLedgers->hasPages() ? '' : 'd-none' }}" id="vatLedgerPagination">
                @if ($partyLedgers->hasPages()){{ $partyLedgers->links() }}@endif
            </div>
        </div>
    </div>
</div>

<style>
.vat-index-header{align-items:center;background:linear-gradient(135deg,#064b19,#0f7b38)!important;color:#fff;display:flex;justify-content:space-between;padding:18px 22px!important}.vat-index-header h4{font-weight:900}.vat-index-header small{color:#dcfce7}.vat-ledger-search{display:flex;gap:8px;min-width:520px}.vat-ledger-search .form-control{min-width:290px}.live-search-status{background:#f0fdf4;border-bottom:1px solid #bbf7d0;color:#166534;font-size:13px;font-weight:800;padding:7px 18px}.bulk-print-bar{align-items:center;background:#f0f7ff;border-bottom:1px solid #c9d8e7;display:flex;gap:10px;justify-content:flex-end;padding:11px 18px}.bulk-print-bar>span{color:#475569;font-weight:900;margin-right:auto}.vat-party-list tbody{display:table-row-group!important;transition:opacity .15s}.vat-party-list thead{display:table-header-group!important}.vat-party-list tr{display:table-row!important}.vat-party-list td{vertical-align:middle!important}.vat-party-list td strong,.vat-party-list td small{display:block}.vat-party-list td small{color:#64748b;margin-top:3px}.vat-number{background:#e0f2fe;border-radius:5px;color:#075985;font-weight:900;padding:5px 8px}.bill-count{align-items:center;background:#dcfce7;border-radius:50%;color:#166534;display:inline-flex;font-weight:900;height:34px;justify-content:center;width:34px}.vat-party-list .amount{font-weight:900;white-space:nowrap}@media(max-width:900px){.vat-index-header{align-items:stretch;flex-direction:column;gap:14px}.vat-ledger-search{min-width:0;width:100%}.vat-ledger-search .form-control{min-width:0}.bulk-print-bar{align-items:stretch;flex-direction:column}.bulk-print-bar>span{margin:0}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('vatLedgerSearchForm');
    var input = document.getElementById('vatLedgerSearch');
    var clear = document.getElementById('vatLedgerSearchClear');
    var rows = document.getElementById('vatLedgerRows');
    var pagination = document.getElementById('vatLedgerPagination');
    var status = document.getElementById('vatLedgerSearchStatus');
    var printLedgers = document.getElementById('printAllPartyLedgers');
    var printConfirmations = document.getElementById('printAllConfirmationLetters');
    var timer = null;
    var activeRequest = null;

    function resultUrl() {
        var url = new URL(form.action, window.location.origin);
        var value = input.value.trim();

        if (value) {
            url.searchParams.set('search', value);
        }

        return url.toString();
    }

    function updatePrintUrl(link, search) {
        var url = new URL(link.href, window.location.origin);
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        link.href = url.toString();
    }

    function loadResults(url) {
        if (activeRequest) activeRequest.abort();

        var request = new AbortController();
        activeRequest = request;
        status.textContent = 'Searching party ledgers...';
        rows.style.opacity = '0.45';

        fetch(url, {
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            signal: request.signal
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Search failed');
            return response.json();
        })
        .then(function (data) {
            var search = input.value.trim();
            rows.innerHTML = data.rows;
            pagination.innerHTML = data.pagination;
            pagination.classList.toggle('d-none', !data.pagination);
            clear.classList.toggle('d-none', search === '');
            status.textContent = data.count + ' party ledger(s) found';
            updatePrintUrl(printLedgers, search);
            updatePrintUrl(printConfirmations, search);
            window.history.replaceState({}, '', url);
        })
        .catch(function (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'Unable to load party ledgers.';
                rows.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Search failed. Please try again.</td></tr>';
            }
        })
        .finally(function () {
            if (activeRequest !== request) return;
            activeRequest = null;
            rows.style.opacity = '1';
        });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { loadResults(resultUrl()); }, 300);
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearTimeout(timer);
        loadResults(resultUrl());
    });

    clear.addEventListener('click', function (event) {
        event.preventDefault();
        clearTimeout(timer);
        input.value = '';
        loadResults(resultUrl());
        input.focus();
    });

    pagination.addEventListener('click', function (event) {
        var link = event.target.closest('a');
        if (!link) return;
        event.preventDefault();
        loadResults(link.href);
    });
});
</script>
@stop
