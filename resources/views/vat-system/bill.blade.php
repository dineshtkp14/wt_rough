@extends('layouts.master')
@section('content')
@php
$numberToWords = function ($number) use (&$numberToWords) {
    $number = (int) round($number);
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    if ($number === 0) return 'Zero';
    if ($number < 20) return $ones[$number];
    if ($number < 100) return $tens[intdiv($number, 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
    if ($number < 1000) return $ones[intdiv($number, 100)] . ' Hundred' . ($number % 100 ? ' ' . $numberToWords($number % 100) : '');
    if ($number < 100000) return $numberToWords(intdiv($number, 1000)) . ' Thousand' . ($number % 1000 ? ' ' . $numberToWords($number % 1000) : '');
    if ($number < 10000000) return $numberToWords(intdiv($number, 100000)) . ' Lakh' . ($number % 100000 ? ' ' . $numberToWords($number % 100000) : '');
    return $numberToWords(intdiv($number, 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . $numberToWords($number % 10000000) : '');
};
$total=$bill->items->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);
$taxable=$bill->items->where('is_taxable',true)->sum(fn($i)=>(float)$i->quantity*(float)$i->rate);
$nonTaxable=$total-$taxable;
$vat=round($taxable*.13,2);
$grand=$total-(float)$bill->discount+$vat;
@endphp
<style>
.print-actions{margin:20px auto;max-width:900px}.vat-paper{background:#fff;color:#173b72;font-family:Arial,sans-serif;margin:20px auto;padding:45px 55px;width:900px;min-height:1100px;box-shadow:0 2px 14px #0002}.seller{text-align:center;font-size:13px;line-height:1.45}.seller h2{color:#111;margin:2px 0;font-size:18px}.bill-head{display:flex;justify-content:space-between;margin:28px 0 22px;font-size:12px;line-height:1.7}.bill-head strong{color:#111}.vat-paper table{border-collapse:collapse;width:100%;font-size:12px;color:#111}.vat-paper th,.vat-paper td{border:1px solid #222;padding:6px}.vat-paper th{font-weight:800;text-align:center}.vat-paper .num{text-align:right}.summary{margin:16px 0 0 auto;width:390px;color:#173b72;font-size:12px}.summary div{display:flex;justify-content:space-between;padding:4px}.summary .grand{border-top:1px solid #111;color:#111;font-weight:800;font-size:14px;margin-top:4px}.footer{border-top:1px solid #111;margin-top:40px;padding-top:8px;font-size:9px;display:flex;justify-content:space-between}@media print{body *{visibility:hidden}.vat-paper,.vat-paper *{visibility:visible}.vat-paper{box-shadow:none;margin:0;padding:35px;width:100%}.print-actions{display:none}}
</style>
<div class="print-actions d-flex justify-content-between"><a href="{{ route('vat-system.create') }}" class="btn btn-outline-secondary">Back</a><button onclick="window.print()" class="btn btn-primary"><i class="fa fa-print me-1"></i>Print VAT Bill</button></div>
<div class="vat-paper"><div class="seller">{{ $bill->seller_name }}<br>VAT No : {{ $bill->seller_vat_no ?: '-' }}<br>Phone: {{ $bill->seller_phone ?: '-' }}<br>Email: {{ $bill->seller_email ?: '-' }}<h2>INVOICE</h2></div><div class="bill-head"><div>Customer Name: &nbsp; {{ $bill->customer->name }}<br>Address: &nbsp; {{ $bill->customer->address ?: '-' }}<br>PAN No. &nbsp; {{ $bill->customer->pan_no ?: '-' }}<br>Phone Number: &nbsp; {{ $bill->customer->phone ?: '-' }}<br>Payment Mode: &nbsp; {{ $bill->payment_mode ?: '-' }}</div><div><strong>Invoice No.:</strong> &nbsp; {{ $bill->bill_no }}<br><strong>Invoice Date:</strong> &nbsp; {{ $bill->bill_date->format('Y-m-d') }}<br><strong>Invoice Miti:</strong> &nbsp; {{ $bsDate }}</div></div><table><thead><tr><th>S.N</th><th>H.S Code</th><th>Particulars</th><th>Unit</th><th>Qty.</th><th>Rate</th><th>Amount</th></tr></thead><tbody>@foreach($bill->items as $item)<tr><td class="text-center">{{ $loop->iteration }}.</td><td>{{ $item->hs_code ?: '' }}</td><td>{{ $item->item_name }}</td><td class="text-center">{{ $item->unit }}</td><td class="text-center">{{ $item->quantity }}</td><td class="num">{{ number_format($item->rate,2) }}</td><td class="num">{{ number_format($item->quantity*$item->rate,2) }}</td></tr>@endforeach</tbody></table><div style="min-height:300px"></div><div style="font-size:10px"><strong>AMOUNT IN WORDS:</strong> {{ $numberToWords($grand) }} Only.</div><div class="summary"><div><span>Total</span><span>{{ number_format($total,2) }}</span></div><div><span>Discount</span><span>{{ number_format($bill->discount,2) }}</span></div><div><span>Non Taxable Total</span><span>{{ number_format($nonTaxable,2) }}</span></div><div><span>Taxable Total</span><span>{{ number_format($taxable,2) }}</span></div><div><span>VAT</span><span>{{ number_format($vat,2) }}</span></div><div class="grand"><span>Grand Total</span><span>{{ number_format($grand,2) }}</span></div></div><div class="footer"><span>For : Nepal E-Billing</span><span>Printed by: {{ $bill->added_by ?: '-' }}</span></div></div>
@endsection




