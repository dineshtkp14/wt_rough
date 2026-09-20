<!doctype html>
<html><head><meta charset="utf-8"><title>All Purchase Balance Confirmations</title><style>@page{size:A4;margin:12mm}*{box-sizing:border-box}body{font-family:DejaVu Sans,sans-serif;color:#173b72;font-size:11px;margin:0}.no-print{text-align:center;margin:0 auto 12px;max-width:800px}.no-print button{background:#198754;color:#fff;border:0;border-radius:6px;padding:9px 14px;font-weight:bold}.count{text-align:center;color:#64748b;margin:0 0 12px}.paper{border-top:4px solid #d5a727;padding:20px 24px;min-height:260mm;page-break-after:always}.paper + .paper{page-break-before:always}.paper:last-child{page-break-after:auto}.firm{text-align:center;border-bottom:2px solid #2563eb;padding-bottom:10px}.firm h1{font-size:21px;margin:0;text-decoration:underline}.firm p{margin:5px 0}.title{text-align:center;color:#64748b;letter-spacing:1px;margin:18px 0}.recipient{border-left:4px solid #2563eb;padding:8px 12px;line-height:1.6}.table{border-collapse:collapse;width:100%;margin-top:18px}.table th,.table td{border:1px solid #9eb6d1;padding:7px}.table th{background:#344bd2;color:#fff;text-align:left}.right{text-align:right}.total{background:#172554;color:#fff;font-weight:bold}.sign{display:flex;justify-content:space-between;margin-top:55px}@media print{.no-print,.count{display:none}}</style></head>
<body><div class="no-print"><button onclick="window.print()">🖨 Print All Purchase Balances</button></div><p class="count">{{ $reports->count() }} company balance confirmations found for FY {{ $fiscalYear }}</p>
@forelse($reports as $report)
    @php
        $reportTaxable = $report['bills']->sum(fn($bill) => $bill->items->where('is_taxable', true)->sum(fn($item) => (float) $item->quantity * (float) $item->rate));
        $reportVat = round($reportTaxable * .13, 2);
    @endphp
    <section class="paper"><div class="firm"><h1>{{ strtoupper($firm->name) }}</h1><p>{{ $firm->address }}<br>VAT No: {{ $firm->pan_no }} - {{ $firm->phone }}</p></div><h2 class="title">PURCHASE BALANCE CONFIRMATION</h2><div class="recipient"><strong>Company: {{ $report['company'] }}</strong><br>Financial Year: {{ $fiscalYear }}</div><p>We confirm that the following purchase transactions are recorded with our organization for this company.</p>
    <table class="table"><thead><tr><th>#</th><th>Date (B.S.)</th><th>Bill No.</th><th class="right">Taxable</th><th class="right">VAT</th><th class="right">Total</th></tr></thead><tbody>
    @foreach($report['bills'] as $bill)
        @php($taxable=$bill->items->where('is_taxable',true)->sum(fn($item)=>(float)$item->quantity*(float)$item->rate))
        @php($vat=round($taxable*.13,2))
        <tr><td>{{ $loop->iteration }}</td><td>{{ \App\Support\NepaliDate::adToBsString($bill->bill_date->format('Y-m-d'),'en') }}</td><td>#{{ $bill->bill_no }}</td><td class="right">{{ number_format($taxable,2) }}</td><td class="right">{{ number_format($vat,2) }}</td><td class="right">{{ number_format($bill->items->sum(fn($item)=>(float)$item->quantity*(float)$item->rate)+$vat,2) }}</td></tr>
    @endforeach
    <tr class="total"><td colspan="3" class="right">TOTAL PURCHASE</td><td class="right">{{ number_format($reportTaxable,2) }}</td><td class="right">{{ number_format($reportVat,2) }}</td><td class="right">{{ number_format($report['total'],2) }}</td></tr>
    </tbody></table><div class="sign"><span>Prepared By: ____________</span><span>Authorized Signature: ____________</span></div></section>
@empty
    <div class="paper"><h2>No purchase records found.</h2></div>
@endforelse
</body></html>
