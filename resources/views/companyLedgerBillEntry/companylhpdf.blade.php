<!DOCTYPE html>
<html>
<head>
    <title>Company Ledger Statement</title>
    <style>
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
            font-family: DejaVu Sans, Arial, sans-serif;
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
            background: #174f3d;
        }

        .letterhead {
            padding-bottom: 9px;
            border-bottom: 1px solid #d7dde8;
            text-align: center;
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
            border-spacing: 0;
        }

        .info-grid td {
            width: 33.333%;
            padding: 0 8px 0 0;
            vertical-align: top;
        }

        .info-grid td:last-child {
            padding-right: 0;
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

        .due-words {
            margin-top: 3px;
            font-size: 12px;
            text-transform: capitalize;
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
            border: 1px solid #2637a3;
            color: #ffffff;
            background: #3348d4;
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

        .total-row td {
            background: #eef2ff !important;
            font-weight: 900;
        }

        .footer {
            margin-top: 10px;
            padding-top: 7px;
            border-top: 1px solid #d7dde8;
            color: #64748b;
            font-size: 11.5px;
            text-align: right;
        }
    </style>
</head>
<body>
@php
    $company = collect($xx ?? [])->first();
    $debitTotal = (float) ($dts ?? 0);
    $creditTotal = (float) ($cts ?? 0);
    $dueAmount = $debitTotal - $creditTotal;
    $rows = collect($all ?? []);

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

<div class="statement">
    <div class="top-rule"></div>

    <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
        <div class="subtitle">Company Ledger Statement</div>
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div>

    <table class="info-grid">
        <tr>
            <td>
                <div class="box">
                    <div class="box-title">Company Details</div>
                    <div class="line"><span class="label">ID:</span> {{ $company->id ?? '-' }}</div>
                    <div class="line"><span class="label">Name:</span> {{ $company->name ?? '-' }}</div>
                    <div class="line"><span class="label">Address:</span> {{ $company->address ?? '-' }}</div>
                    <div class="line"><span class="label">Phone:</span> {{ $company->phoneno ?? '-' }}</div>
                    <div class="line"><span class="label">Email:</span> {{ $company->email ?? '-' }}</div>
                </div>
            </td>
            <td>
                <div class="box">
                    <div class="box-title">Statement Period</div>
                    <div class="line"><span class="label">From:</span> {{ $from ?: 'Beginning' }}</div>
                    <div class="line"><span class="label">To:</span> {{ $to ?: 'Today' }}</div>
                    <div class="line"><span class="label">Records:</span> {{ $rows->count() }}</div>
                    <div class="line"><span class="label">Printed:</span> {{ now()->format('Y-m-d H:i:s') }}</div>
                </div>
            </td>
            <td>
                <div class="box due-box {{ $dueAmount < 0 ? 'due-negative' : '' }}">
                    <div class="due-label">Total Due Amount</div>
                    <div class="due-amount">Rs {{ number_format($dueAmount, 2) }}</div>
                    <div class="due-words">{{ $amountToWords($dueAmount) }} only</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td>
                <span class="summary-label">Total Debit</span>
                <span class="summary-value">Rs {{ number_format($debitTotal, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Total Credit</span>
                <span class="summary-value">Rs {{ number_format($creditTotal, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">Balance</span>
                <span class="summary-value">Rs {{ number_format($dueAmount, 2) }}</span>
            </td>
        </tr>
    </table>

    <table class="ledger">
        <colgroup>
            <col style="width: 6%;">
            <col style="width: 13%;">
            <col style="width: 13%;">
            <col style="width: 24%;">
            <col style="width: 15%;">
            <col style="width: 11%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
        </colgroup>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Nepali Date</th>
                <th>Particulars</th>
                <th>Voucher Type</th>
                <th>Bill No</th>
                <th class="money">Debit</th>
                <th class="money">Credit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    <td class="num">{{ $loop->iteration }}</td>
                    <td>{{ $row->date ?? '-' }}</td>
                    <td>{{ !empty($row->date) ? \App\Support\NepaliDate::adToBsString($row->date, 'en') : '-' }}</td>
                    <td>{{ $row->particulars ?? '-' }}</td>
                    <td>{{ $row->voucher_type ?? '-' }}</td>
                    <td>{{ $row->voucher_no ?? '-' }}</td>
                    <td class="money">{{ number_format((float) ($row->debit ?? 0), 2) }}</td>
                    <td class="money">{{ number_format((float) ($row->credit ?? 0), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 18px;">Record Not Found</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="6" class="money">Total</td>
                <td class="money">Rs {{ number_format($debitTotal, 2) }}</td>
                <td class="money">Rs {{ number_format($creditTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Printed on {{ now()->format('Y-m-d H:i:s') }}
    </div>
</div>
</body>
</html>
