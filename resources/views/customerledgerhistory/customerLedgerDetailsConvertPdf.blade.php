<!DOCTYPE html>
<html>
<head>
    <title>Customer Ledger Statement</title>
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
            color: #172033;
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
            background: #174f3d;
            border-radius: 5px 5px 0 0;
            margin-bottom: 10px;
        }

        .letterhead {
            text-align: center;
            padding-bottom: 9px;
            border-bottom: 1px solid #d7dde8;
        }

        .letterhead h1 {
            margin: 0;
            color: #111827;
            font-size: 26px;
            font-weight: 800;
            text-decoration: underline;
            letter-spacing: .4px;
        }

        .letterhead .subtitle {
            margin-top: 2px;
            color: #174f3d;
            font-size: 19px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .letterhead p {
            margin: 3px 0 0;
            color: #38445a;
            font-size: 12.5px;
        }

        .info-grid {
            width: 100%;
            margin-top: 12px;
            border-collapse: separate;
            border-spacing: 0 0;
        }

        .info-grid td {
            vertical-align: top;
            width: 33.333%;
            padding: 0 8px 0 0;
        }

        .box {
            min-height: 86px;
            padding: 10px 12px;
            border: 1px solid #d9e0ea;
            border-radius: 6px;
            background: #f8fafc;
        }

        .box-title {
            margin-bottom: 7px;
            color: #64748b;
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .line {
            margin: 2px 0;
            font-size: 13px;
        }

        .label {
            color: #475569;
            font-weight: 800;
        }

        .due-box {
            color: #ffffff;
            background: #158f56;
            border-color: #158f56;
        }

        .due-box.due-negative {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        .due-label {
            font-size: 12px;
            font-weight: 800;
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
            width: 33.333%;
            padding: 8px 10px;
            border: 1px solid #d9e0ea;
            background: #ffffff;
        }

        .summary-label {
            display: block;
            color: #64748b;
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .summary-value {
            display: block;
            margin-top: 2px;
            color: #111827;
            font-size: 17px;
            font-weight: 900;
        }

        .ledger {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .ledger thead {
            display: table-header-group;
        }

        .ledger th {
            padding: 7px 6px;
            color: #ffffff;
            background: #3348d4;
            border: 1px solid #2637a3;
            font-size: 12px;
            font-weight: 800;
            text-align: left;
            text-transform: uppercase;
        }

        .ledger td {
            padding: 6px;
            border: 1px solid #cbd5e1;
            font-size: 12.4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .ledger tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .ledger .num,
        .ledger .money {
            text-align: right;
            white-space: nowrap;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 12px;
            color: #0f5132;
            background: #d1fae5;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
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
            font-weight: 700;
        }

        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #d7dde8;
            color: #64748b;
            font-size: 11.5px;
        }
    </style>
</head>
<body>
@php
    $customer = $cusinfobyid->first();
    $debitTotal = (float) ($dts ?? 0);
    $creditTotal = (float) ($cts ?? 0);
    $dueAmount = $debitTotal - $creditTotal;
    $hasDateRange = !empty($fromdate) && !empty($todate);
    $periodText = $hasDateRange ? ($fromdate . ' to ' . $todate) : 'All dates';
    $periodMitiText = $hasDateRange
        ? (\App\Support\NepaliDate::adToBsString($fromdate, 'en') . ' to ' . \App\Support\NepaliDate::adToBsString($todate, 'en'))
        : 'All dates';

    if (!function_exists('customerLedgerWords')) {
        function customerLedgerWords($num) {
            $num = (int) round(abs($num));
            $ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
            $tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
            if ($num === 0) return "Zero";
            $words = "";
            if ($num >= 10000000) { $words .= customerLedgerWords(floor($num / 10000000)) . " Crore "; $num %= 10000000; }
            if ($num >= 100000) { $words .= customerLedgerWords(floor($num / 100000)) . " Lakh "; $num %= 100000; }
            if ($num >= 1000) { $words .= customerLedgerWords(floor($num / 1000)) . " Thousand "; $num %= 1000; }
            if ($num >= 100) { $words .= customerLedgerWords(floor($num / 100)) . " Hundred "; $num %= 100; }
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
        <div class="subtitle">Customer Ledger Statement - Credit Only</div>
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
                    <div class="line"><span class="label">Alt Phone:</span> {{ $customer->alternate_phoneno ?? ($customer->phoneno ?? '-') }}</div>
                    <div class="line"><span class="label">Email:</span> {{ $customer->email ?? '-' }}</div>
                </div>
            </td>
            <td>
                <div class="box">
                    <div class="box-title">Statement Info</div>
                    <div class="line"><span class="label">Period:</span> {{ $periodText }}</div>
                    <div class="line"><span class="label">Miti:</span> {{ $periodMitiText }}</div>
                    @if($hasDateRange)
                        <div class="line"><span class="label">From Date:</span> {{ $fromdate }}</div>
                        <div class="line"><span class="label">From Miti:</span> {{ \App\Support\NepaliDate::adToBsString($fromdate, 'en') }}</div>
                        <div class="line"><span class="label">To Date:</span> {{ $todate }}</div>
                        <div class="line"><span class="label">To Miti:</span> {{ \App\Support\NepaliDate::adToBsString($todate, 'en') }}</div>
                    @endif
                    <div class="line"><span class="label">Customer Id:</span> {{ $customeridonly ?? ($customer->id ?? '-') }}</div>
                    <div class="line"><span class="label">Generated:</span> {{ now()->format('Y-m-d H:i') }}</div>
                    <div class="line"><span class="label">Type:</span> Credit invoices, receipts and credit notes</div>
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
                <span class="summary-label">Debit Total</span>
                <span class="summary-value">Rs {{ number_format($debitTotal, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Credit Total</span>
                <span class="summary-value">Rs {{ number_format($creditTotal, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Amount In Words</span>
                <span class="summary-value">{{ $dueAmount < 0 ? 'Minus ' : '' }}{{ customerLedgerWords($dueAmount) }} Only -/</span>
            </td>
        </tr>
    </table>

    @if($all != null && count($all) > 0)
        <table class="ledger">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th style="width: 8%;">Date</th>
                    <th style="width: 8%;">Miti</th>
                    <th style="width: 12%;">Created At</th>
                    <th style="width: 17%;">Particulars</th>
                    <th style="width: 10%;">Voucher</th>
                    <th style="width: 13%;">Invoice Type</th>
                    <th style="width: 8%;">Invoice No</th>
                    <th style="width: 10%;">Debit</th>
                    <th style="width: 11%;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($all as $i)
                    @php
                        $type = $i->invoicetype == 'settlement' ? 'Nil Account' : str_replace('_', ' ', $i->invoicetype);
                        $badgeClass = $i->invoicetype == 'credit' ? 'credit' : ($i->invoicetype == 'payment' ? 'payment' : ($i->invoicetype == 'credit_note' ? 'note' : 'nil'));
                    @endphp
                    <tr>
                        <td class="num">{{ $loop->iteration }}</td>
                        <td>{{ $i->date }}</td>
                        <td>{{ \App\Support\NepaliDate::adToBsString($i->date ?? now()->toDateString(), 'en') }}</td>
                        <td>{{ $i->created_at }}</td>
                        <td>{{ $i->particulars }}</td>
                        <td>{{ $i->voucher_type }}</td>
                        <td>
                            <span class="badge {{ $badgeClass }}">{{ $type }}</span>
                            @if($i->invoicetype == 'payment')
                                <strong>CR-({{ $i->id }})</strong>
                            @endif
                        </td>
                        <td><strong>{{ $i->invoiceid ?? '-' }}</strong></td>
                        <td class="money">{{ number_format((float) ($i->debit ?? 0), 2) }}</td>
                        <td class="money">{{ number_format((float) ($i->credit ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                <tr class="totals">
                    <td colspan="8" class="money">Total</td>
                    <td class="money">Rs {{ number_format($debitTotal, 2) }}</td>
                    <td class="money">Rs {{ number_format($creditTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="empty">No ledger records found for this customer.</div>
    @endif

    <div class="footer">
        Printed ledger statement for {{ $customer->name ?? 'customer' }}. Please verify entries with original invoices and receipts.
    </div>
</div>
</body>
</html>
