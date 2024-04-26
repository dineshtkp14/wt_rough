<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        /* Add your CSS styles here */
        /* Example: */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .letterhead {
            text-align: center;
            margin-bottom: 20px;
        }
        .letterhead h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
            text-decoration: underline;
        }
        .address-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .address-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black; /* Update border to black */
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-due {
            font-size: 20px;
            color: #ff5733; /* Adjust color as needed */
            margin-top: 20px;
        }
        .printed-info {
            font-size: 12px;
            color: #888;
            text-align: right;
        }
        @page{
            margin:40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="letterhead">
            <h1>OM HARI TRADELINK</h1>
        </div>
        <div class="address-info">
            <p style="font-size: 16px;">Address: Tikapur, Kailali (in front of Tikapur Police Station)</p> <!-- Decrease font size -->
            <p style="font-size: 16px;">Mobile No: 9860378262, 9848448624, 9812656284</p> <!-- Decrease font size -->
        </div>

          <center>  <h4 class="text-danger my-5 bold"> Date: ({{$from}}  To  {{$to}})</h4></center>

        <!-- Company Information -->
        @foreach ($xx as $i)
        <div>
            Company Id: {{$i->id}}<br> 
            Name: {{$i->name}}<br> 
            Address: {{$i->address}}<br>
            Phone No: {{$i->phoneno}}<br>
            Email: {{$i->email}}<br>
        </div>
        @endforeach

        <div style="float: right; margin-top:-100px;">
        <div class="total-due">
            Total Due Amount: <span style="text-decoration: underline;">{{ $dts - $cts }} /-</span>
        </div>
    </div>
        <!-- Invoice Table -->
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th style="width:200px;">Date</th>
                    <th>Particulars</th>
                    <th>Voucher Type</th>
                    <th>Bill No</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
            </thead>
            <tbody>
                @if($all != null)
                    @foreach ($all as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td style="width:200px;">{{ $i->created_at }}</td>
                        <td>{{ $i->particulars }}</td>
                        <td>{{ $i->voucher_type }}</td>
                        <td>{{ $i->voucher_no }}</td>
                        <td>{{ $i->debit }}</td>
                        <td>{{ $i->credit }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5"></td>
                        <td>@if($dts != null) Total: <strong>{{ $dts }}</strong></td> @endif
                        <td>@if($cts != null) Total: <strong>{{ $cts }}</strong></td> @endif
                    </tr>
                @else
                    <tr>
                        <td colspan="7">Record Not Found</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Total Due Amount -->
        <div class="total-due">
            Total Due Amount: <span style="text-decoration: underline;">{{ $dts - $cts }} 
              ( 
                 @php
                function convertNumberToWords($num) {
      $ones = array(
          "", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten",
          "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"
      );
      $tens = array(
          "", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"
      );
  
      // Handle negative numbers
      if ($num < 0) {
          return "Minus " . convertNumberToWords(abs($num));
      }
  
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
          $words .= $ones[$num] . " ";
      }
  
      return $words;
  }
  
  // Retrieve the numerical value from your data
  $number = $dts - $cts;
  // Convert the numerical value to words
  $words = convertNumberToWords($number);
  
  echo $words;
  
              @endphp
              only -/ 
              )
              
            </span>
        </div>

        <!-- Printed Info -->
        <div class="printed-info">
            Printed on: {{ now()->format('Y-m-d H:i:s') }}
        </div>
    </div>
</body>
</html>
