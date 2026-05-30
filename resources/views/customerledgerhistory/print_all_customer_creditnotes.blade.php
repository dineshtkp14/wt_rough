<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Credit Notes - {{ $customer->name ?? 'Customer' }}</title>
    <style>
        @page {
            size: A5 portrait;
            margin: 7mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #111827;
            font-family: Arial, sans-serif;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .page {
            min-height: 196mm;
            page-break-after: always;
            position: relative;
            width: 100%;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .watermark {
            color: #111827;
            font-size: 92px;
            font-weight: 800;
            left: 30%;
            opacity: .055;
            position: fixed;
            top: 42%;
            transform: rotate(-35deg);
        }

        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 5px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            letter-spacing: .5px;
            margin: 0;
        }

        .header p {
            color: #374151;
            font-size: 12px;
            margin: 1px 0;
        }

        .doc-bar {
            background: #111827;
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .4px;
            margin-top: 7px;
            padding: 5px 8px;
            text-align: center;
            text-transform: uppercase;
        }

        .meta {
            margin-top: 8px;
        }

        .meta-left {
            float: left;
            width: 52%;
        }

        .meta-right {
            float: right;
            text-align: right;
            width: 45%;
        }

        .clearfix::after {
            clear: both;
            content: "";
            display: block;
        }

        .cn-number {
            color: #111827;
            font-size: 24px;
            font-weight: 900;
            margin-bottom: 4px;
        }

        .field {
            margin: 2px 0;
        }

        .label {
            color: #4b5563;
            font-weight: 700;
        }

        .customer-box {
            border: 1px solid #9ca3af;
            margin-top: 8px;
            padding: 6px 8px;
        }

        table {
            border-collapse: collapse;
            margin-top: 9px;
            width: 100%;
        }

        th {
            background: #f97316;
            color: #ffffff;
            font-size: 12px;
            font-weight: 800;
            padding: 5px 4px;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #9ca3af;
            font-size: 12px;
            padding: 5px 4px;
            vertical-align: middle;
        }

        th,
        td {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .totals td {
            background: #f3f4f6;
            font-size: 13px;
            font-weight: 800;
        }

        .grand-total td {
            background: #111827;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
        }

        .words {
            border: 1px solid #d1d5db;
            border-top: 0;
            padding: 5px 7px;
        }

        .notes {
            border: 1px solid #d1d5db;
            margin-top: 8px;
            min-height: 24px;
            padding: 6px 8px;
        }

        .footer {
            border-top: 1px solid #9ca3af;
            bottom: 0;
            color: #4b5563;
            font-size: 11px;
            left: 0;
            padding-top: 4px;
            position: absolute;
            right: 0;
        }

        .empty {
            border: 1px solid #d1d5db;
            font-size: 16px;
            font-weight: 800;
            margin-top: 40mm;
            padding: 25px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        if (!function_exists('convertCreditNoteListNumberToWords')) {
            function convertCreditNoteListNumberToWords($num) {
                $num = (int) floor($num);
                $ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
                $tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
                if ($num == 0) return "Zero";
                $words = "";
                if ($num >= 10000000) { $words .= convertCreditNoteListNumberToWords(floor($num / 10000000)) . " Crore "; $num %= 10000000; }
                if ($num >= 100000) { $words .= convertCreditNoteListNumberToWords(floor($num / 100000)) . " Lakh "; $num %= 100000; }
                if ($num >= 1000) { $words .= convertCreditNoteListNumberToWords(floor($num / 1000)) . " Thousand "; $num %= 1000; }
                if ($num >= 100) { $words .= convertCreditNoteListNumberToWords(floor($num / 100)) . " Hundred "; $num %= 100; }
                if ($num >= 20) { $words .= $tens[floor($num / 10)] . " "; $num %= 10; }
                if ($num > 0) { $words .= $ones[(int) $num] . " "; }
                return trim($words);
            }
        }
    @endphp

    @if(count($creditNoteData) <= 0)
        <div class="empty">No credit notes found.</div>
    @endif

    @foreach($creditNoteData as $data)
        <div class="page">
            <div class="watermark">CN</div>

            <div class="header">
                <h1>OM HARI TRADELINK</h1>
                <p>Tikapur, Kailali (in front of Tikapur Police Station)</p>
                <p>Mobile: 9860378262, 9848448624, 9812656284</p>
            </div>

            <div class="doc-bar">Credit Note / Sales Return</div>

            <div class="meta clearfix">
                <div class="meta-left">
                    <div class="cn-number">CN NO: {{ $data['invoice']->id }}</div>
                    <div class="field"><span class="label">Customer:</span> {{ $customer->name ?? 'N/A' }}</div>
                    <div class="field"><span class="label">Address:</span> {{ $customer->address ?? 'N/A' }}</div>
                    <div class="field"><span class="label">Contact:</span> {{ $customer->phoneno ?? 'N/A' }}{{ !empty($customer->alternate_phoneno) ? ', ' . $customer->alternate_phoneno : '' }}</div>
                </div>

                <div class="meta-right">
                    <div class="field"><span class="label">Date:</span> {{ $data['invoice']->inv_date }}</div>
                    <div class="field"><span class="label">Miti:</span> {{ \App\Support\NepaliDate::adToBsString($data['invoice']->inv_date ?? now()->toDateString(), 'en') }}</div>
                    <div class="field"><span class="label">Customer Id:</span> {{ $customer->id ?? 'N/A' }}</div>
                    <div class="field"><span class="label">Created By:</span> {{ $data['invoice']->added_by ?? 'System' }}</div>
                </div>
            </div>

            <div class="customer-box">
                <span class="label">Email:</span> {{ $customer->email ?? '-' }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">#</th>
                        <th style="width: 13%;">Item ID</th>
                        <th style="width: 31%;">Item</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 11%;">Unit</th>
                        <th style="width: 13%;">Price</th>
                        <th style="width: 14%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['items'] as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->itemidorg ?? '-' }}</td>
                            <td class="text-left">{{ $item->itemname ?? $item->unstockedname ?? 'N/A' }}</td>
                            <td>{{ $item->nos ?? $item->quantity ?? 1 }}</td>
                            <td>{{ $item->unit ?? 'pcs' }}</td>
                            <td class="text-right">{{ number_format((float) ($item->price ?? 0), 2) }}</td>
                            <td class="text-right">{{ number_format((float) ($item->subtotal ?? 0), 2) }}</td>
                        </tr>
                    @endforeach

                    <tr class="totals">
                        <td colspan="5"></td>
                        <td class="text-right">Sub-Total</td>
                        <td class="text-right">Rs {{ number_format((float) ($data['invoice']->subtotal ?? 0), 2) }}</td>
                    </tr>
                    <tr class="totals">
                        <td colspan="5"></td>
                        <td class="text-right">Extra Discount</td>
                        <td class="text-right">Rs {{ number_format((float) ($data['invoice']->discount ?? 0), 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td colspan="5"></td>
                        <td class="text-right">Total Credit</td>
                        <td class="text-right">Rs {{ number_format((float) ($data['invoice']->total ?? 0), 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="words">
                <span class="label">Amount in Words:</span>
                {{ convertCreditNoteListNumberToWords($data['invoice']->total ?? 0) }} only/-
            </div>

            <div class="notes">
                <span class="label">Notes:</span> {{ $data['invoice']->notes ?? '-' }}
            </div>

            <div class="footer">
                This document records goods returned / credit note adjustment for the selected customer.
            </div>
        </div>
    @endforeach
</body>
</html>
