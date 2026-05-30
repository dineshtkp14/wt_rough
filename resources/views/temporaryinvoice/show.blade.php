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

    <div class="main-content temporary-invoice-page">
        @yield('breadcrumb')

        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="temporary-invoice-actions d-flex justify-content-between align-items-center mb-3">
                <div>
                    <div class="text-muted fw-semibold text-uppercase small">Temporary Invoice Details</div>
                    <h3 class="mb-0">Invoice #{{ $temporaryinvoice->invoice_number ?? $temporaryinvoice->id }}</h3>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('temporaryinvoice.index') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-list"></i> Back to List
                    </a>
                    <a href="{{ route('temporaryinvoice.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> New
                    </a>
                    <a href="{{ route('temporaryinvoice.print', $temporaryinvoice) }}" class="btn btn-success" target="_blank">
                        <i class="fa-solid fa-print"></i> Print
                    </a>
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
                    </div>
                </div>

                <div class="temporary-invoice-info">
                    <div>
                        <span>Date</span>
                        <b>{{ $temporaryinvoice->invoice_date }}</b>
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
                                    <td class="text-end fw-semibold">{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
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
            grid-template-columns: repeat(4, 1fr);
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

        .temporary-invoice-grand-total th,
        .temporary-invoice-grand-total td {
            background: #111827;
            color: #ffffff;
            font-size: 22px;
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
    </style>
@stop
