@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    @php
        $customer = $cusinfobyid->first();
        $dueAmount = (float) $dts - (float) $cts;
        $hasRows = $all && count($all) > 0;
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

    <div class="main-content clhs-page">
        @yield('breadcrumb')

        <div class="container-fluid px-3 px-xl-4">
            <div class="card customer-card mb-4" id="customerCard" style="display: none;">
                <div class="card-body">
                    <h5 class="card-title">Customer Info</h5>
                    <p><span>ID: </span><span id="customerId">...</span></p>
                    <p class="card-text"><span>Name: </span><span id="customerName">...</span></p>
                    <p><span>Address: </span><span id="customerAddress">...</span></p>
                    <p><span>E-mail: </span><span id="customerEmail">...</span></p>
                    <p><span>PhoneNo: </span><span id="customerPhone">...</span></p>
                </div>

                <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                    <i class="fas fa-user"></i>
                </div>
            </div>

            <div class="clhs-top-grid">
                <section class="clhs-panel clhs-search-panel">
                    <div class="clhs-section-title">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Find Customer Ledger
                    </div>

                    <form action="{{ route('clhs.returnchoosendatehistroy') }}" method="get" id="chosendatepdfform">
                        <div class="search-box clhs-customer-search">
                            <input id="customerIdInput" name="customerid" hidden>
                            <input type="text"
                                class="search-input @error('customerid') is-invalid @enderror"
                                placeholder="Search Customer"
                                id="searchCustomerInput"
                                data-api="customer_search"
                                autocomplete="off">
                            @error('customerid')
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
                                <input type="date" name="date1" value="{{ request('date1') }}" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">End Date</span>
                                <input type="date" name="date2" value="{{ request('date2') }}" class="form-control">
                            </div>
                        </div>

                        <button type="submit" class="clhs-search-btn">
                            <i class="fas fa-search"></i>
                            Search Ledger
                        </button>
                    </form>
                </section>

                <section class="clhs-panel clhs-summary-panel">
                    <div class="clhs-summary-head">
                        <div>
                            <div class="clhs-section-title">
                                <i class="fa-solid fa-user"></i>
                                Customer Summary
                            </div>
                            <h3>{{ $customer->name ?? 'Select a customer' }}</h3>
                            <p>{{ $customer->address ?? 'No address selected' }}</p>
                        </div>

                        <a href="{{ route('clhspdf.convert', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate]) }}"
                            onclick="openPdfInNewTab(event, this.href); return false;"
                            class="clhs-print-btn {{ !$hasRows ? 'pdf-link-disabled' : '' }}">
                            <span>Print</span>
                            <i class="fa-solid fa-print"></i>
                        </a>
                    </div>

                    <div class="clhs-customer-meta">
                        <div><span>Phone</span><b>{{ $customer->phoneno ?? '-' }}</b></div>
                        <div><span>Alternate Phone</span><b>{{ $customer->alternate_phoneno ?? $customer->phoneno ?? '-' }}</b></div>
                        <div><span>Email</span><b>{{ $customer->email ?? '-' }}</b></div>
                    </div>

                    <div class="clhs-due-card {{ $dueAmount < 0 ? 'is-negative' : '' }}">
                        <span>Total Due Amount</span>
                        <strong>{{ number_format($dueAmount, 2) }} -/</strong>
                        <small>{{ $amountToWords($dueAmount) }} only -/</small>
                    </div>

                    <div class="clhs-actions">
                        <a href="{{ route('cpayments.create', [
                            'customerid' => $customeridonly,
                            'cname' => $customer->name ?? null,
                        ]) }}"
                            class="customer-ledger-payment-btn {{ !$customeridonly ? 'disabled' : '' }}">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            Customer Ledger Payment
                        </a>
                    </div>
                </section>
            </div>

            <div class="clhs-toolbar">
                <div>
                    <h4>Ledger Entries</h4>
                    <span>{{ $hasRows ? count($all) . ' records found' : 'No records found' }}</span>
                </div>
                <div class="clhs-table-search">
                    <i class="fa-solid fa-filter"></i>
                    <input class="form-control" id="filterInput" type="text" placeholder="Filter visible table rows">
                </div>
            </div>

            <div class="clhs-table-wrap">
                <table class="clhs-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Created At</th>
                            <th>Particulars</th>
                            <th>Voucher Type</th>
                            <th>Invoice Type</th>
                            <th>Invoice No</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($hasRows)
                            @foreach ($all as $i)
                                <tr class="{{ $i->invoicetype == 'payment' ? 'is-payment-row' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $i->id }}</td>
                                    <td>{{ $i->date }}</td>
                                    <td>{{ $i->created_at }}</td>
                                    <td>{{ $i->particulars }}</td>
                                    <td>{{ $i->voucher_type }}</td>
                                    <td>
                                        <span class="clhs-type-badge {{ $i->invoicetype == 'payment' ? 'payment' : 'credit' }}">
                                            {{ $i->invoicetype }}
                                            @if($i->invoicetype == 'payment')
                                                CR-({{ $i->id }})
                                            @endif
                                        </span>
                                    </td>
                                    <td>{{ $i->invoiceid ?: '-' }}</td>
                                    <td class="text-end">{{ number_format((float) $i->debit, 2) }}</td>
                                    <td class="text-end">{{ number_format((float) $i->credit, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="clhs-total-row">
                                <td colspan="8" class="text-end">Total</td>
                                <td class="text-end">{{ number_format((float) $dts, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $cts, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="10" class="clhs-empty-state">
                                    <i class="fa-solid fa-file-circle-question"></i>
                                    Select a customer and search to view ledger records.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function openPdfInNewTab(event, url) {
            event.preventDefault();
            var newTab = window.open(url, '_blank');
            if (newTab) newTab.focus();
        }
    </script>

    <style>
        .clhs-page {
            flex: 1 1 auto;
            width: 100%;
        }

        .clhs-top-grid {
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(360px, 1.1fr) minmax(420px, 1fr);
            margin-bottom: 18px;
        }

        .clhs-panel {
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
            padding: 18px;
        }

        .clhs-search-panel {
            border-top: 5px solid #0f8f5f;
        }

        .clhs-summary-panel {
            border-top: 5px solid #5d5ced;
        }

        .clhs-section-title {
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

        .clhs-customer-search .search-input {
            border-color: #cbd5e1;
            font-size: 20px;
            min-height: 54px;
        }

        .clhs-date-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin: 16px 0;
        }

        .clhs-date-grid .input-group-text {
            background: #f1f5f9;
            font-weight: 700;
        }

        .clhs-search-btn {
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

        .clhs-search-btn:hover {
            background: #111827;
        }

        .clhs-summary-head {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
        }

        .clhs-summary-head h3 {
            color: #111827;
            font-size: 26px;
            font-weight: 900;
            line-height: 1.05;
            margin: 0 0 4px;
        }

        .clhs-summary-head p {
            color: #475569;
            font-size: 18px;
            margin: 0;
        }

        .clhs-print-btn {
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

        .clhs-print-btn span {
            padding: 13px 24px;
        }

        .clhs-print-btn i {
            align-items: center;
            background: #6366f1;
            color: #ffffff;
            display: inline-flex;
            padding: 0 16px;
        }

        .clhs-customer-meta {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin: 14px 0;
        }

        .clhs-customer-meta div {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
        }

        .clhs-customer-meta span,
        .clhs-due-card span {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .clhs-customer-meta b {
            color: #111827;
            display: block;
            font-size: 16px;
            margin-top: 3px;
            word-break: break-word;
        }

        .clhs-due-card {
            background: #138c55;
            border-radius: 8px;
            color: #ffffff;
            padding: 14px 18px;
        }

        .clhs-due-card.is-negative {
            background: #dc2626;
        }

        .clhs-due-card span {
            color: rgba(255, 255, 255, .86);
        }

        .clhs-due-card strong {
            display: block;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
            margin-top: 4px;
        }

        .clhs-due-card small {
            display: block;
            font-size: 14px;
            margin-top: 6px;
            text-transform: capitalize;
        }

        .customer-ledger-payment-btn {
            align-items: center;
            background: #16a34a !important;
            border: 2px solid #0f7a37 !important;
            border-radius: 6px;
            color: #ffffff !important;
            display: inline-flex;
            font-size: 18px;
            font-weight: 800;
            gap: 8px;
            margin-top: 12px;
            padding: 10px 18px;
            text-decoration: none !important;
            white-space: nowrap;
        }

        .customer-ledger-payment-btn:hover {
            background: #0f7a37 !important;
            color: #ffffff !important;
        }

        .customer-ledger-payment-btn.disabled {
            opacity: .55;
            pointer-events: none;
        }

        .clhs-toolbar {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin: 18px 0 10px;
        }

        .clhs-toolbar h4 {
            color: #111827;
            font-size: 22px;
            font-weight: 900;
            margin: 0;
        }

        .clhs-toolbar span {
            color: #64748b;
            font-size: 14px;
            font-weight: 700;
        }

        .clhs-table-search {
            align-items: center;
            display: flex;
            min-width: 340px;
            position: relative;
        }

        .clhs-table-search i {
            color: #f59e0b;
            left: 14px;
            position: absolute;
        }

        .clhs-table-search input {
            border: 2px solid #f59e0b;
            padding-left: 40px;
        }

        .clhs-table-wrap {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow-x: auto;
        }

        .clhs-table {
            border-collapse: collapse;
            margin: 0;
            min-width: 1120px;
            width: 100%;
        }

        .clhs-table thead {
            display: table-header-group !important;
        }

        .clhs-table tbody {
            display: table-row-group !important;
            height: auto !important;
            overflow: visible !important;
        }

        .clhs-table tr {
            display: table-row !important;
            width: auto !important;
        }

        .clhs-table th,
        .clhs-table td {
            border: 1px solid #cbd5e1 !important;
            display: table-cell !important;
            font-size: 16px;
            padding: 12px 10px;
            vertical-align: middle;
        }

        .clhs-table th {
            background: #5d5ced;
            color: #ffffff;
            font-weight: 900;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .clhs-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .clhs-table tbody tr:hover td {
            background: #ecfeff;
        }

        .clhs-type-badge {
            border-radius: 999px;
            display: inline-flex;
            font-size: 13px;
            font-weight: 900;
            padding: 5px 10px;
            text-transform: uppercase;
        }

        .clhs-type-badge.credit {
            background: #fee2e2;
            color: #991b1b;
        }

        .clhs-type-badge.payment {
            background: #dcfce7;
            color: #166534;
        }

        .clhs-total-row td {
            background: #111827 !important;
            color: #ffffff;
            font-size: 18px;
            font-weight: 900;
        }

        .clhs-empty-state {
            color: #64748b;
            font-size: 18px !important;
            font-weight: 800;
            padding: 34px !important;
            text-align: center;
        }

        .clhs-empty-state i {
            display: block;
            font-size: 30px;
            margin-bottom: 8px;
        }

        @media (max-width: 1100px) {
            .clhs-top-grid,
            .clhs-customer-meta {
                grid-template-columns: 1fr;
            }

            .clhs-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .clhs-table-search {
                min-width: 0;
                width: 100%;
            }
        }

        @media (max-width: 700px) {
            .clhs-date-grid,
            .clhs-summary-head {
                grid-template-columns: 1fr;
            }

            .clhs-summary-head {
                align-items: stretch;
                flex-direction: column;
            }

            .clhs-print-btn,
            .customer-ledger-payment-btn {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
@stop
