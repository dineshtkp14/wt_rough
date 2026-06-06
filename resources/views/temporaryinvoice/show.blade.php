@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    @php
        $tempAmountToWords = function ($num) use (&$tempAmountToWords) {
            $num = (int) floor($num);
            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            if ($num === 0) {
                return 'Zero';
            }

            $words = '';
            if ($num >= 10000000) {
                $words .= $tempAmountToWords(floor($num / 10000000)) . ' Crore ';
                $num %= 10000000;
            }
            if ($num >= 100000) {
                $words .= $tempAmountToWords(floor($num / 100000)) . ' Lakh ';
                $num %= 100000;
            }
            if ($num >= 1000) {
                $words .= $tempAmountToWords(floor($num / 1000)) . ' Thousand ';
                $num %= 1000;
            }
            if ($num >= 100) {
                $words .= $tempAmountToWords(floor($num / 100)) . ' Hundred ';
                $num %= 100;
            }
            if ($num >= 20) {
                $words .= $tens[floor($num / 10)] . ' ';
                $num %= 10;
            }
            if ($num > 0) {
                $words .= $ones[$num] . ' ';
            }

            return trim($words);
        };
    @endphp
    @php
        $temporaryInvoiceNepaliDate = \App\Support\NepaliDate::adToBsString($temporaryinvoice->invoice_date, 'en');
        $temporaryInvoiceTime = optional($temporaryinvoice->created_at)->format('h:i A');
        $temporaryInvoiceItemDiscount = $temporaryinvoice->items->sum(function ($item) {
            $gross = (float) $item->quantity * (float) $item->price;
            return max(0, $gross - (float) $item->subtotal);
        });
    @endphp

    <div class="main-content temporary-invoice-page">
        @yield('breadcrumb')

        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="temporary-invoice-actions d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h3 class="mb-0">Invoice #{{ $temporaryinvoice->invoice_number ?? $temporaryinvoice->id }}</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('temporaryinvoice.index') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-list"></i> Back to List
                    </a>
                    <a href="{{ route('temporaryinvoice.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> New
                    </a>
                    <button type="button" class="btn btn-success" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Print
                    </button>
                </div>
            </div>

            <div class="temporary-invoice-sheet">
                <div class="temporary-invoice-head">
                    <div>
                        <div class="temporary-invoice-label">Bill To</div>
                        <h4>{{ $temporaryinvoice->customer_name }}</h4>
                        <div class="temporary-invoice-muted">{{ $temporaryinvoice->customer_address ?: 'Address not added' }}</div>
                        <div class="temporary-invoice-muted">{{ $temporaryinvoice->contact_number ?: 'Contact not added' }}</div>
                    </div>
                    <div class="temporary-invoice-meta">
                        <div class="temporary-invoice-badge">Temporary Invoice</div>
                        <div><span>Invoice No</span><b>{{ $temporaryinvoice->invoice_number ?? $temporaryinvoice->id }}</b></div>
                        <div><span>Date</span><b>{{ $temporaryinvoice->invoice_date }}</b></div>
                        <div><span>Time</span><b>{{ $temporaryInvoiceTime }}</b></div>
                        <div><span>Nepali Date</span><b>{{ $temporaryInvoiceNepaliDate }}</b></div>
                    </div>
                </div>

                <div class="temporary-invoice-info">
                    <div>
                        <span>Date</span>
                        <b>{{ $temporaryinvoice->invoice_date }}</b>
                    </div>
                    <div>
                        <span>Time</span>
                        <b>{{ $temporaryInvoiceTime }}</b>
                    </div>
                    <div>
                        <span>Nepali Date</span>
                        <b>{{ $temporaryInvoiceNepaliDate }}</b>
                    </div>
                    <div>
                        <span>Customer Name</span>
                        <b>{{ $temporaryinvoice->customer_name }}</b>
                    </div>
                    <div>
                        <span>Address</span>
                        <b>{{ $temporaryinvoice->customer_address ?: '-' }}</b>
                    </div>
                    <div>
                        <span>Contact No</span>
                        <b>{{ $temporaryinvoice->contact_number ?: '-' }}</b>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="temporary-invoice-detail-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th class="text-end">Quantity</th>
                                <th>Unit</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Discount %</th>
                                <th class="text-end">Discount Amount</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($temporaryinvoice->items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $item->item_name }}</td>
                                    <td class="text-end">{{ $item->quantity }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td class="text-end">{{ number_format($item->price, 2) }}</td>
                                    @php
                                        $gross = (float) $item->quantity * (float) $item->price;
                                        $discountAmount = max(0, $gross - (float) $item->subtotal);
                                        $discountPercent = $gross > 0 ? max(0, ($discountAmount / $gross) * 100) : 0;
                                    @endphp
                                    <td class="text-end">{{ number_format($discountPercent, 2) }}%</td>
                                    <td class="text-end">{{ number_format($discountAmount, 2) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="temporary-print-totals">
                            <tr>
                                <th colspan="7" class="text-end">Total Discount</th>
                                <th class="text-end">{{ number_format($temporaryInvoiceItemDiscount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end">Subtotal</th>
                                <th class="text-end">{{ number_format($temporaryinvoice->subtotal, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end">Discount</th>
                                <th class="text-end">{{ number_format($temporaryinvoice->discount, 2) }}</th>
                            </tr>
                            <tr class="temporary-print-grand-total">
                                <th colspan="7" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($temporaryinvoice->total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="temporary-invoice-bottom">
                    <div class="temporary-invoice-notes">
                        @if ($temporaryinvoice->notes)
                            <span>Notes</span>
                            <p>{{ $temporaryinvoice->notes }}</p>
                        @endif
                    </div>
                    <table class="temporary-invoice-totals">
                        <tr>
                            <th>Total Discount</th>
                            <td>{{ number_format($temporaryInvoiceItemDiscount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Subtotal</th>
                            <td>{{ number_format($temporaryinvoice->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td>{{ number_format($temporaryinvoice->discount, 2) }}</td>
                        </tr>
                        <tr class="temporary-invoice-grand-total">
                            <th>Total</th>
                            <td>{{ number_format($temporaryinvoice->total, 2) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="temporary-amount-words">
                    <span>Amount in words</span>
                    <b>{{ $tempAmountToWords($temporaryinvoice->total) }} only /-</b>
                </div>
            </div>
        </div>
    </div>

    <style>
        .temporary-invoice-actions h3 {
            color: #111827;
            font-weight: 800;
        }

        .temporary-invoice-page {
            box-sizing: border-box;
            flex: 1 1 auto;
            width: 100%;
        }

        .temporary-invoice-page .container-fluid {
            max-width: none;
            width: 100%;
        }

        .temporary-invoice-sheet {
            background: #ffffff;
            border: 1px solid #d5dbe3;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.08);
            margin-bottom: 32px;
            padding: 28px 28px 34px;
        }

        .temporary-invoice-head {
            align-items: flex-start;
            border-bottom: 3px solid #111827;
            display: flex;
            justify-content: space-between;
            gap: 24px;
            padding-bottom: 20px;
        }

        .temporary-invoice-label,
        .temporary-invoice-info span,
        .temporary-invoice-meta span,
        .temporary-invoice-notes span,
        .temporary-amount-words span {
            color: #64748b;
            display: block;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .temporary-invoice-head h4 {
            font-size: 30px;
            font-weight: 800;
            margin: 4px 0 6px;
        }

        .temporary-invoice-muted {
            color: #475569;
            font-size: 16px;
            line-height: 1.45;
        }

        .temporary-invoice-meta {
            min-width: 260px;
            text-align: right;
        }

        .temporary-invoice-meta > div:not(.temporary-invoice-badge) {
            align-items: center;
            display: flex;
            gap: 18px;
            justify-content: space-between;
            margin-top: 10px;
        }

        .temporary-invoice-badge {
            background: #111827;
            color: #ffffff;
            display: inline-block;
            font-weight: 800;
            padding: 8px 14px;
            text-transform: uppercase;
        }

        .temporary-invoice-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(6, 1fr);
            margin: 22px 0;
            padding: 16px 18px;
        }

        .temporary-invoice-info b {
            color: #111827;
            font-size: 18px;
        }

        .temporary-invoice-detail-table,
        .temporary-invoice-totals {
            border-collapse: collapse;
            width: 100%;
        }

        .temporary-invoice-detail-table thead {
            display: table-header-group !important;
        }

        .temporary-invoice-detail-table tbody {
            display: table-row-group !important;
            height: auto !important;
            overflow: visible !important;
        }

        .temporary-invoice-detail-table tr {
            display: table-row !important;
            width: auto !important;
        }

        .temporary-invoice-detail-table th,
        .temporary-invoice-detail-table td {
            display: table-cell !important;
        }

        .temporary-print-totals {
            display: none;
        }

        .temporary-invoice-detail-table th {
            background: #5d5ced;
            color: #ffffff;
            font-size: 17px;
            padding: 12px 10px;
        }

        .temporary-invoice-detail-table td {
            background: #ffffff;
            border: 1px solid #cbd5e1 !important;
            font-size: 17px;
            padding: 12px 10px;
        }

        .temporary-invoice-bottom {
            align-items: flex-start;
            display: flex;
            gap: 28px;
            justify-content: space-between;
            margin-top: 20px;
        }

        .temporary-invoice-notes {
            color: #334155;
            flex: 1;
            font-size: 16px;
        }

        .temporary-invoice-notes p {
            margin-top: 6px;
        }

        .temporary-invoice-totals {
            max-width: 360px;
        }

        .temporary-invoice-totals th,
        .temporary-invoice-totals td {
            border: 1px solid #cbd5e1 !important;
            font-size: 18px;
            padding: 12px 14px;
        }

        .temporary-invoice-totals th {
            background: #f8fafc;
            text-align: right;
            text-transform: uppercase;
        }

        .temporary-invoice-totals td {
            font-weight: 800;
            text-align: right;
        }

        .temporary-invoice-totals .temporary-invoice-grand-total th,
        .temporary-invoice-totals .temporary-invoice-grand-total td {
            background: #111827 !important;
            color: #ffffff !important;
            font-size: 22px;
        }

        .temporary-invoice-totals .temporary-invoice-grand-total:hover th,
        .temporary-invoice-totals .temporary-invoice-grand-total:hover td {
            background: #111827 !important;
            color: #ffffff !important;
        }

        .temporary-amount-words {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #111827;
            font-size: 18px;
            line-height: 1.4;
            margin-top: 18px;
            padding: 14px 16px;
            text-transform: capitalize;
        }

        @media (max-width: 900px) {
            .temporary-invoice-actions,
            .temporary-invoice-head,
            .temporary-invoice-bottom {
                align-items: stretch !important;
                flex-direction: column;
            }

            .temporary-invoice-info {
                grid-template-columns: repeat(2, 1fr);
            }

            .temporary-invoice-meta {
                text-align: left;
            }
        }

        @media (max-width: 600px) {
            .temporary-invoice-sheet {
                padding: 18px 14px;
            }

            .temporary-invoice-info {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 6mm;
            }

            html,
            body {
                background: #ffffff !important;
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .side-nav,
            .mainbreadcrumb,
            .temporary-invoice-actions,
            .alert {
                display: none !important;
            }

            .temporary-invoice-page {
                padding-left: 0 !important;
                width: 100% !important;
            }

            .temporary-invoice-page .container-fluid {
                margin: 0 !important;
                max-width: none !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .temporary-invoice-sheet {
                border: 0;
                box-shadow: none;
                margin: 0;
                padding: 0 !important;
            }

            .temporary-invoice-head {
                gap: 10px;
                padding-bottom: 8px;
            }

            .temporary-invoice-head h4 {
                font-size: 20px;
                margin: 2px 0;
            }

            .temporary-invoice-label,
            .temporary-invoice-info span,
            .temporary-invoice-meta span,
            .temporary-invoice-notes span,
            .temporary-amount-words span {
                font-size: 10px;
            }

            .temporary-invoice-muted {
                font-size: 12px;
                line-height: 1.25;
            }

            .temporary-invoice-meta {
                min-width: 180px;
            }

            .temporary-invoice-meta > div:not(.temporary-invoice-badge) {
                gap: 8px;
                margin-top: 4px;
            }

            .temporary-invoice-badge {
                font-size: 14px;
                padding: 5px 8px;
            }

            .temporary-invoice-info {
                display: none !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .temporary-invoice-detail-table,
            .temporary-invoice-totals {
                min-width: 0 !important;
                table-layout: fixed;
                width: 100% !important;
                page-break-inside: avoid;
            }

            .temporary-print-totals {
                display: table-footer-group !important;
            }

            .temporary-invoice-detail-table th,
            .temporary-invoice-detail-table td {
                font-size: 8px !important;
                line-height: 1.08;
                overflow-wrap: anywhere;
                padding: 3px 4px !important;
                white-space: normal !important;
                word-break: break-word;
            }

            .temporary-invoice-detail-table th:nth-child(1),
            .temporary-invoice-detail-table td:nth-child(1) {
                width: 5% !important;
            }

            .temporary-invoice-detail-table th:nth-child(2),
            .temporary-invoice-detail-table td:nth-child(2) {
                width: 27% !important;
            }

            .temporary-invoice-detail-table th:nth-child(3),
            .temporary-invoice-detail-table td:nth-child(3) {
                width: 11% !important;
            }

            .temporary-invoice-detail-table th:nth-child(4),
            .temporary-invoice-detail-table td:nth-child(4) {
                width: 8% !important;
            }

            .temporary-invoice-detail-table th:nth-child(5),
            .temporary-invoice-detail-table td:nth-child(5) {
                width: 12% !important;
            }

            .temporary-invoice-detail-table th:nth-child(6),
            .temporary-invoice-detail-table td:nth-child(6) {
                width: 10% !important;
            }

            .temporary-invoice-detail-table th:nth-child(6) {
                font-size: 0 !important;
            }

            .temporary-invoice-detail-table th:nth-child(6)::after {
                content: "Disc %";
                font-size: 8px !important;
            }

            .temporary-invoice-detail-table th:nth-child(7),
            .temporary-invoice-detail-table td:nth-child(7) {
                width: 13% !important;
            }

            .temporary-invoice-detail-table th:nth-child(7) {
                font-size: 0 !important;
            }

            .temporary-invoice-detail-table th:nth-child(7)::after {
                content: "Disc Amt";
                font-size: 8px !important;
            }

            .temporary-invoice-detail-table th:nth-child(8),
            .temporary-invoice-detail-table td:nth-child(8) {
                width: 14% !important;
            }

            .temporary-invoice-bottom {
                display: block;
                margin-top: 8px;
            }

            .temporary-invoice-notes {
                font-size: 11px;
                margin-bottom: 6px;
            }

            .temporary-invoice-totals {
                display: none !important;
            }

            .temporary-invoice-totals th,
            .temporary-invoice-totals td {
                font-size: 10px !important;
                padding: 4px 5px !important;
            }

            .temporary-invoice-totals th {
                width: 58.5% !important;
            }

            .temporary-invoice-totals td {
                width: 41.5% !important;
            }

            .temporary-invoice-totals .temporary-invoice-grand-total th,
            .temporary-invoice-totals .temporary-invoice-grand-total td {
                font-size: 12px !important;
            }

            .temporary-print-totals th,
            .temporary-print-totals td {
                background: #f8fafc !important;
                border: 1px solid #cbd5e1 !important;
                color: #111827 !important;
                font-size: 9px !important;
                font-weight: 900;
                padding: 4px 5px !important;
            }

            .temporary-print-grand-total th,
            .temporary-print-grand-total td {
                background: #111827 !important;
                color: #ffffff !important;
                font-size: 11px !important;
            }

            .temporary-amount-words {
                font-size: 12px;
                margin-top: 8px;
                padding: 7px 8px;
            }
        }
    </style>
@stop
