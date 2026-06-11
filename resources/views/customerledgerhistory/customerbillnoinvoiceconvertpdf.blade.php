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
    .letterhead h1{ margin:0 0 4px; font-size:30px; text-decoration:underline; line-height:1.04; }

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
  $itemPages = $items->chunk(13);
  if ($itemPages->isEmpty()) {
    $itemPages = collect([collect()]);
  }
  $totalPages = $itemPages->count();
  $invoiceTime = optional($invoice)->created_at
    ? \Carbon\Carbon::parse($invoice->created_at)->format('H:i:s')
    : '';

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
  @endphp

  <div class="page invoice-page">
    <div class="watermark">OHT</div>

    <div class="letterhead">
      <h1>OM HARI TRADELINK</h1>
    </div>

    <div class="address-info">
      <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
      <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div>

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
        @if ($invoice && $invoice->total < 19900)
          <div class="pan-line">PAN No. 608641838</div>
        @endif
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
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>ITEM ID</th>
            <th>ITEM Name</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Sold Price</th>
            <th>Amount</th>
          </tr>
        </thead>
        <tbody>
          @foreach($pageItems as $i)
            <tr>
              <td>{{ $serialNo++ }}</td>
              <td>{{ $i->itemidorg }}</td>
              <td>{{ $i->itemid }}</td>
              <td>{{ $i->quantity }}</td>
              <td class="nep">{{ $i->unit }}</td>
              <td>{{ $i->price }}</td>
              <td>{{ $i->subtotal }}</td>
            </tr>
          @endforeach

          @if($isLastPage && $invoice)
            <tr>
              <td colspan="5"></td>
              <td class="text-right"><b>Sub-Total:</b></td>
              <td><b>{{ $invoice->subtotal }}</b></td>
            </tr>
            <tr>
              <td colspan="5">
                <p style="font-size:13px;text-align:left;"># Goods once sold won't be returned</p>
              </td>
              <td class="text-right">E-Discount:</td>
              <td>{{ $invoice->discount }}</td>
            </tr>
            <tr>
              <td colspan="5" style="font-size:14px;text-align:left;">
                <b>Amount in Words: </b>
                {{ $amountToWords($invoice->total) }} only/-
              </td>
              <td class="text-right"><b>Total Amount:</b></td>
              <td>{{ $invoice->total }}</td>
            </tr>
            <tr>
              <td colspan="7" class="notes" style="text-align:left"><b>Notes:</b> {{ $invoice->notes }}</td>
            </tr>
          @elseif(!$isLastPage)
            <tr>
              <td colspan="7" class="continuation-note">Continued on next page...</td>
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
