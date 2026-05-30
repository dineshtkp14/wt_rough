<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Ledger Statement Cash Credit</title>
    <style>
        @font-face {
            font-family: "Noto Sans Devanagari";
            src: url('{{ public_path('fonts/NotoSansDevanagari-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            background: #ffffff;
            font-family: "Noto Sans Devanagari", DejaVu Sans, sans-serif;
            font-size: 13px;
            line-height: 1.35;
        }

        .statement {
            width: 100%;
        }

        .top-rule {
            height: 8px;
            margin-bottom: 10px;
            border-radius: 5px 5px 0 0;
            background: #0f766e;
        }

        .letterhead {
            padding-bottom: 9px;
            border-bottom: 1px solid #d6dee8;
            text-align: center;
        }

        .letterhead h1 {
            margin: 0;
            color: #111827;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: .4px;
            text-decoration: underline;
        }

        .letterhead .subtitle {
            margin-top: 2px;
            color: #0f766e;
            font-size: 19px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .letterhead p {
            margin: 3px 0 0;
            color: #4b5563;
            font-size: 12.5px;
        }

        .info-grid {
            width: 100%;
            margin-top: 12px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .info-grid td {
            width: 33.333%;
            padding-right: 8px;
            vertical-align: top;
        }

        .box {
            min-height: 86px;
            padding: 10px 12px;
            border: 1px solid #d9e1ec;
            border-radius: 6px;
            background: #f8fafc;
        }

        .box-title {
            margin-bottom: 7px;
            color: #64748b;
            font-size: 11.5px;
            font-weight: 900;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .line {
            margin: 2px 0;
            font-size: 13px;
        }

        .label {
            color: #475569;
            font-weight: 900;
        }

        .due-box {
            color: #ffffff;
            background: #0f766e;
            border-color: #0f766e;
        }

        .due-box.due-negative {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        .due-label {
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .due-amount {
            margin-top: 6px;
            font-size: 28px;
            font-weight: 900;
        }

        .summary-table {
            width: 100%;
            margin: 12px 0 10px;
            border-collapse: collapse;
        }

        .summary-table td {
            width: 25%;
            padding: 8px 10px;
            border: 1px solid #d9e1ec;
            background: #ffffff;
        }

        .summary-label {
            display: block;
            color: #64748b;
            font-size: 11.5px;
            font-weight: 900;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .summary-value {
            display: block;
            margin-top: 2px;
            color: #111827;
            font-size: 16.5px;
            font-weight: 900;
        }

        .section-title {
            margin: 12px 0 6px;
            color: #111827;
            font-size: 15.5px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .ledger,
        .notes-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .ledger thead,
        .notes-table thead {
            display: table-header-group;
        }

        .ledger th,
        .notes-table th {
            padding: 7px 6px;
            color: #ffffff;
            background: #0f766e;
            border: 1px solid #0a5c56;
            font-size: 12px;
            font-weight: 900;
            text-align: left;
            text-transform: uppercase;
        }

        .ledger td,
        .notes-table td {
            padding: 6px;
            border: 1px solid #cbd5e1;
            font-size: 12.2px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .ledger tbody tr:nth-child(even) td,
        .notes-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .num,
        .money {
            text-align: right;
            white-space: nowrap;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .badge.cash {
            color: #075985;
            background: #e0f2fe;
        }

        .badge.credit {
            color: #991b1b;
            background: #fee2e2;
        }

        .badge.payment {
            color: #065f46;
            background: #ccfbf1;
        }

        .badge.note {
            color: #92400e;
            background: #fef3c7;
        }

        .badge.nil {
            color: #7c2d12;
            background: #ffedd5;
        }

        .totals td {
            color: #ffffff;
            background: #111827 !important;
            border-color: #111827;
            font-size: 14px;
            font-weight: 900;
        }

        .empty {
            padding: 18px;
            color: #64748b;
            border: 1px dashed #cbd5e1;
            text-align: center;
            font-weight: 800;
        }

        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #d6dee8;
            color: #64748b;
            font-size: 11.5px;
        }
    </style>
</head>
<body>
@php
    $customer = $cusinfoforpdfok->first();
    $transactionTotal = (float) ($dts ?? 0);
    $creditOnlyDebit = (float) ($allnotcash ?? 0);
    $creditTotal = (float) ($cts ?? 0);
    $cashTotal = $transactionTotal - $creditOnlyDebit;
    $dueAmount = $creditOnlyDebit - $creditTotal;
    $hasDateRange = !empty($from) && !empty($to);
    $periodText = $hasDateRange ? ($from . ' to ' . $to) : 'All dates';
    $periodMitiText = $hasDateRange
        ? (\App\Support\NepaliDate::adToBsString($from, 'en') . ' to ' . \App\Support\NepaliDate::adToBsString($to, 'en'))
        : 'All dates';

    if (!function_exists('cashCreditLedgerWords')) {
        function cashCreditLedgerWords($num) {
            $num = (int) round(abs($num));
            $ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
            $tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
            if ($num === 0) return "Zero";
            $words = "";
            if ($num >= 10000000) { $words .= cashCreditLedgerWords(floor($num / 10000000)) . " Crore "; $num %= 10000000; }
            if ($num >= 100000) { $words .= cashCreditLedgerWords(floor($num / 100000)) . " Lakh "; $num %= 100000; }
            if ($num >= 1000) { $words .= cashCreditLedgerWords(floor($num / 1000)) . " Thousand "; $num %= 1000; }
            if ($num >= 100) { $words .= cashCreditLedgerWords(floor($num / 100)) . " Hundred "; $num %= 100; }
            if ($num >= 20) { $words .= $tens[floor($num / 10)] . " "; $num %= 10; }
            if ($num > 0) { $words .= $ones[$num] . " "; }
            return trim($words);
        }
    }
@endphp

<div class="statement">
    <div class="top-rule"></div>

    <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
        <div class="subtitle">Customer Ledger Statement - Cash / Credit</div>
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div>

    <table class="info-grid">
        <tr>
            <td>
                <div class="box">
                    <div class="box-title">Customer Details</div>
                    <div class="line"><span class="label">Name:</span> {{ $customer->name ?? '-' }}</div>
                    <div class="line"><span class="label">Address:</span> {{ $customer->address ?? '-' }}</div>
                    <div class="line"><span class="label">Phone:</span> {{ $customer->phoneno ?? '-' }}</div>
                    <div class="line"><span class="label">Alt Phone:</span> {{ $customer->alternate_phoneno ?? '-' }}</div>
                    <div class="line"><span class="label">Email:</span> {{ $customer->email ?? '-' }}</div>
                </div>
            </td>
            <td>
                <div class="box">
                    <div class="box-title">Statement Info</div>
                    <div class="line"><span class="label">Period:</span> {{ $periodText }}</div>
                    <div class="line"><span class="label">Miti:</span> {{ $periodMitiText }}</div>
                    @if($hasDateRange)
                        <div class="line"><span class="label">From Date:</span> {{ $from }}</div>
                        <div class="line"><span class="label">From Miti:</span> {{ \App\Support\NepaliDate::adToBsString($from, 'en') }}</div>
                        <div class="line"><span class="label">To Date:</span> {{ $to }}</div>
                        <div class="line"><span class="label">To Miti:</span> {{ \App\Support\NepaliDate::adToBsString($to, 'en') }}</div>
                    @endif
                    <div class="line"><span class="label">Customer Id:</span> {{ $cid ?? ($customer->id ?? '-') }}</div>
                    <div class="line"><span class="label">Generated:</span> {{ now()->format('Y-m-d H:i') }}</div>
                    <div class="line"><span class="label">Type:</span> Cash invoices, credit invoices, receipts and credit notes</div>
                </div>
            </td>
            <td>
                <div class="box due-box {{ $dueAmount < 0 ? 'due-negative' : '' }}">
                    <div class="due-label">Total Due Amount</div>
                    <div class="due-amount">Rs {{ number_format($dueAmount, 2) }} -/</div>
                    <div>{{ $dueAmount < 0 ? 'Advance balance' : 'Customer balance' }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td>
                <span class="summary-label">All Sales Total</span>
                <span class="summary-value">Rs {{ number_format($transactionTotal, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Cash Sales</span>
                <span class="summary-value">Rs {{ number_format($cashTotal, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Credit Debit</span>
                <span class="summary-value">Rs {{ number_format($creditOnlyDebit, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Credit / Receipts</span>
                <span class="summary-value">Rs {{ number_format($creditTotal, 2) }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <span class="summary-label">Due Amount In Words</span>
                <span class="summary-value">{{ $dueAmount < 0 ? 'Minus ' : '' }}{{ cashCreditLedgerWords($dueAmount) }} Only -/</span>
            </td>
        </tr>
    </table>

    <div class="section-title">Ledger Entries</div>
    @if($all != null && count($all) > 0)
        <table class="ledger">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th style="width: 8%;">Date</th>
                    <th style="width: 8%;">Miti</th>
                    <th style="width: 17%;">Particulars</th>
                    <th style="width: 11%;">Voucher</th>
                    <th style="width: 9%;">Invoice No</th>
                    <th style="width: 9%;">CN No</th>
                    <th style="width: 13%;">Type</th>
                    <th style="width: 11%;">Debit</th>
                    <th style="width: 11%;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($all as $i)
                    @php
                        $type = $i->invoicetype == 'settlement' ? 'Nil Account' : str_replace('_', ' ', $i->invoicetype);
                        $badgeClass = $i->invoicetype == 'cash' ? 'cash' : ($i->invoicetype == 'credit' ? 'credit' : ($i->invoicetype == 'payment' ? 'payment' : ($i->invoicetype == 'credit_note' ? 'note' : 'nil')));
                        $cnNo = $i->cninvoiceid ?? (($i->is_credit_note ?? false) ? $i->invoiceid : '-');
                    @endphp
                    <tr>
                        <td class="num">{{ $loop->iteration }}</td>
                        <td>{{ $i->date }}</td>
                        <td>{{ \App\Support\NepaliDate::adToBsString($i->date ?? now()->toDateString(), 'en') }}</td>
                        <td>{{ $i->particulars }}</td>
                        <td>{{ $i->voucher_type }}</td>
                        <td><strong>{{ ($i->is_credit_note ?? false) ? '-' : ($i->invoiceid ?? '-') }}</strong></td>
                        <td><strong>{{ $cnNo }}</strong></td>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $type }}</span>
                            @if($i->invoicetype == 'payment')
                                <strong>CR-({{ $i->id }})</strong>
                            @endif
                        </td>
                        <td class="money">{{ number_format((float) ($i->debit ?? 0), 2) }}</td>
                        <td class="money">{{ number_format((float) ($i->credit ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                <tr class="totals">
                    <td colspan="8" class="money">Total</td>
                    <td class="money">Rs {{ number_format($transactionTotal, 2) }}</td>
                    <td class="money">Rs {{ number_format($creditTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty">No ledger records found for this customer.</div>
    @endif

    <div class="section-title">Credit Notes / Sales Return Details</div>
    @if($creditnoteledger != null && count($creditnoteledger) > 0)
        <table class="notes-table">
            <thead>
                <tr>
                    <th style="width: 7%;">#</th>
                    <th style="width: 14%;">Date</th>
                    <th style="width: 31%;">Particulars</th>
                    <th style="width: 18%;">Voucher Type</th>
                    <th style="width: 15%;">CN Invoice No</th>
                    <th style="width: 15%;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($creditnoteledger as $i)
                    <tr>
                        <td class="num">{{ $loop->iteration }}</td>
                        <td>{{ $i->date ?? $i->created_at }}</td>
                        <td>{{ $i->particulars }}</td>
                        <td>{{ $i->voucher_type }}</td>
                        <td><strong>{{ $i->invoiceid }}</strong></td>
                        <td class="money">{{ number_format((float) ($i->debit ?? $i->credit ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                <tr class="totals">
                    <td colspan="5" class="money">Credit Note Total</td>
                    <td class="money">Rs {{ number_format((float) ($debittotalcrnotes ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty">No credit notes found for this customer.</div>
    @endif

    <div class="footer">
        Printed on {{ now()->format('Y-m-d H:i') }}. Please verify entries with original invoices, receipts and credit notes.
    </div>
</div>
</body>
</html>
