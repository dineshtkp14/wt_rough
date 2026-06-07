@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
@php
    $hasCustomer = !empty($customerId) && $customer;
    $dateQuery = ['customerid' => $customerId, 'date1' => $from, 'date2' => $to];
    $proofQuery = array_merge($dateQuery, ['customer_invoice_numbers' => request('customer_invoice_numbers')]);
    $shareText = $hasCustomer
        ? 'Ledger proof for ' . ($customer->name ?? 'Customer') . ': System invoices ' . $invoiceNumbers->count() . ', missing from customer ' . $missingFromCustomer->count() . ', total due Rs ' . number_format((float) $totalDue, 2) . '.'
        : '';
@endphp

<div class="main-content dispute-page">
    @yield('breadcrumb')

    <div class="dispute-shell">
        <section class="dispute-panel">
            <div class="dispute-title">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Customer Ledger Dispute / Missing Bills
            </div>

            <form action="{{ route('customer.ledger.dispute') }}" method="get" id="disputeSearchForm">
                <div class="search-box dispute-search">
                    <input id="customerIdInput" name="customerid" value="{{ $customerId }}" hidden>
                    <input type="text"
                        class="search-input"
                        placeholder="Search Customer"
                        id="searchCustomerInput"
                        data-api="customer_search"
                        autocomplete="off"
                        value="{{ $customer ? $customer->name : '' }}">
                    <i class="fas fa-search search-icon"></i>

                    <div class="result-wrapper" id="customerResultWrapper" style="display: none;">
                        <div class="result-box d-flex justify-content-start align-items-center" id="customerLoadingResultBox">
                            <i class="fas fa-spinner" id="spinnerIcon"></i>
                            <h1 class="m-0 px-2">Loading</h1>
                        </div>
                        <div class="result-box d-flex justify-content-start align-items-center d-none" id="customerNotFoundResultBox">
                            <i class="fas fa-triangle-exclamation"></i>
                            <h1 class="m-0 px-2">Record Not Found</h1>
                        </div>
                        <div id="customerResultList"></div>
                    </div>
                </div>

                <div class="dispute-date-grid">
                    <div class="input-group">
                        <span class="input-group-text">Start Date</span>
                        <input type="date" name="date1" value="{{ $from }}" class="form-control">
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">End Date</span>
                        <input type="date" name="date2" value="{{ $to }}" class="form-control">
                    </div>
                </div>

                <label class="dispute-label" for="customerInvoiceNumbers">Invoice numbers customer brought</label>
                <textarea id="customerInvoiceNumbers" name="customer_invoice_numbers" placeholder="Example: 12662, 12663, 12664">{{ request('customer_invoice_numbers') }}</textarea>
                <small>You can paste numbers separated by comma, space, or new line.</small>

                <div class="dispute-actions">
                    <button type="submit" class="dispute-btn primary">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Check Missing Bills
                    </button>
                    @if($hasCustomer)
                        <a href="{{ route('customer.ledger.dispute.pdf', $proofQuery) }}"
                            onclick="openPdfInNewTab(event, this.href); return false;"
                            class="dispute-btn print">
                            <i class="fa-solid fa-print"></i>
                            Print Proof
                        </a>
                        @if($missingFromCustomer->isNotEmpty())
                            <a href="{{ route('customer.ledger.dispute.missing-invoices.pdf', $proofQuery) }}"
                                onclick="openPdfInNewTab(event, this.href); return false;"
                                class="dispute-btn missing-print">
                                <i class="fa-solid fa-file-invoice"></i>
                                Print Missing Invoices
                            </a>
                        @endif
                        <a href="https://wa.me/?text={{ rawurlencode($shareText) }}" target="_blank" class="dispute-btn share">
                            <i class="fa-brands fa-whatsapp"></i>
                            Share Summary
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <section class="dispute-panel summary-panel">
            @if($hasCustomer)
                <div class="customer-name">{{ $customer->name }}</div>
                <div class="customer-meta">{{ $customer->address }} | {{ $customer->phoneno }}</div>
                <div class="summary-grid">
                    <div><span>System Invoices</span><strong>{{ $invoiceNumbers->count() }}</strong></div>
                    <div><span>Customer Brought</span><strong>{{ $customerNumbers->count() }}</strong></div>
                    <div><span>Missing From Customer</span><strong>{{ $missingFromCustomer->count() }}</strong></div>
                    <div><span>Total Due</span><strong>Rs {{ number_format((float) $totalDue, 2) }}</strong></div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-user-magnifying-glass"></i>
                    Search and select a customer to check dispute proof.
                </div>
            @endif
        </section>
    </div>

    @if($hasCustomer)
        <div class="dispute-results">
            <section class="dispute-panel">
                <h4>Missing From Customer</h4>
                @if($customerNumbers->isEmpty())
                    <p class="hint">Enter the invoice numbers customer brought, then click check.</p>
                @elseif($missingFromCustomer->isEmpty())
                    <div class="ok-box"><i class="fa-solid fa-circle-check"></i> Customer brought all system invoice numbers for this range.</div>
                @else
                    <div class="pill-list">
                        @foreach($missingFromCustomer as $number)
                            <span>{{ $number }}</span>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="dispute-panel">
                <h4>Customer Gave But Not In System</h4>
                @if($notInSystem->isEmpty())
                    <p class="hint">No extra invoice numbers found.</p>
                @else
                    <div class="pill-list warning">
                        @foreach($notInSystem as $number)
                            <span>{{ $number }}</span>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>

        <section class="dispute-panel invoice-panel">
            <h4>System Invoice List</h4>
            <div class="table-responsive">
                <table class="table dispute-table">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th class="text-end">Total</th>
                            <th>Customer Has?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            @php $customerHas = $customerNumbers->contains((int) $invoice->id); @endphp
                            <tr class="invoice-select-row {{ $customerHas ? 'selected-row' : '' }} {{ $customerNumbers->isNotEmpty() && !$customerHas ? 'missing-row' : '' }}"
                                data-invoice-no="{{ $invoice->id }}"
                                title="Click to add/remove this invoice number">
                                <td><strong>{{ $invoice->id }}</strong></td>
                                <td>{{ $invoice->inv_date }}</td>
                                <td>{{ strtoupper($invoice->inv_type ?? '-') }}</td>
                                <td class="text-end">Rs {{ number_format((float) $invoice->total, 2) }}</td>
                                <td>{{ $customerNumbers->isEmpty() ? '-' : ($customerHas ? 'Yes' : 'Missing') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">No invoices found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>

<script>
    function openPdfInNewTab(event, url) {
        event.preventDefault();
        var tab = window.open(url, '_blank');
        if (tab) tab.focus();
    }

    $(document).on('customer:selected', function () {
        const form = document.getElementById('disputeSearchForm');
        const customerInput = document.getElementById('customerIdInput');
        if (form && customerInput && customerInput.value) form.submit();
    });

    function currentInvoiceNumbers() {
        const input = document.getElementById('customerInvoiceNumbers');
        return Array.from(new Set((input.value || '')
            .split(/[^0-9]+/)
            .map(function (value) { return parseInt(value, 10); })
            .filter(function (value) { return value > 0; })));
    }

    function writeInvoiceNumbers(numbers) {
        document.getElementById('customerInvoiceNumbers').value = numbers.sort(function (a, b) {
            return a - b;
        }).join(', ');
    }

    document.querySelectorAll('.invoice-select-row').forEach(function (row) {
        row.addEventListener('click', function () {
            const invoiceNo = parseInt(row.dataset.invoiceNo, 10);
            let numbers = currentInvoiceNumbers();

            if (numbers.includes(invoiceNo)) {
                numbers = numbers.filter(function (number) { return number !== invoiceNo; });
                row.classList.remove('selected-row');
            } else {
                numbers.push(invoiceNo);
                row.classList.add('selected-row');
            }

            writeInvoiceNumbers(numbers);
        });
    });
</script>

<style>
    .dispute-page { background: #eef2f7; min-height: 100vh; padding: 18px; }
    .dispute-shell { display: grid; gap: 16px; grid-template-columns: minmax(360px, 1.1fr) minmax(320px, .9fr); }
    .dispute-panel { background: #fff; border: 1px solid #d7dee8; border-radius: 8px; padding: 18px; }
    .dispute-title { color: #111827; font-size: 20px; font-weight: 900; margin-bottom: 14px; text-transform: uppercase; }
    .dispute-title i { color: #dc2626; margin-right: 8px; }
    .dispute-search .search-input { height: 58px; font-size: 22px; }
    .dispute-date-grid { display: grid; gap: 12px; grid-template-columns: 1fr 1fr; margin: 14px 0; }
    .dispute-label { color: #334155; display: block; font-size: 14px; font-weight: 900; margin-bottom: 6px; text-transform: uppercase; }
    #customerInvoiceNumbers { border: 1px solid #cbd5e1; border-radius: 8px; min-height: 96px; padding: 12px; width: 100%; }
    .dispute-panel small, .hint { color: #64748b; font-weight: 700; }
    .dispute-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
    .dispute-btn { align-items: center; border: 0; border-radius: 6px; color: #fff !important; display: inline-flex; font-weight: 900; gap: 8px; min-height: 44px; padding: 0 14px; text-decoration: none !important; }
    .dispute-btn.primary { background: #1f2937; }
    .dispute-btn.print { background: #4f46e5; }
    .dispute-btn.missing-print { background: #dc2626; }
    .dispute-btn.share { background: #16a34a; }
    .customer-name { color: #111827; font-size: 30px; font-weight: 900; }
    .customer-meta { color: #475569; font-size: 18px; font-weight: 800; margin-bottom: 14px; }
    .summary-grid { display: grid; gap: 10px; grid-template-columns: 1fr 1fr; }
    .summary-grid div { background: #f8fafc; border: 1px solid #dbe3ef; border-radius: 8px; padding: 12px; }
    .summary-grid span { color: #64748b; display: block; font-size: 12px; font-weight: 900; text-transform: uppercase; }
    .summary-grid strong { color: #0f172a; display: block; font-size: 22px; margin-top: 3px; }
    .empty-state { color: #64748b; font-size: 18px; font-weight: 800; text-align: center; }
    .empty-state i { display: block; font-size: 36px; margin-bottom: 8px; }
    .dispute-results { display: grid; gap: 16px; grid-template-columns: repeat(2, 1fr); margin-top: 16px; }
    .dispute-panel h4 { color: #111827; font-size: 18px; font-weight: 900; margin: 0 0 12px; }
    .pill-list { display: flex; flex-wrap: wrap; gap: 8px; }
    .pill-list span { background: #fee2e2; border: 1px solid #fca5a5; border-radius: 999px; color: #991b1b; font-weight: 900; padding: 6px 10px; }
    .pill-list.warning span { background: #fffbeb; border-color: #fbbf24; color: #92400e; }
    .ok-box { background: #ecfdf5; border: 1px solid #86efac; border-radius: 8px; color: #166534; font-weight: 900; padding: 12px; }
    .invoice-panel { margin-top: 16px; }
    .dispute-table th { background: #3348d4 !important; color: #fff !important; }
    .dispute-table td, .dispute-table th { font-size: 15px; font-weight: 800; white-space: nowrap; }
    .invoice-select-row { cursor: pointer; }
    .invoice-select-row:hover td { background: #e0f2fe !important; }
    .dispute-table .selected-row td { background: #dcfce7 !important; color: #166534 !important; }
    .dispute-table .missing-row td { background: #fee2e2 !important; color: #991b1b !important; }
    @media (max-width: 1100px) { .dispute-shell, .dispute-results { grid-template-columns: 1fr; } }
    @media (max-width: 700px) { .dispute-date-grid, .summary-grid { grid-template-columns: 1fr; } .dispute-btn { justify-content: center; width: 100%; } }
</style>
@stop
