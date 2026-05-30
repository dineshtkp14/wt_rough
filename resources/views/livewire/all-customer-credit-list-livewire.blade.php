<div class="all-credit-list">
    <div class="credit-list-toolbar">
        <div>
            <div class="toolbar-label">Total Due Amount</div>
            <div class="toolbar-total">Rs {{ number_format($totalDue, 2) }}</div>
        </div>
        <div class="toolbar-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" wire:model.debounce.350ms="searchTerm" placeholder="Search name, address, contact no">
        </div>
    </div>

    <div class="credit-list-table-wrap">
        <table class="credit-list-table">
            <thead>
                <tr>
                    <th style="width: 70px;">#</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact No</th>
                    <th style="width: 180px;">Total Due Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>
                        <td>
                            <strong>{{ $customer->name ?? '-' }}</strong>
                            <span class="customer-id">ID: {{ $customer->id }}</span>
                        </td>
                        <td>{{ $customer->address ?? '-' }}</td>
                        <td>
                            <strong>{{ $customer->phoneno ?? '-' }}</strong>
                            @if(!empty($customer->alternate_phoneno) && $customer->alternate_phoneno !== $customer->phoneno)
                                <span class="muted-line">{{ $customer->alternate_phoneno }}</span>
                            @endif
                        </td>
                        <td class="amount">Rs {{ number_format((float) $customer->total_due, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">No credit customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="credit-list-footer">
        <span>Showing {{ $customers->count() }} of {{ $customers->total() }} customers</span>
        {{ $customers->links() }}
    </div>

    <style>
        .all-credit-list {
            color: #172033;
        }

        .credit-list-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
            padding: 18px 20px;
            border-top: 5px solid #0f766e;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 26px rgba(15, 23, 42, .08);
        }

        .toolbar-label {
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .toolbar-total {
            margin-top: 3px;
            color: #0f766e;
            font-size: 30px;
            font-weight: 900;
        }

        .toolbar-search {
            position: relative;
            width: min(440px, 100%);
        }

        .toolbar-search i {
            position: absolute;
            left: 16px;
            top: 50%;
            color: #64748b;
            transform: translateY(-50%);
        }

        .toolbar-search input {
            width: 100%;
            height: 50px;
            padding: 0 16px 0 46px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #111827;
            font-size: 18px;
            outline: none;
        }

        .toolbar-search input:focus {
            border-color: #0f766e;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, .16);
        }

        .credit-list-table-wrap {
            overflow-x: auto;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 26px rgba(15, 23, 42, .08);
        }

        .credit-list-table {
            width: 100%;
            min-width: 860px;
            border-collapse: collapse;
        }

        .credit-list-table th {
            padding: 15px 14px;
            color: #ffffff;
            background: #3348d4;
            border: 1px solid #2637a3;
            font-size: 15px;
            font-weight: 900;
            text-align: left;
            text-transform: uppercase;
        }

        .credit-list-table td {
            padding: 14px;
            border: 1px solid #d5deea;
            font-size: 17px;
            vertical-align: top;
        }

        .credit-list-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .customer-id,
        .muted-line {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .amount {
            color: #0f766e;
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        .empty-row {
            padding: 28px !important;
            color: #64748b;
            font-weight: 800;
            text-align: center;
        }

        .credit-list-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 14px;
            color: #64748b;
            font-weight: 800;
        }

        @media (max-width: 768px) {
            .credit-list-toolbar,
            .credit-list-footer {
                align-items: stretch;
                flex-direction: column;
            }

            .toolbar-total {
                font-size: 24px;
            }
        }
    </style>
</div>
