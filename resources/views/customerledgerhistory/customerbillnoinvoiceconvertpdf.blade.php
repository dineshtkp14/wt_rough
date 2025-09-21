<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Print</title>

  @php
    // Absolute filesystem paths for Dompdf
    $nepR = str_replace('\\','/', public_path('fonts/Hind-Regular.ttf'));                  // Nepali (works well with Dompdf)
    $nepB = str_replace('\\','/', public_path('fonts/Hind-Bold.ttf'));                    // Optional (if present)
    $engR = str_replace('\\','/', public_path('fonts/NotoSans_Condensed-Regular.ttf'));   // English
    $engB = str_replace('\\','/', public_path('fonts/NotoSans_Condensed-Bold.ttf'));      // Optional (if present)
  @endphp

  <style>
    /* -------- Fonts (filesystem URLs for Dompdf) -------- */
    @font-face{
      font-family:'HindDevanagari';
      src:url('file://{{ $nepR }}') format('truetype');
      font-weight:normal; font-style:normal;
    }
    @font-face{
      font-family:'HindDevanagari';
      src:url('file://{{ $nepB }}') format('truetype');
      font-weight:bold; font-style:normal;
    }
    @font-face{
      font-family:'NotoSansEnglish';
      src:url('file://{{ $engR }}') format('truetype');
      font-weight:normal; font-style:normal;
    }
    @font-face{
      font-family:'NotoSansEnglish';
      src:url('file://{{ $engB }}') format('truetype');
      font-weight:bold; font-style:normal;
    }

    /* -------- Page & global spacing -------- */
    @page {
      size: A5 portrait;
      margin: 30px;
    }
    html, body{
      font-family: 'NotoSansEnglish','HindDevanagari',sans-serif;
      margin: 0 !important;
      padding: 0 !important;
      line-height: 1.15; /* tighter than default */
    }
    * { box-sizing: border-box; }

    /* Paragraphs – remove big default margins */
    p { margin: 0 0 2px 0; line-height: 1.15; }

    /* Sections */
    .container { margin: 0 auto; padding: 20px; background:#fff; }
    .letterhead { color:#000; padding: 0 20px 10px; text-align:center; }
    .letterhead h1 { margin: 0 0 6px; font-size: 30px; text-decoration: underline; line-height:1.05; }

    .address-info p,
    .invoice-info p { margin: 2px 0; line-height:1.15; }

    .firstdiv { float: right; }
    .seconddiv { margin-top:-24px !important; } /* a bit less than before */

    /* Table – compact rows */
    table { width:100%; border-collapse: collapse; margin-top: 14px; font-size: 20px; }
    th, td {
      border: 1px solid #000;
      padding: 1px 4px;          /* tighter cell padding */
      line-height: 1.12;         /* compact row height */
      vertical-align: middle;
      text-align: center;
    }
    th { background:#fff; }

    .text-right { text-align:right; }
    .notes { margin-top:12px; max-height:100px; overflow:hidden; font-size:14px; }

    .forfontsizebll p { font-size: 16px !important; line-height:1.15; }
    .forbillandpan { margin-top: -60px !important; }

    /* Nepali runs – tiny padding to avoid matra clipping */
    .nep { font-family:'HindDevanagari',sans-serif; line-height:1.16; }
    .label-nep { font-family:'HindDevanagari',sans-serif; display:inline-block; padding-left:3px; line-height:1.16; }

    /* Watermark */
    .watermark {
      position: fixed; top: 45%; left: 35%;
      transform: rotate(-45deg);
      font-size: 148px; opacity: 0.1; color: gray;
    }
  </style>
</head>
<body>

<div class="container">
    <div class="watermark">OHT</div> <!-- Watermark text -->


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
                        <p style="background-color: black; color: white; padding:10px;font-size:18px !important;">Invoice Type: {{ $forinvoicetype->invoicetype }}</p>
                    @else
                        <p>Invoice Type: {{ $forinvoicetype->invoicetype }}</p>
                    @endif
                    <p>Date: {{ $forinvoicetype->date }}</p>

                    <p style="font-family:'HindDevanagari','NotoSansDevanagari',sans-serif; display:inline-block; padding-left:3px">
                        {{ \App\Support\NepaliDate::adToBsString($forinvoicetype->date ?? now()->toDateString(), 'np') }}
                      </p>
                      
                      
                                
                       @endif
            
            </div>
       
       <div class="forbillandpan">
        <span style="font-size: 18px;"> INVOICE NO: </span><b>{{$invoiceid}} </b><br>


        @if ($allinvoices != null)
                @foreach($allinvoices as $i)
                    @if ($i->total < 19900)
                        <span style="font-size: 18px;">PAN N0. 608641838</span>
                    @endif
                @endforeach
        @endif

    
           
        </div>
        <BR>-
          
            <div class="seconddiv forfontsizebll"> 
              
                        @if ($cinfodetails !=null)
                            @foreach($cinfodetails as $i)
                                <p>Name: {{$i->name}}</p>
                                <p>Address: {{$i->address}}</p>
                                <p>Email: {{$i->email}}</p>
                                <p>Contact No: {{$i->phoneno}}, {{$i->alternate_phoneno}}</p>
                            @endforeach

                          
                        @endif

                      

                        @if ($allinvoices !=null)
                            @foreach($allinvoices as $i)
                                <p>Customer Id: {{$i->customerid}}</p>
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
                        @if ($allcusbyid != null)
                            @foreach($allcusbyid as $i)
                                <tr>
                                    <td>{{ $serialNo++ }}</td>
                                    <td>{{$i->itemidorg}}</td>
                                    
                                    <td>{{$i->itemid}}</td>
                                
                                    <td>{{$i->quantity}}</td>
                                    <td>{{$i->unit}}</td>
                                    <td>{{$i->price}}</td>
                                    <td>{{$i->subtotal}}</td>
                                </tr>
                            @endforeach
                        @endif

                        @if ($allinvoices != null)
                            @foreach($allinvoices as $i)
                                <tr>
                                    <td colspan="5"></td>
                                    <td class="text-right"><b>Sub-Total:</b></td>
                                    <td class="p-4"><b>{{$i->subtotal}}</b></td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <p style=" font-size: 14px; text-align: left;">#  Goods once sold won't be returned</p>

                                    </td>
                                    <td class="text-right">E-Discount:</td>
                                    <td>{{$i->discount}}</td>
                                </tr>
                                <tr>
                                    
                                    <td colspan="5" class="" style="font-size: 14px; text-align: left; margin-left: 2px;"><b>Amount in Words: </b>
                                        @php
                                            function convertNumberToWords($num) {
                                                $ones = array(
                                                    "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
                                                    "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"
                                                );
                                                $tens = array(
                                                    "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
                                                );

                                                if ($num == 0) {
                                                    return "Zero";
                                                }

                                                $words = "";

                                                if ($num >= 10000000) {
                                                    $words .= convertNumberToWords(floor($num / 10000000)) . " Crore ";
                                                    $num %= 10000000;
                                                }

                                                if ($num >= 100000) {
                                                    $words .= convertNumberToWords(floor($num / 100000)) . " Lakh ";
                                                    $num %= 100000;
                                                }

                                                if ($num >= 1000) {
                                                    $words .= convertNumberToWords(floor($num / 1000)) . " Thousand ";
                                                    $num %= 1000;
                                                }

                                                if ($num >= 100) {
                                                    $words .= convertNumberToWords(floor($num / 100)) . " Hundred ";
                                                    $num %= 100;
                                                }

                                                if ($num >= 20) {
                                                    $words .= $tens[floor($num / 10)] . " ";
                                                    $num %= 10;
                                                }

                                                if ($num > 0) {
                                            $words .= $ones[(int)$num] . " ";
                                        }

                                                return $words;
                                            }

                                            // Retrieve the numerical value from your data
                                            $number = $i->total;

                                            // Convert the numerical value to words
                                            $words = convertNumberToWords($number);

                                            echo $words;
                                        @endphp
                                    only/-
                                    </td>
                                    <td class="text-right"><b>Total Amount:</b></td>
                                    <td colspan="" id="totalAmountWords">{{$i->total}}</td>

                                </tr>
                                
                                <tr>

                                    <td colspan="7" class="notes" style="text-align: left"><b>Notes:</b> {{$i->notes}}</td>
                                </tr>
                            @endforeach

                            
                        @endif
                    </tbody>
                </table>
                
    </div>
    <br>
    @if ($allinvoices !=null)
    @foreach($allinvoices as $i)
      
      <p>Bill Created_by: {{$i->added_by}} </p>
      <p style="font-size: 14px !important;">Printed Time and Date: <span style="color: #4b4b4b; font-size: 14px;"><?php echo date("Y-m-d H:i:s"); ?></span></p>
    
    @endforeach
@endif
</div>

<script>

        
</script>

</body>
</html>
