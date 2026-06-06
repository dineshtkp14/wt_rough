<!DOCTYPE html>
@php
    $tempAmountToWords = function ($num) use (&$tempAmountToWords) {
        $num = (int) floor($num);
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($num === 0) {
            return 'Zero';
        }

        $words = '';
        if ($num >= 10000000) {
            $words .= $tempAmountToWords(floor($num / 10000000)) . ' Crore ';
            $num %= 10000000;
        }
        if ($num >= 100000) {
            $words .= $tempAmountToWords(floor($num / 100000)) . ' Lakh ';
            $num %= 100000;
        }
        if ($num >= 1000) {
            $words .= $tempAmountToWords(floor($num / 1000)) . ' Thousand ';
            $num %= 1000;
        }
        if ($num >= 100) {
            $words .= $tempAmountToWords(floor($num / 100)) . ' Hundred ';
            $num %= 100;
        }
        if ($num >= 20) {
            $words .= $tens[floor($num / 10)] . ' ';
            $num %= 10;
        }
        if ($num > 0) {
            $words .= $ones[$num] . ' ';
        }

        return trim($words);
    };
@endphp
@php
    $temporaryInvoiceNepaliDate = \App\Support\NepaliDate::adToBsString($temporaryinvoice->invoice_date, 'en');
    $temporaryInvoiceTime = optional($temporaryinvoice->created_at)->format('h:i A');
    $temporaryInvoiceItemDiscount = $temporaryinvoice->items->sum(function ($item) {
        $gross = (float) $item->quantity * (float) $item->price;
        return max(0, $gross - (float) $item->subtotal);
    });
@endphp
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temporary Invoice #{{ $temporaryinvoice->invoice_number ?? $temporaryinvoice->id }}</title>
    <style>
        body {
            color: #111;
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.15;
            margin: 10px;
        }

        .invoice {
            max-width: 560px;
            margin: 0 auto;
        }

        .header {
            border-bottom: 2px solid #111;
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 7px;
        }

        h1 {
            font-size: 21px;
            margin: 0 0 4px;
        }

        .muted {
            color: #555;
            font-size: 11px;
        }

        .grid {
            display: grid;
            gap: 5px 18px;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 10px;
        }

        table {
            border-collapse: collapse;
            margin-top: 6px;
            table-layout: fixed;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 6px;
            text-align: left;
            vertical-align: middle;
            word-break: break-word;
        }

        th {
            background: #f0f0f0;
        }

        .text-end {
            text-align: right;
        }

        .total-row {
            font-size: 14px;
            font-weight: bold;
        }

        .total-row th,
        .total-row td {
            background: #111827;
            color: #ffffff;
        }

        .notes {
            border: 1px solid #ccc;
            margin-top: 10px;
            min-height: 36px;
            padding: 6px;
        }

        .amount-words {
            border: 1px solid #333;
            font-size: 12px;
            margin-top: 6px;
            padding: 5px 6px;
            text-transform: capitalize;
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
            @page {
                size: A5 portrait;
                margin: 6mm;
            }

            body {
                font-size: 11px;
                margin: 0;
            }

            .actions {
                display: none;
            }

            .invoice {
                max-width: none;
                width: 100%;
            }

            th,
            td {
                padding: 3px 5px;
            }

            h1 {
                font-size: 20px;
            }

            .amount-words {
                font-size: 11px;
                padding: 4px 5px;
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
                <div><b>Time:</b> {{ $temporaryInvoiceTime }}</div>
                <div><b>Nepali Date:</b> {{ $temporaryInvoiceNepaliDate }}</div>
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
                    <th style="width: 7%;">#</th>
                    <th>Item</th>
                    <th style="width: 15%;">Quantity</th>
                    <th style="width: 12%;">Unit</th>
                    <th style="width: 14%;">Rate</th>
                    <th style="width: 10%;">Disc %</th>
                    <th style="width: 13%;">Disc Amt</th>
                    <th style="width: 15%;">Amount</th>
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
                        @php
                            $gross = (float) $item->quantity * (float) $item->price;
                            $discountAmount = max(0, $gross - (float) $item->subtotal);
                            $discountPercent = $gross > 0 ? max(0, ($discountAmount / $gross) * 100) : 0;
                        @endphp
                        <td class="text-end">{{ number_format($discountPercent, 2) }}%</td>
                        <td class="text-end">{{ number_format($discountAmount, 2) }}</td>
                        <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-end">Total Discount</th>
                    <th class="text-end">{{ number_format($temporaryInvoiceItemDiscount, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="7" class="text-end">Subtotal</th>
                    <th class="text-end">{{ number_format($temporaryinvoice->subtotal, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="7" class="text-end">Discount</th>
                    <th class="text-end">{{ number_format($temporaryinvoice->discount, 2) }}</th>
                </tr>
                <tr class="total-row">
                    <th colspan="7" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($temporaryinvoice->total, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="amount-words">
            <b>Amount in words:</b> {{ $tempAmountToWords($temporaryinvoice->total) }} only /-
        </div>

        @if ($temporaryinvoice->notes)
            <div class="notes">
                <b>Notes:</b><br>
                {{ $temporaryinvoice->notes }}
            </div>
        @endif
    </div>
</body>

</html>
