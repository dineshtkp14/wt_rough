<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Invoices - {{ $customer->name ?? 'Customer' }}</title>
    <style>
        body { font-family: 'Noto Sans', Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 18px; }
        .customer-info { margin-bottom: 20px; padding: 10px; background: #f5f5f5; }
        .invoice-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; page-break-inside: avoid; }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .invoice-header-left { font-weight: bold; }
        .invoice-header-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: left; }
        th { background: #f97316; color: white; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background: #f9fafb; }
        .page-break { page-break-before: always; }
        .date-range { text-align: center; margin-bottom: 20px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h2>ALL INVOICES</h2>
    </div>
    
    <div class="customer-info">
        <strong>Customer:</strong> {{ $customer->name ?? 'N/A' }}<br>
        <strong>Address:</strong> {{ $customer->address ?? 'N/A' }}<br>
        <strong>Contact:</strong> {{ $customer->phoneno ?? 'N/A' }}<br>
        <strong>Customer ID:</strong> {{ $customer->id ?? 'N/A' }}
    </div>
    
    @if($from && $to)
    <div class="date-range">
        Date Range: {{ $from }} to {{ $to }}
    </div>
    @endif
    
    @foreach($invoiceData as $index => $data)
        @if($index > 0)
            <div class="page-break"></div>
        @endif
        
        <div class="invoice-section">
            <div class="invoice-header">
                <div class="invoice-header-left">
                    <strong>INVOICE NO: {{ $data['invoice']->id }}</strong><br>
                    Type: {{ $data['invoice']->inv_type ?? 'credit' }}
                </div>
                <div class="invoice-header-right">
                    <strong>Date:</strong> {{ $data['invoice']->inv_date }}<br>
                    <strong>Miti:</strong> {{ \App\Support\NepaliDate::adToBsString($data['invoice']->inv_date, 'en') }}
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['items'] as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->itemname ?? $item->unstockedname ?? 'N/A' }}</td>
                        <td>{{ $item->nos }}</td>
                        <td>{{ $item->price ?? $item->mrp ?? 0 }}</td>
                        <td class="text-right">{{ $item->subtotal }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td colspan="3"></td>
                        <td class="text-right"><strong>Total:</strong></td>
                        <td class="text-right"><strong>Rs {{ number_format($data['invoice']->total, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
    
    @if(count($invoiceData) == 0)
    <div style="text-align: center; padding: 40px; color: #666;">
        No invoices found for this customer.
    </div>
    @endif
</body>
</html>
