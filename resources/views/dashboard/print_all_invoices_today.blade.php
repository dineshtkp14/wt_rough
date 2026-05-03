<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Invoices - {{ $today }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A5 portrait;
            margin: 0;
        }

        body {
            font-family: 'Noto Sans', Arial, sans-serif;
            font-size: 14px;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            border: 15px solid red;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .page {
            padding: 30px;
            background: #fff;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #f97316;
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
            justify-content: space-between;
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #f97316;
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

        .invoice-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }

        .invoice-header {
            background: #f3f4f6;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .invoice-header h3 {
            font-size: 16px;
            color: #374151;
            font-weight: 700;
            background: #fef3c7;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }

        .invoice-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .meta-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        .customer-info {
            line-height: 1.8;
        }

        .invoice-details {
            text-align: right;
            line-height: 1.8;
        }

        .label-highlight {
            background: black;
            padding: 3px 8px;
            color: #f4f2ee;
            border-radius: 4px;
            font-weight: 700;
            display: inline-block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 17px;
        }

        th, td {
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }

        th {
            background: #1f2937;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 16px;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            font-weight: 700;
            background: #f9fafb;
        }

        .notes {
            font-style: italic;
            color: #6b7280;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 16px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            margin-top: 30px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="page">
    <div class="header">
        <h1>OM HARI TRADELINK</h1>
        <p>Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
        <p>Mobile: 9860378262, 9848448624, 9812566284</p>
        <br>
        <h3>All Invoices for Today</h3>
        <p>Date: {{ $today }} | Nepali Date: {{ $nepaliToday }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-label">Total Invoices</div>
            <div class="summary-value">{{ $totalInvoices }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Amount</div>
            <div class="summary-value">Rs {{ number_format($totalAmount, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Paid</div>
            <div class="summary-value" style="color: #065f46;">Rs {{ number_format($totalPaid, 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Pending</div>
            <div class="summary-value" style="color: #92400e;">Rs {{ number_format($totalPending, 2) }}</div>
        </div>
    </div>

    @foreach($invoices as $index => $inv)
    <div class="invoice-section">
        <div class="invoice-header">
            <h3>INVOICE NO: {{ $inv['invoice_no'] }}</h3>
            <span class="invoice-status status-{{ $inv['status'] }}">{{ ucfirst($inv['status']) }}</span>
        </div>

        <table class="meta-table">
            <tr>
                <td class="customer-info">
                    <strong>Customer:</strong> {{ $inv['customer']['name'] }}<br>
                    @if($inv['customer']['address'])
                    <strong>Address:</strong> {{ $inv['customer']['address'] }}<br>
                    @endif
                    @if($inv['customer']['phoneno'])
                    <strong>Contact:</strong> {{ $inv['customer']['phoneno'] }}<br>
                    @endif
                    @if($inv['customer']['pan_no'])
                    <strong>PAN No:</strong> {{ $inv['customer']['pan_no'] }}<br>
                    @endif
                    <strong>Customer ID:</strong> {{ $inv['customer']['id'] }}
                </td>
                <td class="invoice-details">
                    @if(strtolower($inv['type']) == 'credit')
                    <span class="label-highlight"><strong>Invoice Type:</strong> {{ ucfirst($inv['type']) }}</span><br>
                    @else
                    <span><strong>Invoice Type:</strong> {{ ucfirst($inv['type']) }}</span><br>
                    @endif
                    <strong>Date:</strong> {{ $inv['date'] }}<br>
                    <strong>Miti:</strong> {{ $inv['nepali_date'] }}<br>
                    <strong>Created By:</strong> {{ $inv['added_by'] ?? 'System' }}
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inv['items'] as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item['item_name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $item['unit'] }}</td>
                    <td>Rs {{ number_format($item['price'], 2) }}</td>
                    <td class="text-right">Rs {{ number_format($item['subtotal'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4"></td>
                    <td class="text-right">Sub-Total:</td>
                    <td class="text-right">Rs {{ number_format($inv['subtotal'], 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4"></td>
                    <td class="text-right">E-Discount:</td>
                    <td class="text-right">Rs {{ number_format($inv['discount'], 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" class="notes">
                        @if($inv['notes'])
                        <strong>Notes:</strong> {{ $inv['notes'] }}
                        @endif
                    </td>
                    <td class="text-right"><strong>Total:</strong></td>
                    <td class="text-right"><strong>Rs {{ number_format($inv['total'], 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(!$loop->last)
    <div class="page-break"></div>
    @endif
    @endforeach

    <div class="footer">
        <p>Total {{ $totalInvoices }} invoices generated on {{ $today }}</p>
        <p>Goods once sold won't be returned. Thank you for your business!</p>
    </div>
    </div>
</body>
</html>
