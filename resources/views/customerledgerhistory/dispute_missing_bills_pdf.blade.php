<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Ledger Dispute Proof</title>
    <style>
        body { color: #111827; font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; margin: 0; }
        .letterhead { border-bottom: 2px solid #111827; margin-bottom: 12px; padding-bottom: 8px; text-align: center; }
        .letterhead h1 { font-size: 24px; margin: 0; }
        .letterhead p { margin: 2px 0; }
        .title { background: #111827; color: #fff; font-size: 16px; font-weight: 700; margin-bottom: 10px; padding: 8px; text-align: center; }
        .grid { display: table; width: 100%; }
        .col { display: table-cell; padding: 6px; vertical-align: top; width: 50%; }
        .box { border: 1px solid #cbd5e1; margin-bottom: 10px; padding: 8px; }
        .box h3 { font-size: 13px; margin: 0 0 6px; text-transform: uppercase; }
        .stat { font-size: 16px; font-weight: 700; }
        table { border-collapse: collapse; margin-top: 8px; width: 100%; }
        th, td { border: 1px solid #94a3b8; padding: 5px; text-align: left; }
        th { background: #e2e8f0; }
        .right { text-align: right; }
        .missing { background: #fee2e2; color: #991b1b; font-weight: 700; }
        .pill { border: 1px solid #94a3b8; display: inline-block; margin: 2px; padding: 3px 6px; }
        .footer { color: #475569; font-size: 10px; margin-top: 14px; }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
        <p>Address: Tikapur, Kailali (In front of Tikapur Police Station)</p>
        <p>Mobile No: 9860378262, 9848448624, 9812656284</p>
    </div>

    <div class="title">Customer Ledger Dispute / Missing Bills Proof</div>

    <div class="grid">
        <div class="col">
            <div class="box">
                <h3>Customer</h3>
                <div><strong>Name:</strong> {{ $customer->name ?? 'N/A' }}</div>
                <div><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</div>
                <div><strong>Phone:</strong> {{ $customer->phoneno ?? 'N/A' }}</div>
                <div><strong>Customer ID:</strong> {{ $customer->id ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="col">
            <div class="box">
                <h3>Summary</h3>
                <div><strong>Date Range:</strong> {{ $from ?: 'All' }} to {{ $to ?: 'All' }}</div>
                <div><strong>System Invoice Count:</strong> {{ $invoiceNumbers->count() }}</div>
                <div><strong>Customer Brought Count:</strong> {{ $customerNumbers->count() }}</div>
                <div><strong>Missing From Customer:</strong> {{ $missingFromCustomer->count() }}</div>
                <div class="stat">Total Due: Rs {{ number_format((float) $totalDue, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="box">
        <h3>Invoice Numbers Customer Did Not Bring</h3>
        @forelse($missingFromCustomer as $number)
            <span class="pill">{{ $number }}</span>
        @empty
            Customer brought all entered/system invoice numbers, or no customer invoice numbers were entered.
        @endforelse
    </div>

    <div class="box">
        <h3>Customer Gave But Not Found In System</h3>
        @forelse($notInSystem as $number)
            <span class="pill">{{ $number }}</span>
        @empty
            No extra customer-given invoice numbers.
        @endforelse
    </div>

    <h3>System Invoice List</h3>
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Date</th>
                <th>Type</th>
                <th class="right">Total</th>
                <th>Customer Has?</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                @php $customerHas = $customerNumbers->contains((int) $invoice->id); @endphp
                <tr class="{{ $customerNumbers->isNotEmpty() && !$customerHas ? 'missing' : '' }}">
                    <td>{{ $invoice->id }}</td>
                    <td>{{ $invoice->inv_date }}</td>
                    <td>{{ strtoupper($invoice->inv_type ?? '-') }}</td>
                    <td class="right">Rs {{ number_format((float) $invoice->total, 2) }}</td>
                    <td>{{ $customerNumbers->isEmpty() ? '-' : ($customerHas ? 'Yes' : 'Missing') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No invoices found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Printed by {{ session('user_email') }} at {{ now()->format('Y-m-d H:i:s') }}.
    </div>
</body>
</html>
