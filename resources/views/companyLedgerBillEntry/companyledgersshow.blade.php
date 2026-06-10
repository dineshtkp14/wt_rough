@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
@php
    $company = $allcus->first();
    $totalDue = (float) $dts - (float) $cts;
    $hasRows = $all !== null && count($all) > 0;
    $hasCompany = !empty($companyid) && $company;

    $amountToWords = function ($num) use (&$amountToWords) {
        $num = (int) floor($num);
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($num < 0) return 'Minus ' . $amountToWords(abs($num));
        if ($num === 0) return 'Zero';

        $words = '';
        if ($num >= 10000000) { $words .= $amountToWords(floor($num / 10000000)) . ' Crore '; $num %= 10000000; }
        if ($num >= 100000) { $words .= $amountToWords(floor($num / 100000)) . ' Lakh '; $num %= 100000; }
        if ($num >= 1000) { $words .= $amountToWords(floor($num / 1000)) . ' Thousand '; $num %= 1000; }
        if ($num >= 100) { $words .= $amountToWords(floor($num / 100)) . ' Hundred '; $num %= 100; }
        if ($num >= 20) { $words .= $tens[floor($num / 10)] . ' '; $num %= 10; }
        if ($num > 0) { $words .= $ones[$num] . ' '; }

        return trim($words);
    };
@endphp

