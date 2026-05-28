<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temporary Invoice #{{ $temporaryinvoice->invoice_number ?? $temporaryinvoice->id }}</title>
    <style>
        body {
            color: #111;
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 24px;
        }

        .invoice {
            max-width: 850px;
            margin: 0 auto;
        }

        .header {
            border-bottom: 2px solid #111;
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 12px;
        }

        h1 {
            font-size: 24px;
            margin: 0 0 6px;
        }

        .muted {
            color: #555;
            font-size: 13px;
        }

        .grid {
            display: grid;
            gap: 8px 24px;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 18px;
        }

        table {
            border-collapse: collapse;
            margin-top: 10px;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        .text-end {
            text-align: right;
        }

        .total-row {
            font-size: 16px;
            font-weight: bold;
        }

        .notes {
            border: 1px solid #ccc;
            margin-top: 18px;
            min-height: 60px;
            padding: 10px;
        }

        .actions {
            margin: 0 auto 16px;
            max-width: 850px;
            text-align: right;
        }

        .print-btn {
            background: #198754;
            border: 0;
            color: white;
            cursor: pointer;
            font-size: 15px;
            padding: 8px 14px;
        }

        @media print {
            body {
                margin: 0;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="actions">
        <button class="print-btn" onclick="window.print()">Print</button>
    </div>

    <div class="invoice">
        <div class="header">
            <div>
                <h1>Temporary Invoice</h1>
                <div class="muted">Invoice No: {{ $temporaryinvoice->invoice_number ?? $temporaryinvoice->id }}</div>
            </div>
            <div class="text-end">
                <div><b>Date:</b> {{ $temporaryinvoice->invoice_date }}</div>
                <div class="muted">Created by: {{ $temporaryinvoice->added_by }}</div>
            </div>
        </div>

        <div class="grid">
            <div><b>Customer Name:</b> {{ $temporaryinvoice->customer_name }}</div>
            <div><b>Contact No:</b> {{ $temporaryinvoice->contact_number }}</div>
            <div><b>Address:</b> {{ $temporaryinvoice->customer_address }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 45px;">#</th>
                    <th>Item</th>
                    <th style="width: 110px;">Quantity</th>
                    <th style="width: 90px;">Unit</th>
                    <th style="width: 110px;">Rate</th>
                    <th style="width: 130px;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($temporaryinvoice->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="text-end">{{ number_format($item->price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Subtotal</th>
                    <th class="text-end">{{ number_format($temporaryinvoice->subtotal, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-end">Discount</th>
                    <th class="text-end">{{ number_format($temporaryinvoice->discount, 2) }}</th>
                </tr>
                <tr class="total-row">
                    <th colspan="5" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($temporaryinvoice->total, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        @if ($temporaryinvoice->notes)
            <div class="notes">
                <b>Notes:</b><br>
                {{ $temporaryinvoice->notes }}
            </div>
        @endif
    </div>
</body>

</html>
