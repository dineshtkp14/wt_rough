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

    <div class="credit-summary-grid">
        <button type="button" class="credit-summary-tile" wire:click="$set('sortBy', 'high_to_low')">
            <span>Due Customers</span>
            <strong>{{ number_format($creditCustomerCount) }}</strong>
        </button>
        <button type="button" class="credit-summary-tile urgent" wire:click="$set('sortBy', 'credit_time_expired')">
            <span>Credit Time Expired</span>
            <strong>{{ number_format($expiredCustomerCount) }}</strong>
        </button>
        <button type="button" class="credit-summary-tile warning" wire:click="$set('sortBy', 'more_than_45_days')">
            <span>More Than 45 Days</span>
            <strong>{{ number_format($oldDueCustomerCount) }}</strong>
        </button>
        <div class="credit-summary-tile calm">
            <span>Highest Due</span>
            <strong>Rs {{ number_format((float) optional($highestDueCustomer)->total_due, 2) }}</strong>
            <small>{{ optional($highestDueCustomer)->name ?? 'No customer' }}</small>
        </div>
        <div class="credit-summary-tile collection">
            <span>Today Collection</span>
            <strong>Rs {{ number_format((float) $todayCollectionAmount, 2) }}</strong>
            <small>{{ number_format($todayCollectionCount) }} payments</small>
        </div>
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
                <col class="col-actions">
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    @php
                        $dueAmount = (float) $customer->total_due;
                        $latestDate = !empty($customer->latest_date) ? \Carbon\Carbon::parse($customer->latest_date) : null;
                        $creditLimitDate = (!empty($customer->latest_credit_date) && !empty($customer->credit_limit_days))
                            ? \Carbon\Carbon::parse($customer->latest_credit_date)->addDays((int) $customer->credit_limit_days)
                            : null;
                        $isExpired = $creditLimitDate && $creditLimitDate->lt(now()->startOfDay());
                        $isOldDue = $latestDate && $latestDate->lte(now()->subDays(45));
                        $phoneForWhatsapp = preg_replace('/\D+/', '', $customer->phoneno ?? '');
                        if (strlen($phoneForWhatsapp) === 10) {
                            $phoneForWhatsapp = '977' . $phoneForWhatsapp;
                        }
                        $message = rawurlencode('Namaste ' . ($customer->name ?? 'Customer') . ', your remaining due amount is Rs ' . number_format($dueAmount, 2) . '. Please clear it when possible.');
                        $whatsappUrl = $phoneForWhatsapp ? 'https://wa.me/' . $phoneForWhatsapp . '?text=' . $message : '';
                        $customerLabel = trim(($customer->name ?? '') . ' | ' . ($customer->address ?? '') . ' | ' . ($customer->phoneno ?? ''));
                        $lastReminder = !empty($customer->last_credit_reminder_sent_at) ? \Carbon\Carbon::parse($customer->last_credit_reminder_sent_at) : null;
                    @endphp
                    <tr class="{{ $isExpired ? 'is-expired-row' : ($isOldDue ? 'is-old-row' : '') }}">
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
                            @if($lastReminder)
                                <span class="reminder-line {{ $lastReminder->isToday() ? 'sent-today' : '' }}">
                                    Reminder: {{ $lastReminder->isToday() ? 'today' : $lastReminder->format('Y-m-d') }}
                                </span>
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
                            @if($isExpired)
                                <span class="status-badge danger">Expired {{ $creditLimitDate->diffInDays(now()) }} days</span>
                            @elseif($isOldDue)
                                <span class="status-badge warning">Old Due</span>
                            @endif
                        </td>
                        <td class="amount {{ $dueAmount < 0 ? 'advance-amount' : '' }}">
                            Rs {{ number_format($dueAmount, 2) }}
                        </td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('clhs.returnchoosendatehistroy', ['customerid' => $customer->id]) }}" class="action-btn neutral" title="View ledger">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                    <span>Ledger</span>
                                </a>
                                <a href="{{ route('cpayments.create', [
                                    'customerid' => $customer->id,
                                    'amount' => $dueAmount,
                                    'totaldueamountfornotclear' => $dueAmount,
                                    'cname' => $customerLabel,
                                ]) }}" class="action-btn success" title="Receive payment">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                    <span>Pay</span>
                                </a>
                                <a href="{{ route('clhspdf.convert', ['customerid' => $customer->id]) }}" target="_blank" class="action-btn pdf" title="Print statement">
                                    <i class="fa-solid fa-print"></i>
                                    <span>PDF</span>
                                </a>
                                @if($phoneForWhatsapp)
                                    <button type="button" wire:click="markReminderSent({{ $customer->id }}, @js($whatsappUrl))" class="action-btn whatsapp" title="Send WhatsApp reminder">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>WA</span>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-row">No credit customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="credit-mobile-list">
        @forelse($customers as $customer)
            @php
                $dueAmount = (float) $customer->total_due;
                $latestDate = !empty($customer->latest_date) ? \Carbon\Carbon::parse($customer->latest_date) : null;
                $creditLimitDate = (!empty($customer->latest_credit_date) && !empty($customer->credit_limit_days))
                    ? \Carbon\Carbon::parse($customer->latest_credit_date)->addDays((int) $customer->credit_limit_days)
                    : null;
                $isExpired = $creditLimitDate && $creditLimitDate->lt(now()->startOfDay());
                $isOldDue = $latestDate && $latestDate->lte(now()->subDays(45));
                $phoneForWhatsapp = preg_replace('/\D+/', '', $customer->phoneno ?? '');
                if (strlen($phoneForWhatsapp) === 10) {
                    $phoneForWhatsapp = '977' . $phoneForWhatsapp;
                }
                $message = rawurlencode('Namaste ' . ($customer->name ?? 'Customer') . ', your remaining due amount is Rs ' . number_format($dueAmount, 2) . '. Please clear it when possible.');
                $whatsappUrl = $phoneForWhatsapp ? 'https://wa.me/' . $phoneForWhatsapp . '?text=' . $message : '';
                $customerLabel = trim(($customer->name ?? '') . ' | ' . ($customer->address ?? '') . ' | ' . ($customer->phoneno ?? ''));
                $lastReminder = !empty($customer->last_credit_reminder_sent_at) ? \Carbon\Carbon::parse($customer->last_credit_reminder_sent_at) : null;
            @endphp

            <article class="credit-mobile-card {{ $isExpired ? 'is-expired' : ($isOldDue ? 'is-old' : '') }}">
                <div class="mobile-card-head">
                    <div>
                        <strong>{{ $customer->name ?? '-' }}</strong>
                        <span>ID: {{ $customer->id }} | {{ ucfirst($customer->type ?? '-') }}</span>
                    </div>
                    <div class="mobile-card-amount {{ $dueAmount < 0 ? 'advance-amount' : '' }}">
                        Rs {{ number_format($dueAmount, 2) }}
                    </div>
                </div>

                <div class="mobile-card-meta">
                    <div>
                        <span>Address</span>
                        <strong>{{ $customer->address ?? '-' }}</strong>
                    </div>
                    <div>
                        <span>Contact</span>
                        <strong>{{ $customer->phoneno ?? '-' }}</strong>
                        @if(!empty($customer->alternate_phoneno) && $customer->alternate_phoneno !== $customer->phoneno)
                            <small>{{ $customer->alternate_phoneno }}</small>
                        @endif
                    </div>
                    <div>
                        <span>Last Activity</span>
                        <strong>{{ $customer->latest_date ?? '-' }}</strong>
                        @if(!empty($customer->latest_date))
                            <small>{{ $latestDate->diffInDays(now()) }} days ago</small>
                        @endif
                    </div>
                    <div>
                        <span>Reminder</span>
                        <strong class="{{ $lastReminder && $lastReminder->isToday() ? 'mobile-reminder-today' : '' }}">
                            {{ $lastReminder ? ($lastReminder->isToday() ? 'Today' : $lastReminder->format('Y-m-d')) : 'Not sent' }}
                        </strong>
                    </div>
                </div>

                <div class="mobile-card-status">
                    @if($isExpired)
                        <span class="status-badge danger">Expired {{ $creditLimitDate->diffInDays(now()) }} days</span>
                    @elseif($isOldDue)
                        <span class="status-badge warning">Old Due</span>
                    @endif
                    @if(!empty($customer->credit_limit_days))
                        <span class="status-badge neutral">Limit {{ $customer->credit_limit_days }} days</span>
                    @endif
                </div>

                <div class="mobile-card-actions">
                    <a href="{{ route('clhs.returnchoosendatehistroy', ['customerid' => $customer->id]) }}" class="action-btn neutral">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Ledger</span>
                    </a>
                    <a href="{{ route('cpayments.create', [
                        'customerid' => $customer->id,
                        'amount' => $dueAmount,
                        'totaldueamountfornotclear' => $dueAmount,
                        'cname' => $customerLabel,
                    ]) }}" class="action-btn success">
                        <i class="fa-solid fa-money-bill-wave"></i>
                        <span>Pay</span>
                    </a>
                    <a href="{{ route('clhspdf.convert', ['customerid' => $customer->id]) }}" target="_blank" class="action-btn pdf">
                        <i class="fa-solid fa-print"></i>
                        <span>PDF</span>
                    </a>
                    @if($phoneForWhatsapp)
                        <button type="button" wire:click="markReminderSent({{ $customer->id }}, @js($whatsappUrl))" class="action-btn whatsapp">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>WA</span>
                        </button>
                    @endif
                </div>
            </article>
        @empty
            <div class="mobile-empty">No credit customers found.</div>
        @endforelse
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

    @if($showQuickPaymentModal)
        <div class="quick-payment-backdrop">
            <div class="quick-payment-modal">
                <div class="quick-payment-header">
                    <div>
                        <span>Quick Payment</span>
                        <strong>{{ $quickPaymentCustomerName ?: 'Customer' }}</strong>
                    </div>
                    <button type="button" wire:click="closeQuickPayment" class="quick-payment-close" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="quick-payment-body">
                    <div class="quick-payment-due">
                        <span>Current Due</span>
                        <strong>Rs {{ number_format((float) $quickPaymentDueAmount, 2) }}</strong>
                    </div>

                    @error('quickPaymentCustomerId')
                        <div class="quick-payment-error">{{ $message }}</div>
                    @enderror

                    <div class="quick-payment-grid">
                        <label>
                            <span>Amount</span>
                            <input type="number" step="0.01" min="0.01" wire:model.defer="quickPaymentAmount">
                            @error('quickPaymentAmount')
                                <small>{{ $message }}</small>
                            @enderror
                        </label>

                        <label>
                            <span>Date</span>
                            <input type="date" wire:model.defer="quickPaymentDate">
                            @error('quickPaymentDate')
                                <small>{{ $message }}</small>
                            @enderror
                        </label>
                    </div>

                    <div class="quick-payment-mode">
                        <button type="button" wire:click="$set('quickPaymentMode', 'CASH')" class="{{ $quickPaymentMode === 'CASH' ? 'active' : '' }}">
                            <i class="fa-solid fa-money-bill"></i>
                            Cash
                        </button>
                        <button type="button" wire:click="$set('quickPaymentMode', 'FONEPAY')" class="{{ $quickPaymentMode === 'FONEPAY' ? 'active' : '' }}">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                            Fonepay
                        </button>
                        <button type="button" wire:click="$set('quickPaymentMode', 'BANK')" class="{{ $quickPaymentMode === 'BANK' ? 'active' : '' }}">
                            <i class="fa-solid fa-building-columns"></i>
                            Bank
                        </button>
                        <button type="button" wire:click="$set('quickPaymentMode', 'OTHER')" class="{{ $quickPaymentMode === 'OTHER' ? 'active' : '' }}">
                            <i class="fa-solid fa-receipt"></i>
                            Other
                        </button>
                    </div>

                    <label class="quick-payment-notes">
                        <span>Notes</span>
                        <textarea rows="3" wire:model.defer="quickPaymentNotes" placeholder="Optional note"></textarea>
                        @error('quickPaymentNotes')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="quick-payment-check">
                        <input type="checkbox" wire:model.defer="quickPaymentNilAccount">
                        <span>Nil Account / account settled after this payment</span>
                    </label>
                </div>

                <div class="quick-payment-footer">
                    <a href="{{ route('cpayments.create', [
                        'customerid' => $quickPaymentCustomerId,
                        'amount' => $quickPaymentDueAmount,
                        'totaldueamountfornotclear' => $quickPaymentDueAmount,
                        'cname' => $quickPaymentCustomerName,
                    ]) }}" class="quick-payment-full-link">
                        Full Payment Page
                    </a>
                    <button type="button" wire:click="saveQuickPayment" wire:loading.attr="disabled" wire:target="saveQuickPayment" class="quick-payment-save">
                        <i class="fa-solid fa-check"></i>
                        <span wire:loading.remove wire:target="saveQuickPayment">Save Payment</span>
                        <span wire:loading wire:target="saveQuickPayment">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        window.addEventListener('open-whatsapp-reminder', function (event) {
            if (event.detail && event.detail.url) {
                window.open(event.detail.url, '_blank');
            }
        });
    </script>

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

        .credit-summary-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            margin-bottom: 16px;
        }

        .credit-summary-tile {
            min-height: 102px;
            padding: 14px 16px;
            border: 1px solid #d9e2ef;
            border-top: 4px solid #0f766e;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
            text-align: left;
        }

        button.credit-summary-tile {
            cursor: pointer;
        }

        .credit-summary-tile span,
        .credit-summary-tile small {
            display: block;
            color: #64748b;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .credit-summary-tile strong {
            display: block;
            margin-top: 8px;
            color: #172033;
            font-size: 28px;
            font-weight: 900;
            line-height: 1.1;
        }

        .credit-summary-tile small {
            margin-top: 5px;
            text-transform: none;
        }

        .credit-summary-tile.urgent {
            border-top-color: #dc2626;
        }

        .credit-summary-tile.warning {
            border-top-color: #d97706;
        }

        .credit-summary-tile.calm {
            border-top-color: #2563eb;
        }

        .credit-summary-tile.collection {
            border-top-color: #15803d;
        }

        .credit-list-table-wrap {
            overflow-x: auto;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 26px rgba(15, 23, 42, .08);
        }

        .credit-list-table {
            width: 100%;
            min-width: 1390px;
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
        .credit-list-table .col-actions { width: 230px; }

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

        .credit-list-table tbody tr.is-expired-row td {
            background: #fff1f2;
        }

        .credit-list-table tbody tr.is-old-row td {
            background: #fff7ed;
        }

        .customer-id,
        .muted-line,
        .reminder-line {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .reminder-line.sent-today {
            color: #15803d;
            font-weight: 900;
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

        .status-badge {
            display: inline-flex;
            margin-top: 6px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1;
            text-transform: uppercase;
        }

        .status-badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.neutral {
            background: #e0f2fe;
            color: #075985;
        }

        .row-actions {
            display: grid;
            gap: 7px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .action-btn {
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            min-height: 34px;
            padding: 6px 8px;
            border-radius: 6px;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 900;
            line-height: 1.1;
            text-decoration: none !important;
            white-space: nowrap;
        }

        .action-btn.neutral {
            background: #334155;
        }

        .action-btn.success {
            background: #15803d;
        }

        .action-btn.pdf {
            background: #dc2626;
        }

        .action-btn.whatsapp {
            background: #16a34a;
        }

        .credit-mobile-list {
            display: none;
        }

        .credit-mobile-card {
            margin-bottom: 12px;
            border: 1px solid #d8e2ef;
            border-top: 4px solid #0f766e;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
            overflow: hidden;
        }

        .credit-mobile-card.is-expired {
            border-top-color: #dc2626;
            background: #fff7f7;
        }

        .credit-mobile-card.is-old {
            border-top-color: #d97706;
            background: #fffaf3;
        }

        .mobile-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 14px;
            border-bottom: 1px solid #e2e8f0;
        }

        .mobile-card-head strong {
            display: block;
            color: #172033;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.2;
        }

        .mobile-card-head span {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 13px;
            font-weight: 800;
        }

        .mobile-card-amount {
            color: #0f766e;
            flex: 0 0 auto;
            font-size: 17px;
            font-weight: 900;
            text-align: right;
            white-space: nowrap;
        }

        .mobile-card-meta {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            padding: 12px 14px;
        }

        .mobile-card-meta div {
            min-width: 0;
        }

        .mobile-card-meta span {
            display: block;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .mobile-card-meta strong,
        .mobile-card-meta small {
            display: block;
            color: #172033;
            font-size: 14px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .mobile-card-meta small {
            color: #64748b;
            font-size: 12px;
        }

        .mobile-reminder-today {
            color: #15803d !important;
        }

        .mobile-card-status {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            padding: 0 14px 12px;
        }

        .mobile-card-actions {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            padding: 12px 14px 14px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .mobile-empty {
            padding: 22px;
            border-radius: 8px;
            background: #ffffff;
            color: #64748b;
            font-weight: 900;
            text-align: center;
        }

        .quick-payment-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: rgba(15, 23, 42, .62);
        }

        .quick-payment-modal {
            width: min(620px, 100%);
            max-height: calc(100vh - 36px);
            overflow-y: auto;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 28px 70px rgba(15, 23, 42, .35);
        }

        .quick-payment-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px;
            background: #0f766e;
            color: #ffffff;
        }

        .quick-payment-header span {
            display: block;
            color: rgba(255, 255, 255, .78);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .quick-payment-header strong {
            display: block;
            margin-top: 4px;
            font-size: 20px;
            font-weight: 900;
            line-height: 1.2;
        }

        .quick-payment-close {
            width: 36px;
            height: 36px;
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 6px;
            background: rgba(255, 255, 255, .12);
            color: #ffffff;
        }

        .quick-payment-body {
            padding: 18px 20px;
        }

        .quick-payment-due {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 16px;
            padding: 14px 16px;
            border: 1px solid #dbe7f0;
            border-radius: 8px;
            background: #f8fafc;
        }

        .quick-payment-due span,
        .quick-payment-grid label > span,
        .quick-payment-notes > span {
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .quick-payment-due strong {
            color: #0f766e;
            font-size: 24px;
            font-weight: 900;
        }

        .quick-payment-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .quick-payment-grid label,
        .quick-payment-notes {
            display: block;
        }

        .quick-payment-grid input,
        .quick-payment-notes textarea {
            width: 100%;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            color: #111827;
            font-size: 18px;
            outline: none;
        }

        .quick-payment-grid input {
            height: 46px;
            padding: 0 12px;
        }

        .quick-payment-notes textarea {
            min-height: 86px;
            padding: 10px 12px;
            resize: vertical;
        }

        .quick-payment-grid small,
        .quick-payment-notes small,
        .quick-payment-error {
            display: block;
            margin-top: 5px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 800;
        }

        .quick-payment-mode {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin: 16px 0;
        }

        .quick-payment-mode button {
            min-height: 46px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #334155;
            font-size: 14px;
            font-weight: 900;
        }

        .quick-payment-mode button.active {
            border-color: #0f766e;
            background: #0f766e;
            color: #ffffff;
        }

        .quick-payment-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            color: #172033;
            font-size: 15px;
            font-weight: 800;
        }

        .quick-payment-check input {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
        }

        .quick-payment-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .quick-payment-full-link,
        .quick-payment-save {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 900;
            text-decoration: none !important;
        }

        .quick-payment-full-link {
            border: 1px solid #94a3b8;
            background: #ffffff;
            color: #334155 !important;
        }

        .quick-payment-save {
            gap: 7px;
            border: 0;
            background: #15803d;
            color: #ffffff;
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

            .credit-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .credit-list-table-wrap {
                display: none;
            }

            .credit-mobile-list {
                display: block;
            }

            .quick-payment-grid,
            .quick-payment-mode {
                grid-template-columns: 1fr;
            }

            .quick-payment-footer {
                align-items: stretch;
                flex-direction: column;
            }
        }

        @media (max-width: 520px) {
            .credit-summary-grid {
                grid-template-columns: 1fr;
            }

            .mobile-card-meta,
            .mobile-card-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</div>
