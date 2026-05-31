<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Customer Credit List</title>
    <style>
        body {
            color: #111827;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            margin: 0;
        }

        .header {
            margin-bottom: 14px;
            text-align: center;
        }

        h2 {
            margin: 0 0 6px;
            font-size: 20px;
            text-transform: uppercase;
        }

        .meta {
            margin: 2px 0;
            color: #374151;
            font-size: 12px;
        }

        .summary {
            margin: 10px 0 12px;
            font-size: 14px;
            font-weight: bold;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #111827;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #e5e7eb;
            font-size: 11px;
            text-align: left;
            text-transform: uppercase;
        }

        .sn { width: 5%; }
        .name { width: 20%; }
        .type { width: 9%; }
        .address { width: 21%; }
        .contact { width: 15%; }
        .date { width: 15%; }
        .amount {
            width: 15%;
            text-align: right;
        }

        .muted {
            color: #4b5563;
            display: block;
            font-size: 10px;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>All Customer Credit List</h2>
        <p class="meta">Selected List: {{ $filterLabel }}</p>
        <p class="meta">Printed Date and Time: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary">
        Total Due Amount: Rs {{ number_format($totalDue, 2) }}<br>
        Advance Deposit: Rs {{ number_format($totalAdvanceDeposit ?? 0, 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="sn">#</th>
                <th class="name">Name</th>
                <th class="type">Type</th>
                <th class="address">Address</th>
                <th class="contact">Contact No</th>
                <th class="date">Last Activity</th>
                <th class="amount">Total Due Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td class="sn">{{ $loop->iteration }}</td>
                    <td class="name">
                        <strong>{{ $customer->name ?? '-' }}</strong>
                        <span class="muted">ID: {{ $customer->id }}</span>
                    </td>
                    <td class="type">{{ ucfirst($customer->type ?? '-') }}</td>
                    <td class="address">{{ $customer->address ?? '-' }}</td>
                    <td class="contact">
                        <strong>{{ $customer->phoneno ?? '-' }}</strong>
                        @if(!empty($customer->alternate_phoneno) && $customer->alternate_phoneno !== $customer->phoneno)
                            <span class="muted">{{ $customer->alternate_phoneno }}</span>
                        @endif
                    </td>
                    <td class="date">
                        <strong>{{ $customer->latest_date ?? '-' }}</strong>
                        @if(!empty($customer->latest_date))
                            <span class="muted">{{ \Carbon\Carbon::parse($customer->latest_date)->diffInDays(now()) }} days ago</span>
                        @endif
                        @if(!empty($customer->credit_limit_days))
                            <span class="muted">Limit: {{ $customer->credit_limit_days }} days</span>
                        @endif
                    </td>
                    <td class="amount">Rs {{ number_format((float) $customer->total_due, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No credit customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
