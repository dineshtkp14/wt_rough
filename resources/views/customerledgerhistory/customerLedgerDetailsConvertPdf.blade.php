<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Print</title>

  @php
    $nepR = str_replace('\\','/', public_path('fonts/Hind-Regular.ttf'));                 // Nepali
    $engR = str_replace('\\','/', public_path('fonts/NotoSans_Condensed-Regular.ttf'));  // English
  @endphp

  <style>
    /* -------- Fonts (filesystem paths for Dompdf) -------- */
    @font-face { font-family:'HindDevanagari';  src:url('file://{{ $nepR }}') format('truetype');  font-weight:normal; font-style:normal; }
    @font-face { font-family:'NotoSansEnglish'; src:url('file://{{ $engR }}') format('truetype');  font-weight:normal; font-style:normal; }

    /* -------- Real page margins on all sides -------- */
    @page {
  size: A5 portrait;        /* or A4, etc. */
  margin: 50px !important;             /* same on all sides */
}
    html, body{
      font-family: 'NotoSansEnglish','HindDevanagari',sans-serif;
      margin:0 !important;
      padding:0 !important;         /* leave body with no padding */
      line-height:1.14;
      font-size:16px;
    }
    *{ box-sizing:border-box; }
    p{ margin:0 0 2px 0; line-height:1.14; }

    /* Outer wrapper (no extra padding; page margins handle spacing) */
    .container{ margin:0; padding:0; background:#fff; }

    /* Header */
    .letterhead{ color:#000; padding:0 0 8px; text-align:center; }
    .letterhead h1{ margin:0 0 6px; font-size:34px; text-decoration:underline; line-height:1.05; }

    .address-info{ font-size:15px; text-align:center; margin-top:6px; }
    .address-info p{ margin:2px 0; }

    .invoice-info{ font-size:15px; margin-top:8px; }
    .invoice-info p{ margin:2px 0; }

    /* Right block (Invoice Type / Date / Miti) – slight lift */
    .firstdiv{
      float:right;
      margin-top:-14px !important;   /* tweak -10 .. -22 to taste */
    }

    .seconddiv{ margin-top:-10px !important; }

    /* Nepali runs */
    .nep, .label-nep{ font-family:'HindDevanagari',sans-serif; line-height:1.16; }
    .label-nep{ display:inline-block; padding-left:3px; } /* avoids matra clipping */

    /* INVOICE NO / PAN block */
    .forbillandpan{
      margin-top:-58px !important;   /* adjust if you nudge the right block */
      line-height:1.14;
    }
    .invoice-no{
      font-size:20px;
      font-weight:700;               /* Dompdf will synthesize bold if only regular font is embedded */
      letter-spacing:.3px;
      margin-bottom:2px;
    }
    .invoice-no .num{ font-weight:800; }
    .pan-line{ font-size:15px; margin-top:0; }

    /* Table */
    table{
      width:100%; border-collapse:collapse; margin-top:10px;
      font-size:20px;
    }
    th,td{
      border:1px solid #000;
      padding:2px 5px;
      height:22px;
      line-height:1.12;
      vertical-align:middle;
      text-align:center;
    }
    th{ font-weight:700; }

    .text-right{ text-align:right; }
    .notes{ margin-top:10px; font-size:14px; line-height:1.14; }
    .forfontsizebll p{ font-size:17px !important; line-height:1.14; }

    /* Watermark */
    .watermark{
      position:fixed; top:45%; left:35%;
      transform:rotate(-45deg);
      font-size:120px; opacity:.1; color:gray;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="watermark">OHT</div>

  <div class="letterhead">
    <h1>OM HARI TRADELINK</h1>
  </div>

  <div class="address-info">
    <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
    <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
  </div>

  <div class="invoice-info">
    <div class="row">
      <div class="firstdiv">
        @if(isset($forinvoicetype) && !empty($forinvoicetype))
          @if($forinvoicetype->invoicetype == 'credit')
            <p style="background:#000;color:#fff;padding:6px 10px;font-size:16px;">Invoice Type: {{ $forinvoicetype->invoicetype }}</p>
          @else
            <p>Invoice Type: {{ $forinvoicetype->invoicetype }}</p>
          @endif

          <p>Date: {{ $forinvoicetype->date }}</p>
          <!-- If you prefer Nepali label, replace 'Miti' with: म&#x093F;&#x200C;ति -->
          <p class="label-nep">
            Miti: {{ \App\Support\NepaliDate::adToBsString($forinvoicetype->date ?? now()->toDateString(), 'np') }}
          </p>
        @endif
      </div>

      <div class="forbillandpan">
        <div class="invoice-no">
          INVOICE NO: <span class="num">{{ $invoiceid }}</span>
        </div>

        @if ($allinvoices)
          @foreach($allinvoices as $i)
            @if ($i->total < 19900)
              <div class="pan-line">PAN No. 608641838</div>
            @endif
          @endforeach
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

        @if ($allinvoices)
          @foreach($allinvoices as $i)
            <p>Customer Id: {{ $i->customerid }}</p>
          @endforeach
        @endif
      </div>
    </div>
  </div>

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
        @php $serialNo = 1; @endphp

        @if ($allcusbyid)
          @foreach($allcusbyid as $i)
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
        @endif

        @if ($allinvoices)
          @foreach($allinvoices as $i)
            <tr>
              <td colspan="5"></td>
              <td class="text-right"><b>Sub-Total:</b></td>
              <td><b>{{ $i->subtotal }}</b></td>
            </tr>
            <tr>
              <td colspan="5">
                <p style="font-size:14px;text-align:left;"># Goods once sold won't be returned</p>
              </td>
              <td class="text-right">E-Discount:</td>
              <td>{{ $i->discount }}</td>
            </tr>
            <tr>
              <td colspan="5" style="font-size:15px;text-align:left;">
                <b>Amount in Words: </b>
                @php
                  function convertNumberToWords($num) {
                      $ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
                      $tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
                      if ($num == 0) return "Zero";
                      $words = "";
                      if ($num >= 10000000) { $words .= convertNumberToWords(floor($num/10000000))." Crore "; $num %= 10000000; }
                      if ($num >= 100000)   { $words .= convertNumberToWords(floor($num/100000))." Lakh ";  $num %= 100000; }
                      if ($num >= 1000)     { $words .= convertNumberToWords(floor($num/1000))." Thousand "; $num %= 1000; }
                      if ($num >= 100)      { $words .= convertNumberToWords(floor($num/100))." Hundred ";  $num %= 100; }
                      if ($num >= 20)       { $words .= $tens[floor($num/10)]." "; $num %= 10; }
                      if ($num > 0)         { $words .= $ones[(int)$num]." "; }
                      return trim($words);
                  }
                  echo convertNumberToWords($i->total) . " only/-";
                @endphp
              </td>
              <td class="text-right"><b>Total Amount:</b></td>
              <td>{{ $i->total }}</td>
            </tr>
            <tr>
              <td colspan="7" class="notes" style="text-align:left"><b>Notes:</b> {{ $i->notes }}</td>
            </tr>
          @endforeach
        @endif
      </tbody>
    </table>
  </div>

  <br>
  @if ($allinvoices)
    @foreach($allinvoices as $i)
      <p>Bill Created_by: {{ $i->added_by }}</p>
      <p style="font-size:14px;">Printed Time and Date:
        <span style="color:#4b4b4b;">{{ date('Y-m-d H:i:s') }}</span>
      </p>
    @endforeach
  @endif
</div>
</body>
</html>
