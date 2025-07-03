<!DOCTYPE html>
<html>
<head>
    <title>Cash Receipt</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            page-break-inside: avoid;
            border: 1px solid #ccc;
            padding: 20px;
            position: relative;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .sub-header {
            font-size: 14px;
            margin-top: 5px;
            color: #555;
        }

        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            text-decoration: underline;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .info-box {
            width: 48%;
        }

        .info-box h4 {
            margin-bottom: 8px;
            color: #007bff;
        }

        .info-box p {
            margin: 4px 0;
            font-size: 16px;
            word-break: break-word;
        }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-row p {
            font-size: 16px;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
        }

        .total-due {
            background: black;
            color: white;
            padding: 10px;
            margin-top: 15px;
            font-size: 18px;
            text-align: center;
        }

        .amount-box {
            display: inline-block;
            font-size: 32px;
            font-weight: bold;
            border: 2px solid white;
            padding: 5px 15px;
            margin: 0 10px;
        }

        .top-right-info {
            position: absolute;
            top: 20px;
            right: 40px;
            font-size: 14px;
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="watermark" style="position: absolute; top: 35%; left: 30%; transform: rotate(-45deg); font-size: 120px; opacity: 0.1; color: gray; z-index: 0;">
        OHT
    </div>

    <div class="header">
        <h1>OM HARI TRADELINK</h1>
        <div class="sub-header">
            Address: Tikapur, Kailali (in front of Tikapur Police Station)<br>
            Mobile No: 9860378262, 9848448624, 9812656284
        </div>
        <div class="receipt-title">CASH RECEIPT</div>
    </div>

    <div class="top-right-info">
        Receipt No: <strong>7772</strong><br>
        Date: 2025-07-03
    </div>

    <div class="info-row">
        <div class="info-box">
            <h4>RECEIVED FROM</h4>
            <p><strong>Name:</strong> MITTHU BOHARA</p>
            <p><strong>Address:</strong> NAYA TIKAPUR</p>
            <p><strong>Email:</strong> </p>
            <p><strong>Contact No:</strong> 9865741922</p>
        </div>
        <div class="info-box">
            <h4>RECEIPT DETAILS</h4>
            <p><strong>Particulars:</strong> CASH XORA LE</p>
            <p><strong>Voucher Type:</strong> CASH</p>
            <p><strong>Amount:</strong> 5000.00/-</p>
            <p><strong>Amount In Words:</strong> Five Thousand only/-</p>
            <p><strong>Notes:</strong></p>
        </div>
    </div>

    <div class="signature-row">
        <p><strong>Payer's Signature:</strong> __________</p>
        <p><strong>Receiver's Signature:</strong> ________________</p>
    </div>

    <div class="footer">
        Printed Time and Date: <span style="color: #4b4b4b;">2025-07-03 18:47:22</span>
    </div>

    <div class="total-due">
        Total Due Amount:
        <span class="amount-box">2,815.00</span> -/
        <span>( as of the date and time: 2025-07-03 18:47:22 )</span>
    </div>

</div>

</body>
</html>