<div class="main-content company-ledger-page">
    @yield('breadcrumb')

    <div class="container-fluid px-3 px-xl-4">
        <div class="card customer-card mb-4" id="customerCard" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Company Info</h5>
                <p><span>ID:</span> <span id="customerId">...</span></p>
                <p class="card-text"><span>Name:</span> <span id="customerName">...</span></p>
                <p><span>Address:</span> <span id="customerAddress">...</span></p>
                <p><span>E-mail:</span> <span id="customerEmail">...</span></p>
                <p><span>PhoneNo:</span> <span id="customerPhone">...</span></p>
            </div>
            <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                <i class="fas fa-building"></i>
            </div>
        </div>

        <div class="clhs-top-grid {{ !$hasCompany ? 'only-search' : '' }}">
            <section class="clhs-panel clhs-search-panel">
                <div class="clhs-section-title">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Find Company Ledger
                </div>

                <form action="{{ route('companyledgerdetails.returnchoosendatehistroy') }}" method="get" id="chosendatepdfform">
                    <div class="search-box clhs-customer-search">
                        <input id="customerIdInput" name="companyid" value="{{ $companyid }}" hidden>
                        <input type="text"
                            required
                            class="search-input @error('companyid') is-invalid @enderror"
                            placeholder="Search Company"
                            id="searchCustomerInput"
                            data-api="company_search"
                            autocomplete="off">
                        @error('companyid')
                            <p class="invalid-feedback m-0">{{ $message }}</p>
                        @enderror
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

                    <div class="clhs-date-grid">
                        <div class="input-group">
                            <span class="input-group-text">Start Date</span>
                            <input type="date" name="date1" value="{{ $from }}" class="form-control">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">End Date</span>
                            <input type="date" name="date2" value="{{ $to }}" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="clhs-search-btn">
                        <i class="fas fa-search"></i>
                        Search Ledger
                    </button>
                </form>
            </section>

            @if($hasCompany)
                <section class="clhs-panel clhs-summary-panel">
                    <div class="clhs-summary-head">
                        <div>
                            <div class="clhs-section-title">
                                <i class="fa-solid fa-building"></i>
                                Company Summary
                            </div>
                            <h3>{{ $company->name ?? 'Select a company' }}</h3>
                            <p>{{ $company->address ?? 'No address selected' }}</p>
                        </div>
                    </div>

                    <div class="clhs-customer-meta">
                        <div><span>Company ID</span><b>{{ $company->id ?? '-' }}</b></div>
                        <div><span>Phone</span><b>{{ $company->phoneno ?? '-' }}</b></div>
                        <div><span>Email</span><b>{{ $company->email ?? '-' }}</b></div>
                    </div>

                    <div class="clhs-due-card {{ $totalDue < 0 ? 'is-negative' : '' }}">
                        <span>Total Due Amount</span>
                        <strong>{{ number_format($totalDue, 2) }} -/</strong>
                        <small>{{ $amountToWords($totalDue) }} only -/</small>
                    </div>

                    <div class="clhs-actions">
                        <a href="{{ route('companyLedgerspay.create') }}" class="company-ledger-payment-btn">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            Company Ledger Payment
                        </a>
                        <a href="{{ route('companybillentry.create') }}" class="company-bill-entry-btn">
                            <i class="fa-solid fa-file-invoice"></i>
                            Company Bill Entry
                        </a>
                    </div>
                </section>
            @endif
        </div>

        <div class="clhs-toolbar">
            <div>
                <h4>Ledger Entries</h4>
                <span>{{ $hasRows ? count($all) . ' records found' : 'No records found' }}</span>
            </div>
            @if($hasCompany)
                <div class="clhs-toolbar-actions">
                    <input autocomplete="off" class="form-control company-row-search" id="filterInput" type="text" placeholder="Search ledger rows">
                    <a href="{{ route('companyledgerdetails.convert', ['companyid' => $companyid, 'date1' => $from, 'date2' => $to]) }}"
                        onclick="openPdfInNewTab(event, this.href); return false;"
                        class="clhs-print-btn {{ !$hasRows ? 'pdf-link-disabled' : '' }}">
                        <span>Print</span>
                        <i class="fa-solid fa-print"></i>
                    </a>
                </div>
            @endif
        </div>

        <div class="clhs-table-wrap">
            <table class="clhs-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Nepali Date</th>
                        <th>Particulars</th>
                        <th>Voucher Type</th>
                        <th>Bill No</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @if($hasRows)
                        @foreach ($all as $i)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $i->date }}</td>
                                <td>{{ \App\Support\NepaliDate::adToBsString($i->date ?? now()->toDateString(), 'en') }}</td>
                                <td>{{ $i->particulars }}</td>
                                <td>{{ $i->voucher_type }}</td>
                                <td>{{ $i->voucher_no }}</td>
                                <td class="text-end ledger-debit">{{ number_format((float) $i->debit, 2) }}</td>
                                <td class="text-end ledger-credit">{{ number_format((float) $i->credit, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="ledger-total-row">
                            <td colspan="6">Total</td>
                            <td class="text-end ledger-debit">Rs {{ number_format((float) $dts, 2) }}</td>
                            <td class="text-end ledger-credit">Rs {{ number_format((float) $cts, 2) }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="8" class="ledger-empty">Record Not Found</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function openPdfInNewTab(event, url) {
            event.preventDefault();
            var newTab = window.open(url, '_blank');
            newTab.focus();
        }

        $(document).on('customer:selected', function () {
            const form = document.getElementById('chosendatepdfform');
            const companyInput = document.getElementById('customerIdInput');

            if (!form || !companyInput || !companyInput.value) {
                return;
            }

            if (form.requestSubmit) {
                form.requestSubmit();
            } else {
                form.submit();
            }
        });
    </script>

    <style>
        .company-ledger-page {
            flex: 1 1 auto;
            width: 100%;
        }

        .company-ledger-page .clhs-top-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(360px, 1.1fr) minmax(420px, 1fr);
            margin-bottom: 18px;
        }

        .company-ledger-page .clhs-top-grid.only-search {
            grid-template-columns: minmax(360px, 760px);
        }

        .company-ledger-page .clhs-panel {
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
            padding: 18px;
        }

        .company-ledger-page .clhs-search-panel {
            border-top: 5px solid #0f8f5f;
        }

        .company-ledger-page .clhs-summary-panel {
            border-top: 5px solid #5d5ced;
        }

        .company-ledger-page .clhs-section-title {
            align-items: center;
            color: #64748b;
            display: flex;
            font-size: 13px;
            font-weight: 800;
            gap: 8px;
            letter-spacing: .02em;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .company-ledger-page .clhs-customer-search .search-input {
            border-color: #cbd5e1;
            font-size: 20px;
            min-height: 54px;
        }

        .company-ledger-page .clhs-date-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin: 16px 0;
        }

        .company-ledger-page .clhs-date-grid .input-group-text {
            background: #f1f5f9;
            font-weight: 700;
        }

        .company-ledger-page .clhs-search-btn {
            align-items: center;
            background: #1f2933;
            border: 0;
            border-radius: 6px;
            color: #ffffff;
            display: inline-flex;
            font-size: 24px;
            font-weight: 800;
            gap: 12px;
            justify-content: center;
            min-height: 62px;
            width: 100%;
        }

        .company-ledger-page .clhs-search-btn:hover {
            background: #111827;
        }

        .company-ledger-page .clhs-summary-head {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
        }

        .company-ledger-page .clhs-summary-head h3 {
            color: #111827;
            font-size: 26px;
            font-weight: 900;
            line-height: 1.05;
            margin: 0 0 4px;
        }

        .company-ledger-page .clhs-summary-head p {
            color: #475569;
            font-size: 18px;
            margin: 0;
        }

        .company-ledger-page .clhs-customer-meta {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin: 14px 0;
        }

        .company-ledger-page .clhs-customer-meta div {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
        }

        .company-ledger-page .clhs-customer-meta span,
        .company-ledger-page .clhs-due-card span {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .company-ledger-page .clhs-customer-meta b {
            color: #111827;
            display: block;
            font-size: 16px;
            margin-top: 3px;
            word-break: break-word;
        }

        .company-ledger-page .clhs-due-card {
            background: #138c55;
            border-radius: 8px;
            color: #ffffff;
            padding: 14px 18px;
        }

        .company-ledger-page .clhs-due-card.is-negative {
            background: #dc2626;
        }

        .company-ledger-page .clhs-due-card span {
            color: rgba(255, 255, 255, .86);
        }

        .company-ledger-page .clhs-due-card strong {
            display: block;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
            margin-top: 4px;
        }

        .company-ledger-page .clhs-due-card small {
            display: block;
            font-size: 14px;
            margin-top: 6px;
            text-transform: capitalize;
        }

        .company-ledger-page .clhs-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .company-ledger-page .company-ledger-payment-btn,
        .company-ledger-page .company-bill-entry-btn {
            align-items: center;
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-flex;
            font-size: 18px;
            font-weight: 800;
            gap: 8px;
            padding: 10px 14px;
            text-decoration: none !important;
        }

        .company-ledger-page .company-ledger-payment-btn {
            background: #2563eb;
        }

        .company-ledger-page .company-bill-entry-btn {
            background: #0f766e;
        }

        .company-ledger-page .pdf-link-disabled {
            opacity: .55;
            pointer-events: none;
        }

        .company-ledger-page .clhs-toolbar {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin: 18px 0 10px;
        }

        .company-ledger-page .clhs-toolbar h4 {
            color: #111827;
            font-size: 22px;
            font-weight: 900;
            margin: 0;
        }

        .company-ledger-page .clhs-toolbar span {
            color: #64748b;
            font-size: 14px;
            font-weight: 700;
        }

        .company-ledger-page .clhs-toolbar-actions {
            align-items: stretch;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .company-ledger-page .company-row-search {
            min-height: 50px;
            max-width: 300px;
        }

        .company-ledger-page .clhs-print-btn {
            align-items: stretch;
            border: 1px solid #2563eb;
            border-radius: 8px;
            color: #3730a3;
            display: inline-flex;
            font-size: 19px;
            font-weight: 900;
            overflow: hidden;
            text-decoration: none;
            text-transform: uppercase;
        }

        .company-ledger-page .clhs-print-btn span {
            padding: 13px 24px;
        }

        .company-ledger-page .clhs-print-btn i {
            align-items: center;
            background: #6366f1;
            color: #ffffff;
            display: inline-flex;
            padding: 0 16px;
        }

        .company-ledger-page .clhs-table-wrap {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow-x: auto;
        }

        .company-ledger-page .clhs-table {
            border-collapse: collapse;
            margin: 0;
            min-width: 1120px;
            width: 100%;
        }

        .company-ledger-page .clhs-table thead {
            display: table-header-group !important;
        }

        .company-ledger-page .clhs-table tbody {
            display: table-row-group !important;
            height: auto !important;
            overflow: visible !important;
        }

        .company-ledger-page .clhs-table tr {
            display: table-row !important;
            width: auto !important;
        }

        .company-ledger-page .clhs-table th,
        .company-ledger-page .clhs-table td {
            border: 1px solid #cbd5e1 !important;
            display: table-cell !important;
            font-size: 16px;
            padding: 12px 10px;
            vertical-align: middle;
        }

        .company-ledger-page .clhs-table th {
            background: #5d5ced;
            color: #ffffff;
            font-weight: 900;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .company-ledger-page .clhs-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .company-ledger-page .clhs-table tbody tr:hover td {
            background: #ecfeff;
        }

        .company-ledger-page .ledger-debit {
            color: #047857 !important;
            font-weight: 900;
            white-space: nowrap;
        }

        .company-ledger-page .ledger-credit {
            color: #b45309 !important;
            font-weight: 900;
            white-space: nowrap;
        }

        .company-ledger-page .ledger-total-row td {
            background: #eef2ff !important;
            font-weight: 900;
        }

        .company-ledger-page .ledger-empty {
            color: #64748b;
            font-weight: 800;
            padding: 42px !important;
            text-align: center;
        }

        @media (max-width: 1100px) {
            .company-ledger-page .clhs-top-grid,
            .company-ledger-page .clhs-top-grid.only-search {
                grid-template-columns: 1fr;
            }

            .company-ledger-page .clhs-toolbar {
                align-items: stretch;
                flex-direction: column;
            }
        }

        @media (max-width: 768px) {
            .company-ledger-page .clhs-date-grid,
            .company-ledger-page .clhs-customer-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>
</div>
@stop
