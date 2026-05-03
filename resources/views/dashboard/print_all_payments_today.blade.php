<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Payments - {{ $today }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 25mm;
        }

        body {
            font-family: 'Noto Sans', Arial, sans-serif;
            font-size: 18px;
            color: #1f2937;
            line-height: 1.8;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #10b981;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 32px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 18px;
            color: #6b7280;
        }

        .summary {
            display: flex;
            justify-content: space-around;
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #10b981;
        }

        .summary-item {
            text-align: center;
        }

        .summary-label {
            font-size: 16px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .summary-value {
            font-size: 26px;
            font-weight: 700;
            color: #1f2937;
        }

        .summary-value.amount {
            color: #10b981;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 17px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        th {
            background: #10b981;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 16px;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mode-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .mode-cash {
            background: #d1fae5;
            color: #065f46;
        }

        .mode-bank {
            background: #dbeafe;
            color: #1e40af;
        }

        .mode-fonepay {
            background: #fce7f3;
            color: #9d174d;
        }

        .mode-counter {
            background: #fef3c7;
            color: #92400e;
        }

        .mode-default {
            background: #f3f4f6;
            color: #374151;
        }

        .amount-cell {
            font-weight: 600;
            color: #10b981;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 16px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>OM HARI TRADELINK</h1>
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile: 9860378262, 9848448624, 9812566284</p>
        <br>
        <h3>All Payments for Today</h3>
        <p>Date: {{ $today }} | Nepali Date: {{ $nepaliToday }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Payments</div>
            <div class="summary-value">{{ $totalPayments }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Amount Received</div>
            <div class="summary-value amount">Rs {{ number_format($totalAmount, 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th>Receipt No</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>Mode</th>
                <th>Date (Miti)</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $key => $pay)
            @php
                $modeKey = strtolower($pay['mode']);
                $modeClass = match(true) {
                    str_contains($modeKey, 'cash') => 'mode-cash',
                    str_contains($modeKey, 'bank') => 'mode-bank',
                    str_contains($modeKey, 'fonepay') => 'mode-fonepay',
                    str_contains($modeKey, 'counter') => 'mode-counter',
                    default => 'mode-default',
                };
            @endphp
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td><strong>{{ $pay['receipt_no'] }}</strong></td>
                <td>
                    {{ $pay['customer']['name'] }}<br>
                    <small style="color: #6b7280;">ID: {{ $pay['customer']['id'] }}</small>
                </td>
                <td>{{ $pay['customer']['phoneno'] ?? 'N/A' }}</td>
                <td>
                    <span class="mode-badge {{ $modeClass }}">{{ $pay['mode'] }}</span>
                </td>
                <td>{{ $pay['date'] }}<br><small>{{ $pay['nepali_date'] }}</small></td>
                <td class="text-right amount-cell">Rs {{ number_format($pay['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #ecfdf5; font-weight: 700;">
                <td colspan="6" class="text-right" style="font-size: 12px;">TOTAL AMOUNT RECEIVED:</td>
                <td class="text-right" style="color: #10b981; font-size: 14px;">Rs {{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Total {{ $totalPayments }} payments received on {{ $today }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
