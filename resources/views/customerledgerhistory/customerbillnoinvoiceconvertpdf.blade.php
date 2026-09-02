<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Print</title>

  @php
    // Absolute filesystem paths for Dompdf to embed fonts (REGULAR ONLY)
    $nepR = str_replace('\\','/', public_path('fonts/Hind-Regular.ttf'));                 // Nepali
    $engR = str_replace('\\','/', public_path('fonts/NotoSans_Condensed-Regular.ttf'));  // English
  @endphp

  <style>
    /* ---------- Fonts (filesystem paths for Dompdf) ---------- */
    @font-face { font-family:'HindDevanagari';  src:url('file://{{ $nepR }}') format('truetype');  font-weight:normal; font-style:normal; }
    @font-face { font-family:'NotoSansEnglish'; src:url('file://{{ $engR }}') format('truetype');  font-weight:normal; font-style:normal; }

    /* ---------- REAL page margins (use mm) ---------- */
    @page { size: A5 portrait; margin: 200mm; }   /* ← page margin on all sides */

    html, body{
      margin:0; padding:0;                       /* leave body with no spacing */
      font-family:'NotoSansEnglish','HindDevanagari',sans-serif;
      font-size:14px; line-height:1.12;
    }
    *{ box-sizing:border-box; }
    p{ margin:0 0 1px 0; line-height:1.12; }
    .invoice-page{ page-break-after:always; }
    .invoice-page:last-child{ page-break-after:auto; }
    .page-count{ font-size:12px; text-align:right; margin-top:4px; }
    .continuation-note{ font-size:13px; font-weight:700; text-align:right; padding-top:6px; }

    /* ---------- Inner page padding box ---------- */
    .page{ padding:50px; background:#fff; }      /* ← page padding */

    /* Header */
    .letterhead{ color:#000; padding:0 20px 8px; text-align:center; }
    .letterhead h1{ margin:0 0 4px; font-size:38px; font-weight:900; letter-spacing:1px; line-height:1.04; }
    .letterhead h1.memo-title{ font-size:24px; margin-top:30px; }
    .business-name{ font-size:24px; font-weight:900; letter-spacing:1px; margin-top:5px; }

    .address-info{ font-size:13px; text-align:center; margin-top:6px; }
    .address-info p{ margin:1px 0; }

    .invoice-info{ font-size:13px; margin-top:8px; }
    .invoice-info p{ margin:1px 0; }
    .firstdiv{ float:right; margin-top:-100px; }
    .seconddiv{ margin-top:-16px !important; }

    /* Bigger text for Date + Miti ONLY */
    .date-line{ font-size:18px; }
    .miti-line{ font-size:18px; font-family:'HindDevanagari',sans-serif; }

    /* Nepali runs */
    .nep, .label-nep{ font-family:'HindDevanagari',sans-serif; line-height:1.14; }
    .label-nep{ display:inline-block; padding-left:3px; } /* avoids matra clipping */

    /* INVOICE NO / PAN block */
    .forbillandpan{ margin-top:-80px !important; line-height:1.12; }
    .invoice-no{ font-size:18px; font-weight:700; letter-spacing:.3px; margin-bottom:1px; }
    .invoice-no .num{ font-weight:800; }
    .pan-line{ font-size:14px; margin-top:0; }

    /* Table */
    table{ width:100%; border-collapse:collapse; margin-top:10px; font-size:18px; }
    th,td{ border:1px solid #000; padding:0 3px; height:20px; line-height:1.08; vertical-align:middle; text-align:center; }
    th{ font-weight:700; }
    .percent-pricing-table{ font-size:13px; table-layout:fixed; }
    .percent-pricing-table th,
    .percent-pricing-table td{ padding:2px 3px; }
    .percent-pricing-table th{ font-size:13px; line-height:1.05; padding:3px 2px; }
    .percent-pricing-table .item-name-cell{ text-align:left; padding-left:6px; }
    .percent-pricing-table .numeric-cell{ text-align:right; padding-right:6px; white-space:nowrap; }
    .percent-pricing-table .center-cell{ text-align:center; white-space:nowrap; }
    .summary-label{
      background:#f7f7f7;
      font-size:13px;
      font-weight:800;
      line-height:1.05;
      padding:3px 6px 3px 3px;
      text-align:right;
      white-space:nowrap;
    }
    .summary-value{
      font-size:14px;
      font-weight:700;
      padding-right:6px;
      text-align:right;
      white-space:nowrap;
    }
    .amount-words-cell{ font-size:13px; text-align:left; }

    .text-right{ text-align:right; }
    .notes{ margin-top:8px; font-size:13px; line-height:1.12; }
    .forfontsizebll p{ font-size:16px !important; line-height:1.12; }

    /* Watermark */
    .watermark{
      position:fixed; top:45%; left:35%;
      transform:rotate(-45deg);
      font-size:120px; opacity:.1; color:gray; pointer-events:none;
    }
    .clearfix::after{ content:""; display:block; clear:both; }
  </style>
</head>
<body>
@php
  $invoice = $allinvoices ? $allinvoices->first() : null;
  $items = collect($allcusbyid ?? []);
  $showPercentPricing = $items->contains(function ($item) {
    return $item->list_price !== null || $item->discount_percent !== null;
  });
  $itemPages = $items->chunk(13);
  if ($itemPages->isEmpty()) {
    $itemPages = collect([collect()]);
  }
  $totalPages = $itemPages->count();
  $invoiceTime = optional($invoice)->created_at
    ? \Carbon\Carbon::parse($invoice->created_at)->format('H:i:s')
    : '';
  $customer = collect($cinfodetails ?? [])->first();
  $customerType = strtolower(trim((string) ($customer->type ?? '')));
  // Older customer records may not have a type yet; treat those as regular
  // customers so credit/cash memo headings are still shown.
  $isShopCustomer = in_array($customerType, ['', 'shop', 'customer'], true);
  $memoType = strtoupper((string) ($forinvoicetype->invoicetype ?? $invoice->inv_type ?? 'cash')) === 'CREDIT'
    ? 'QUOTATION/CREDIT MEMO'
    : 'QUOTATION/CASH MEMO';

  $amountToWords = function ($num) use (&$amountToWords) {
    $num = (int) floor($num);
    $ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
    $tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
    if ($num == 0) return "Zero";
    $words = "";
    if ($num >= 10000000) { $words .= $amountToWords(floor($num/10000000))." Crore "; $num %= 10000000; }
    if ($num >= 100000)   { $words .= $amountToWords(floor($num/100000))." Lakh ";  $num %= 100000; }
    if ($num >= 1000)     { $words .= $amountToWords(floor($num/1000))." Thousand "; $num %= 1000; }
    if ($num >= 100)      { $words .= $amountToWords(floor($num/100))." Hundred ";  $num %= 100; }
    if ($num >= 20)       { $words .= $tens[floor($num/10)]." "; $num %= 10; }
    if ($num > 0)         { $words .= $ones[(int)$num]." "; }
    return trim($words);
  };
@endphp

@foreach($itemPages as $pageIndex => $pageItems)
  @php
    $isLastPage = $loop->last;
    $serialNo = ($pageIndex * 13) + 1;
    $summaryLeftColspan = $showPercentPricing ? 6 : 4;
    $summaryTotalColspan = $showPercentPricing ? 9 : 7;
  @endphp

  <div class="page invoice-page">
    <div class="watermark">OHT</div>

    <div class="letterhead">
      <h1 class="{{ $isShopCustomer ? 'memo-title' : '' }}">{{ $isShopCustomer ? $memoType : 'OHT' }}</h1>
      @if($customerType === 'customer')
        <div class="business-name">OHT</div>
      @endif
    </div>

    @if($customerType !== 'shop')
      <div class="address-info">
        <p><strong>Address:</strong> Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p><strong>Mobile No:</strong> 9860378262, 9848448624, 9812656284</p>
      </div>
    @endif

    <div class="invoice-info clearfix">
      <div class="firstdiv">
        @if(isset($forinvoicetype) && !empty($forinvoicetype))
          @if($forinvoicetype->invoicetype == 'credit')
            <p style="background:#000;color:#fff;padding:6px 10px;font-size:16px;">Invoice Type: {{ $forinvoicetype->invoicetype }}</p>
          @else
            <p>Invoice Type: {{ $forinvoicetype->invoicetype }}</p>
          @endif

          <p class="date-line">Date: {{ $forinvoicetype->date }} {{ $invoiceTime }}</p>

          <p class="label-nep miti-line">
            Miti: {{ \App\Support\NepaliDate::adToBsString($forinvoicetype->date ?? now()->toDateString(), 'np') }}
          </p>
        @endif
      </div>

      <div class="forbillandpan">
        <div class="invoice-no">INVOICE NO: <span class="num">{{ $invoiceid }}</span></div>
      </div>

      <div class="seconddiv forfontsizebll">
        @if ($cinfodetails)
          @foreach($cinfodetails as $i)
            <p>Name: {{ $i->name }}</p>
            <p>Address: {{ $i->address }}</p>
            <p>Email: {{ $i->email }}</p>
            <p>Contact No: {{ $i->phoneno }}, {{ $i->alternate_phoneno }}</p>
          @endforeach
        @endif

        @if ($invoice)
          <p>Customer Id: {{ $invoice->customerid }}</p>
        @endif
      </div>
    </div>

    <div class="page-count">Page {{ $pageIndex + 1 }} of {{ $totalPages }}</div>

    <div class="table-container">
      <table class="{{ $showPercentPricing ? 'percent-pricing-table' : '' }}">
        @if($showPercentPricing)
          <colgroup>
            <col style="width:4%;">
            <col style="width:6%;">
            <col style="width:30%;">
            <col style="width:11%;">
            <col style="width:10%;">
            <col style="width:8%;">
            <col style="width:6%;">
            <col style="width:12%;">
            <col style="width:13%;">
          </colgroup>
        @endif
        <thead>
          <tr>
            <th>#</th>
            <th>ITEM ID</th>
            <th>ITEM Name</th>
            @if($showPercentPricing)
              <th>MRP</th>
              <th>Discount</th>
            @endif
            <th>Quantity</th>
            <th>Unit</th>
            <th>{{ $showPercentPricing ? 'Net Price' : 'Sold Price' }}</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($pageItems as $i)
            <tr>
              <td class="center-cell">{{ $serialNo++ }}</td>
              <td class="center-cell">{{ $i->itemidorg }}</td>
              <td class="{{ $showPercentPricing ? 'item-name-cell' : '' }}">{{ $i->itemid }}</td>
              @if($showPercentPricing)
                <td class="numeric-cell">{{ $i->list_price ?? $i->mrp ?? '' }}</td>
                <td class="numeric-cell">{{ number_format((float) ($i->discount_percent ?? 0), 2) }} %</td>
              @endif
              <td class="center-cell">{{ $i->quantity }}</td>
              <td class="nep center-cell">{{ $i->unit }}</td>
              <td class="numeric-cell">{{ $i->price }}</td>
              <td class="numeric-cell">{{ $i->subtotal }}</td>
            </tr>
          @endforeach

          @if($isLastPage && $invoice)
            <tr>
              <td colspan="{{ $summaryLeftColspan }}"></td>
              <td colspan="2" class="summary-label">Sub-Total:</td>
              <td class="summary-value"><b>{{ $invoice->subtotal }}</b></td>
            </tr>
            <tr>
              <td colspan="{{ $summaryLeftColspan }}">
                <p style="font-size:13px;text-align:left;"># Goods once sold won't be returned</p>
              </td>
              <td colspan="2" class="summary-label">E-Discount:</td>
              <td class="summary-value">{{ $invoice->discount }}</td>
            </tr>
            <tr>
              <td colspan="{{ $summaryLeftColspan }}" class="amount-words-cell">
                <b>Amount in Words: </b>
                {{ $amountToWords($invoice->total) }} only/-
              </td>
              <td colspan="2" class="summary-label">Total Amount:</td>
              <td class="summary-value">{{ $invoice->total }}</td>
            </tr>
            <tr>
              <td colspan="{{ $summaryTotalColspan }}" class="notes" style="text-align:left"><b>Notes:</b> {{ $invoice->notes }}</td>
            </tr>
          @elseif(!$isLastPage)
            <tr>
              <td colspan="{{ $summaryTotalColspan }}" class="continuation-note">Continued on next page...</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    @if($isLastPage && $invoice)
      <br>
      <p>Bill Created_by: {{ $invoice->added_by }}</p>
      <p style="font-size:13px;">Printed Time and Date:
        <span style="color:#4b4b4b;">{{ date('Y-m-d H:i:s') }}</span>
      </p>
    @endif
  </div>
@endforeach
</body>
</html>
