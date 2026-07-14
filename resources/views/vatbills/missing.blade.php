@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container-fluid py-3">
        <div class="card shadow-sm">
            <div class="card-header missing-header">
                <div>
                    <h4 class="mb-1"><i class="fa-solid fa-file-circle-exclamation"></i> Cash/Shop Invoices Missing VAT Bill</h4>
                    <small>Start typing to find an old invoice instantly.</small>
                </div>
                <form method="GET" action="{{ route('vat-bills.missing') }}" class="missing-search" id="missingVatSearchForm">
                    <input type="search" name="search" id="missingVatSearch" value="{{ $search }}" class="form-control"
                        placeholder="Invoice no, party, VAT no or phone" autocomplete="off">
                    <button class="btn btn-light" type="submit" id="missingVatSearchButton">Search</button>
                    <a href="{{ route('vat-bills.index') }}" class="btn btn-outline-light">Back to Ledgers</a>
                </form>
            </div>

            <div class="live-search-status" id="liveSearchStatus">{{ $invoices->total() }} invoice(s) found</div>

            <div class="card-body p-0 table-responsive">
                <table class="table missing-vat-table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice No.</th><th>Invoice Date</th><th>Type</th><th>Original Customer</th>
                            <th>VAT No.</th><th>Phone</th><th>Invoice Total</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="missingVatRows">@include('vatbills._missing_rows')</tbody>
                </table>
            </div>

            <div class="card-footer {{ $invoices->hasPages() ? '' : 'd-none' }}" id="missingVatPagination">
                @if ($invoices->hasPages()){{ $invoices->links() }}@endif
            </div>
        </div>
    </div>
</div>

<style>
.missing-header{align-items:center;background:linear-gradient(135deg,#9a5b00,#d97706)!important;color:#fff;display:flex;justify-content:space-between;padding:18px 22px!important}.missing-header small{color:#ffedd5}.missing-search{display:flex;gap:8px;min-width:600px}.missing-search .form-control{min-width:280px}.live-search-status{background:#fffbeb;border-bottom:1px solid #fde68a;color:#92400e;font-size:13px;font-weight:800;padding:7px 18px}.missing-vat-table tbody{display:table-row-group!important;transition:opacity .15s}.missing-vat-table thead{display:table-header-group!important}.missing-vat-table tr{display:table-row!important}.missing-vat-table td{vertical-align:middle!important}.missing-vat-table td small{color:#64748b;display:block;margin-top:3px}@media(max-width:900px){.missing-header{align-items:stretch;flex-direction:column;gap:12px}.missing-search{min-width:0}.missing-search .form-control{min-width:0}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('missingVatSearchForm');
    var input = document.getElementById('missingVatSearch');
    var button = document.getElementById('missingVatSearchButton');
    var rows = document.getElementById('missingVatRows');
    var pagination = document.getElementById('missingVatPagination');
    var status = document.getElementById('liveSearchStatus');
    var timer = null;
    var activeRequest = null;

    function searchUrl() {
        var url = new URL(form.action, window.location.origin);
        var value = input.value.trim();
        if (value) {
            url.searchParams.set('search', value);
        }
        return url.toString();
    }

    function loadResults(url) {
        if (activeRequest) activeRequest.abort();
        activeRequest = new AbortController();
        button.disabled = true;
        button.textContent = 'Searching...';
        status.textContent = 'Searching invoices...';
        rows.style.opacity = '0.45';

        fetch(url, {
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            signal: activeRequest.signal
        })
        .then(function (response) {
            if (!response.ok) throw new Error('Search failed');
            return response.json();
        })
        .then(function (data) {
            rows.innerHTML = data.rows;
            pagination.innerHTML = data.pagination;
            pagination.classList.toggle('d-none', !data.pagination);
            status.textContent = data.count + ' invoice(s) found';
            window.history.replaceState({}, '', url);
        })
        .catch(function (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'Unable to load results.';
                rows.innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Search failed. Please try again.</td></tr>';
            }
        })
        .finally(function () {
            button.disabled = false;
            button.textContent = 'Search';
            rows.style.opacity = '1';
        });
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { loadResults(searchUrl()); }, 300);
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearTimeout(timer);
        loadResults(searchUrl());
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
