<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Deleted Invoice - {{ $invoiceid }}</title>
    <style>
        @page { size: A5 portrait; margin: 7mm; }
        * { box-sizing: border-box; }
        body { color: #111827; font-family: Arial, sans-serif; font-size: 13px; line-height: 1.4; margin: 0; padding: 0; }
        .page { min-height: 196mm; position: relative; width: 100%; }
        .watermark { color: #991b1b; font-size: 72px; font-weight: 900; left: 16%; opacity: .06; position: fixed; top: 43%; transform: rotate(-32deg); }
        .header { border-bottom: 2px solid #111827; padding-bottom: 5px; text-align: center; }
        .header h1 { font-size: 28px; letter-spacing: .5px; margin: 0; }
        .header p { color: #374151; font-size: 12px; margin: 1px 0; }
        .doc-bar { background: #991b1b; color: #fff; font-size: 14px; font-weight: 900; letter-spacing: .4px; margin-top: 7px; padding: 5px 8px; text-align: center; text-transform: uppercase; }
        .meta { margin-top: 8px; }
        .meta-left { float: left; width: 52%; }
        .meta-right { float: right; text-align: right; width: 45%; }
        .clearfix::after { clear: both; content: ""; display: block; }
        .invoice-number { font-size: 24px; font-weight: 900; margin-bottom: 4px; }
        .field { margin: 2px 0; }
        .label { color: #4b5563; font-weight: 700; }
        .customer-box { border: 1px solid #9ca3af; margin-top: 8px; padding: 6px 8px; }
        table { border-collapse: collapse; margin-top: 9px; width: 100%; }
        th { background: #991b1b; color: #fff; font-size: 12px; font-weight: 800; padding: 5px 4px; text-transform: uppercase; }
        td { border: 1px solid #9ca3af; font-size: 12px; padding: 5px 4px; text-align: center; vertical-align: middle; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .totals td { background: #f3f4f6; font-size: 13px; font-weight: 800; }
        .grand-total td { background: #111827; color: #fff; font-size: 14px; font-weight: 900; }
        .words { border: 1px solid #d1d5db; border-top: 0; padding: 5px 7px; }
        .notes { border: 1px solid #d1d5db; margin-top: 8px; min-height: 24px; padding: 6px 8px; }
        .footer { border-top: 1px solid #9ca3af; bottom: 0; color: #4b5563; font-size: 11px; left: 0; padding-top: 4px; position: absolute; right: 0; }
    </style>
</head>
<body>
@php
    $invoice = $allinvoices && count($allinvoices) > 0 ? $allinvoices->first() : null;
    $customer = $cinfodetails && count($cinfodetails) > 0 ? $cinfodetails->first() : null;

    if (!function_exists('convertDeletedInvoiceNumberToWords')) {
        function convertDeletedInvoiceNumberToWords($num) {
            $num = (int) floor($num);
            $ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
            $tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
            if ($num == 0) return "Zero";
            $words = "";
            if ($num >= 10000000) { $words .= convertDeletedInvoiceNumberToWords(floor($num / 10000000)) . " Crore "; $num %= 10000000; }
            if ($num >= 100000) { $words .= convertDeletedInvoiceNumberToWords(floor($num / 100000)) . " Lakh "; $num %= 100000; }
            if ($num >= 1000) { $words .= convertDeletedInvoiceNumberToWords(floor($num / 1000)) . " Thousand "; $num %= 1000; }
            if ($num >= 100) { $words .= convertDeletedInvoiceNumberToWords(floor($num / 100)) . " Hundred "; $num %= 100; }
            if ($num >= 20) { $words .= $tens[floor($num / 10)] . " "; $num %= 10; }
            if ($num > 0) { $words .= $ones[(int) $num] . " "; }
            return trim($words);
        }
    }
@endphp
<div class="page">
    <div class="watermark">DELETED</div>
    <div class="header">
        <h1>OM HARI TRADELINK</h1>
        <p>Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile: 9860378262, 9848448624, 9812656284</p>
    </div>
    <div class="doc-bar">Deleted Invoice Copy</div>

    <div class="meta clearfix">
        <div class="meta-left">
            <div class="invoice-number">INV NO: {{ $invoiceid }}</div>
            <div class="field"><span class="label">Customer:</span> {{ $customer->name ?? 'N/A' }}</div>
            <div class="field"><span class="label">Address:</span> {{ $customer->address ?? 'N/A' }}</div>
            <div class="field"><span class="label">Contact:</span> {{ $customer->phoneno ?? 'N/A' }}</div>
        </div>
        <div class="meta-right">
            <div class="field"><span class="label">Type:</span> {{ ucfirst($invoice->inv_type ?? '-') }}</div>
            <div class="field"><span class="label">Date:</span> {{ $invoice->inv_date ?? '' }}</div>
            <div class="field"><span class="label">Miti:</span> {{ \App\Support\NepaliDate::adToBsString(($invoice->inv_date ?? now()->toDateString()), 'en') }}</div>
            <div class="field"><span class="label">Deleted At:</span> {{ $invoice && $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d H:i') : '-' }}</div>
        </div>
    </div>

    <div class="customer-box">
        <span class="label">Email:</span> {{ $customer->email ?? '-' }}
        &nbsp; | &nbsp;
        <span class="label">Customer Id:</span> {{ $invoice->customerid ?? $customer->id ?? 'N/A' }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">#</th>
                <th style="width: 34%;">Item</th>
                <th style="width: 12%;">Qty</th>
                <th style="width: 12%;">Unit</th>
                <th style="width: 16%;">Price</th>
                <th style="width: 18%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allcusbyid as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left">{{ $item->itemid ?? $item->unstockedname ?? 'N/A' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit ?? 'pcs' }}</td>
                    <td class="text-right">{{ number_format((float) ($item->price ?? 0), 2) }}</td>
                    <td class="text-right">{{ number_format((float) ($item->subtotal ?? 0), 2) }}</td>
                </tr>
            @endforeach
            <tr class="totals">
                <td colspan="4"></td>
                <td class="text-right">Sub-Total</td>
                <td class="text-right">Rs {{ number_format((float) ($invoice->subtotal ?? 0), 2) }}</td>
            </tr>
            <tr class="totals">
                <td colspan="4"></td>
                <td class="text-right">Discount</td>
                <td class="text-right">Rs {{ number_format((float) ($invoice->discount ?? 0), 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td colspan="4"></td>
                <td class="text-right">Total</td>
                <td class="text-right">Rs {{ number_format((float) ($invoice->total ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="words">
        <span class="label">Amount in Words:</span>
        {{ convertDeletedInvoiceNumberToWords($invoice->total ?? 0) }} only/-
    </div>

    <div class="notes">
        <span class="label">Notes:</span> {{ $invoice->notes ?? '-' }}
    </div>

    <div class="footer">
        This is a deleted invoice backup copy generated from archived invoice records.
    </div>
</div>
</body>
</html>
