<div class="all-credit-list">
    <div class="credit-list-toolbar">
        <div>
            <div class="toolbar-label">Total Due Amount</div>
            <div class="toolbar-total">Rs {{ number_format($totalDue, 2) }}</div>
            <div class="toolbar-advance">Advance Deposit: Rs {{ number_format($totalAdvanceDeposit, 2) }}</div>
        </div>
        <div class="toolbar-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" wire:model.debounce.350ms="searchTerm" placeholder="Search name, address, contact no">
        </div>
        <div class="toolbar-filter">
            <select wire:model="sortBy">
                <option value="high_to_low">High to Low</option>
                <option value="low_to_high">Low to High</option>
                <option value="credit_time_expired">Credit Time Expired</option>
                <option value="shop">Shop Only</option>
                <option value="customer">Customer Only</option>
                <option value="advance_deposit">Advance Deposit Only</option>
                <option value="more_than_45_days">More Than 45 Days</option>
                <option value="newest">Newest</option>
                <option value="oldest">Oldest</option>
            </select>
        </div>
        <button type="button" class="toolbar-pdf-btn" wire:click="generatePDF">
            <i class="fas fa-file-pdf"></i>
            <span>Download PDF</span>
        </button>
    </div>

    <div class="credit-list-table-wrap">
        <table class="credit-list-table">
            <colgroup>
                <col class="col-sn">
                <col class="col-name">
                <col class="col-type">
                <col class="col-address">
                <col class="col-contact">
                <col class="col-date">
                <col class="col-amount">
            </colgroup>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Address</th>
                    <th>Contact No</th>
                    <th>Last Activity</th>
                    <th>Total Due Amount</th>
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
                        <td>{{ ucfirst($customer->type ?? '-') }}</td>
                        <td>{{ $customer->address ?? '-' }}</td>
                        <td>
                            <strong>{{ $customer->phoneno ?? '-' }}</strong>
                            @if(!empty($customer->alternate_phoneno) && $customer->alternate_phoneno !== $customer->phoneno)
                                <span class="muted-line">{{ $customer->alternate_phoneno }}</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $customer->latest_date ?? '-' }}</strong>
                            @if(!empty($customer->latest_date))
                                <span class="muted-line">{{ \Carbon\Carbon::parse($customer->latest_date)->diffInDays(now()) }} days ago</span>
                            @endif
                            @if(!empty($customer->credit_limit_days))
                                <span class="muted-line">Limit: {{ $customer->credit_limit_days }} days</span>
                            @endif
                        </td>
                        <td class="amount {{ (float) $customer->total_due < 0 ? 'advance-amount' : '' }}">
                            Rs {{ number_format((float) $customer->total_due, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">No credit customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="credit-list-footer">
        <span>Showing {{ $customers->count() }} of {{ $customers->total() }} customers</span>
        {{ $customers->links() }}
    </div>

    @if($sortBy !== 'advance_deposit' && $advanceCustomers->isNotEmpty())
        <div class="advance-list-panel">
            <div class="advance-list-header">
                <div>
                    <div class="toolbar-label">Advance Deposit Customers</div>
                    <div class="advance-list-total">Rs {{ number_format($totalAdvanceDeposit, 2) }}</div>
                </div>
                <button type="button" class="advance-filter-btn" wire:click="$set('sortBy', 'advance_deposit')">
                    View Only Advance Deposit
                </button>
            </div>

            <div class="advance-table-wrap">
                <table class="advance-table">
                    <colgroup>
                        <col class="advance-col-sn">
                        <col class="advance-col-name">
                        <col class="advance-col-contact">
                        <col class="advance-col-date">
                        <col class="advance-col-amount">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Contact No</th>
                            <th>Last Activity</th>
                            <th>Advance Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($advanceCustomers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $customer->name ?? '-' }}</strong>
                                    <span class="customer-id">ID: {{ $customer->id }}</span>
                                </td>
                                <td>
                                    <strong>{{ $customer->phoneno ?? '-' }}</strong>
                                    @if(!empty($customer->alternate_phoneno) && $customer->alternate_phoneno !== $customer->phoneno)
                                        <span class="muted-line">{{ $customer->alternate_phoneno }}</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $customer->latest_date ?? '-' }}</strong>
                                    @if(!empty($customer->latest_date))
                                        <span class="muted-line">{{ \Carbon\Carbon::parse($customer->latest_date)->diffInDays(now()) }} days ago</span>
                                    @endif
                                </td>
                                <td class="amount advance-amount">Rs {{ number_format(abs((float) $customer->total_due), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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

        .toolbar-advance {
            margin-top: 4px;
            color: #b45309;
            font-size: 17px;
            font-weight: 900;
        }

        .toolbar-search {
            position: relative;
            width: min(440px, 100%);
        }

        .toolbar-filter {
            width: min(260px, 100%);
        }

        .toolbar-pdf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 50px;
            padding: 0 18px;
            border: 0;
            border-radius: 8px;
            background: #dc2626;
            color: #ffffff;
            font-size: 15px;
            font-weight: 900;
            white-space: nowrap;
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

        .toolbar-filter select {
            width: 100%;
            height: 50px;
            padding: 0 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #111827;
            font-size: 16px;
            font-weight: 800;
            outline: none;
        }

        .toolbar-search input:focus,
        .toolbar-filter select:focus {
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
            min-width: 1160px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .credit-list-table .col-sn { width: 72px; }
        .credit-list-table .col-name { width: 240px; }
        .credit-list-table .col-type { width: 110px; }
        .credit-list-table .col-address { width: 260px; }
        .credit-list-table .col-contact { width: 180px; }
        .credit-list-table .col-date { width: 190px; }
        .credit-list-table .col-amount { width: 190px; }

        .credit-list-table thead {
            display: table-header-group;
            width: auto;
        }

        .credit-list-table tbody {
            display: table-row-group;
            max-height: none;
            overflow: visible;
        }

        .credit-list-table tr {
            display: table-row;
            width: auto;
        }

        .credit-list-table th,
        .credit-list-table td {
            display: table-cell;
            box-sizing: border-box;
            width: auto;
            vertical-align: top;
            overflow-wrap: anywhere;
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

        .advance-amount {
            color: #b45309;
        }

        .advance-list-panel {
            margin-top: 24px;
            border-top: 5px solid #b45309;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 26px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .advance-list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .advance-list-total {
            margin-top: 2px;
            color: #b45309;
            font-size: 26px;
            font-weight: 900;
        }

        .advance-filter-btn {
            height: 42px;
            padding: 0 14px;
            border: 1px solid #b45309;
            border-radius: 8px;
            background: #ffffff;
            color: #92400e;
            font-weight: 900;
        }

        .advance-table-wrap {
            overflow-x: auto;
        }

        .advance-table {
            width: 100%;
            min-width: 820px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .advance-table .advance-col-sn { width: 70px; }
        .advance-table .advance-col-name { width: 260px; }
        .advance-table .advance-col-contact { width: 190px; }
        .advance-table .advance-col-date { width: 190px; }
        .advance-table .advance-col-amount { width: 190px; }

        .advance-table thead {
            display: table-header-group;
        }

        .advance-table tbody {
            display: table-row-group;
        }

        .advance-table tr {
            display: table-row;
        }

        .advance-table th,
        .advance-table td {
            display: table-cell;
            border: 1px solid #d5deea;
            padding: 13px 14px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .advance-table th {
            background: #b45309;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            text-align: left;
            text-transform: uppercase;
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
            .advance-list-header,
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
