<html><head><meta charset="UTF-8"><style>body{font-family:Arial}table{border-collapse:collapse;width:100%}th{background:#198754;color:#fff;font-weight:bold}th,td{border:1px solid #b8c4d4;padding:8px}td.num{text-align:right}.title{font-size:18px;font-weight:bold}</style></head><body>
<div class="title">VAT Purchase Bills - {{ $firm->name }}</div><p>Generated: {{ now()->format('Y-m-d H:i') }} @if($search) | Search: {{ $search }} @endif</p>
@php
    $exportSubtotal = 0;
    $exportTaxable = 0;
    $exportVat = 0;
    $exportGrandTotal = 0;
@endphp
<table>
    <thead>
        <tr>
            <th>Bill No.</th><th>Date (A.D.)</th><th>Date (B.S.)</th><th>Supplier</th>
            <th>Supplier VAT/PAN</th><th>Subtotal</th><th>Taxable Amount</th><th>VAT 13%</th><th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bills as $bill)
        @php
            $subtotal = $bill->items->sum(fn ($item) => (float) $item->quantity * (float) $item->rate);
            $taxable = $bill->items->where('is_taxable', true)->sum(fn ($item) => (float) $item->quantity * (float) $item->rate);
            $vat = round($taxable * 0.13, 2);
            $grand = $subtotal + $vat;
            $exportSubtotal += $subtotal;
            $exportTaxable += $taxable;
            $exportVat += $vat;
            $exportGrandTotal += $grand;
            $supplierVat = $bill->company_vat_no ?: $bill->supplier?->vat_no;
            $supplierPan = $bill->company_pan_no ?: $bill->supplier?->pan_no;
        @endphp
        <tr>
            <td>{{ $bill->bill_no }}</td>
            <td>{{ $bill->bill_date->format('Y-m-d') }}</td>
            <td>{{ \App\Support\NepaliDate::adToBsString($bill->bill_date->format('Y-m-d'), 'en') }}</td>
            <td>{{ $bill->company_name }}</td>
            <td>{{ $supplierVat ?: ($supplierPan ?: '-') }}</td>
            <td class="num">{{ number_format($subtotal, 2) }}</td>
            <td class="num">{{ number_format($taxable, 2) }}</td>
            <td class="num">{{ number_format($vat, 2) }}</td>
            <td class="num">{{ number_format($grand, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5" style="text-align:right">TOTAL</th>
            <th class="num">{{ number_format($exportSubtotal, 2) }}</th>
            <th class="num">{{ number_format($exportTaxable, 2) }}</th>
            <th class="num">{{ number_format($exportVat, 2) }}</th>
            <th class="num">{{ number_format($exportGrandTotal, 2) }}</th>
        </tr>
    </tfoot>
</table>
</body></html>
