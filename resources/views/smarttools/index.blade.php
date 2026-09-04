@extends('layouts.master')

@section('page-css')
    <style>
        .smart-tools {
            min-height: 100vh;
            padding: 22px 22px 32px 300px;
            background: #f6f8fb;
        }

        .smart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .smart-header h2 {
            margin: 0;
            color: #182235;
            font-size: 25px;
            font-weight: 900;
        }

        .smart-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 14px;
            font-weight: 700;
        }

        .smart-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .smart-card {
            border: 1px solid #dbe3ee;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        }

        .smart-card-body {
            padding: 16px;
        }

        .metric-label {
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .metric-value {
            margin-top: 6px;
            color: #111827;
            font-size: 23px;
            font-weight: 900;
            line-height: 1.1;
        }

        .metric-note {
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .smart-section {
            margin-bottom: 16px;
        }

        .smart-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #dbe3ee;
        }

        .smart-section-title h3 {
            margin: 0;
            color: #172033;
            font-size: 17px;
            font-weight: 900;
        }

        .search-row,
        .price-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
        }

        .smart-tools .form-control,
        .smart-tools .form-select {
            min-height: 44px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
        }

        .smart-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 44px;
            padding: 0 16px;
            border: 0;
            border-radius: 8px;
            background: #0f766e;
            color: #ffffff;
            font-weight: 900;
            text-decoration: none;
            white-space: nowrap;
        }

        .smart-btn:hover {
            background: #115e59;
            color: #ffffff;
        }

        .result-list {
            display: grid;
            gap: 8px;
            margin-top: 14px;
        }

        .result-item {
            display: grid;
            grid-template-columns: 110px 1fr auto;
            gap: 12px;
            align-items: center;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            background: #fbfdff;
        }

        .result-item:hover {
            background: #eefaf8;
        }

        .pill {
            display: inline-flex;
            justify-content: center;
            padding: 5px 9px;
            border-radius: 999px;
            background: #ecfeff;
            color: #155e75;
            font-size: 12px;
            font-weight: 900;
        }

        .result-title {
            color: #0f172a;
            font-weight: 900;
        }

        .result-detail {
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
        }

        .smart-table {
            width: 100%;
            min-width: 840px;
            border-collapse: collapse;
        }

        .smart-table th {
            padding: 12px;
            background: #172033;
            color: #ffffff;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .smart-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #172033;
            font-size: 14px;
            font-weight: 700;
            vertical-align: top;
        }

        .audit-table {
            min-width: 760px;
        }

        .audit-time {
            color: #64748b;
            font-size: 12px;
            white-space: nowrap;
        }

        .audit-record {
            font-weight: 900;
        }

        .audit-user {
            color: #0f766e;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .audit-user small {
            display: block;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            margin-top: 3px;
        }

        .audit-details summary {
            color: #2563eb;
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
        }

        .audit-values {
            background: #f8fafc;
            border-left: 3px solid #cbd5e1;
            color: #475569;
            font-size: 11px;
            font-weight: 600;
            margin-top: 7px;
            max-width: 310px;
            padding: 7px 9px;
            white-space: normal;
            word-break: break-word;
        }

        .audit-values strong {
            color: #334155;
        }

        .audit-header-summary {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .audit-summary-pill {
            border-radius: 999px;
            border: 0;
            cursor: pointer;
            font-size: 11px;
            font-weight: 900;
            padding: 5px 9px;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .audit-summary-pill:hover,
        .audit-summary-pill.is-active {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .15);
            transform: translateY(-1px);
        }

        .audit-summary-updated {
            background: #fef3c7;
            color: #92400e;
        }

        .audit-summary-deleted {
            background: #fee2e2;
            color: #b91c1c;
        }

        .audit-row-deleted td {
            background: #fff7f7;
        }

        .audit-row-deleted .audit-record {
            color: #b91c1c;
        }

        .audit-row-deleted .pill {
            background: #fee2e2;
            color: #b91c1c;
        }

        .audit-row-updated .pill {
            background: #fef3c7;
            color: #92400e;
        }

        .audit-row-highlight td {
            background: #dbeafe !important;
            box-shadow: inset 0 2px 0 #2563eb, inset 0 -2px 0 #2563eb;
        }

        .audit-row-deleted.audit-row-highlight td {
            background: #fecaca !important;
            box-shadow: inset 0 2px 0 #dc2626, inset 0 -2px 0 #dc2626;
        }

        .audit-detail-line {
            line-height: 1.35;
            margin-bottom: 3px;
        }

        .audit-change {
            border-bottom: 1px solid #e2e8f0;
            display: grid;
            gap: 2px;
            padding: 5px 0;
        }

        .audit-change:last-child {
            border-bottom: 0;
        }

        .audit-before,
        .audit-after {
            font-size: 11px;
        }

        .audit-before {
            color: #b45309;
        }

        .audit-after {
            color: #047857;
        }

        .status-buy {
            color: #b91c1c;
            font-weight: 900;
        }

        .status-watch {
            color: #a16207;
            font-weight: 900;
        }

        .status-ok {
            color: #047857;
            font-weight: 900;
        }

        @media (max-width: 1100px) {
            .smart-grid {
                grid-template-columns: repeat(2, minmax(160px, 1fr));
            }

        }

        @media (max-width: 768px) {
            .smart-tools {
                padding: 76px 12px 24px;
            }

            .smart-header,
            .search-row,
            .result-item {
                grid-template-columns: 1fr;
            }

            .smart-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    <div class="smart-tools">
        <div class="smart-header">
            <div>
                <h2>Smart Tools</h2>
                <p>Global search, audit log, price guidance, stock prediction, and today's business summary.</p>
            </div>
            <span class="pill">{{ $dailySummary['date'] }}</span>
        </div>

        <div class="smart-grid">
            <div class="smart-card">
                <div class="smart-card-body">
                    <div class="metric-label">Today Sales</div>
                    <div class="metric-value">Rs {{ number_format($dailySummary['sales_total'], 2) }}</div>
                    <div class="metric-note">{{ $dailySummary['invoice_count'] }} invoices</div>
                </div>
            </div>
            <div class="smart-card">
                <div class="smart-card-body">
                    <div class="metric-label">Cash In</div>
                    <div class="metric-value">Rs {{ number_format($dailySummary['cash_sales'] + $dailySummary['payments'], 2) }}</div>
                    <div class="metric-note">Cash sales + payments</div>
                </div>
            </div>
            <div class="smart-card">
                <div class="smart-card-body">
                    <div class="metric-label">Credit Sales</div>
                    <div class="metric-value">Rs {{ number_format($dailySummary['credit_sales'], 2) }}</div>
                    <div class="metric-note">Credit notes: Rs {{ number_format($dailySummary['credit_notes'], 2) }}</div>
                </div>
            </div>
            <div class="smart-card">
                <div class="smart-card-body">
                    <div class="metric-label">Net Cash Hint</div>
                    <div class="metric-value">Rs {{ number_format($dailySummary['net_cash_hint'], 2) }}</div>
                    <div class="metric-note">Expenses: Rs {{ number_format($dailySummary['expenses'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="smart-card smart-section">
            <div class="smart-section-title">
                <h3>Global Search</h3>
            </div>
            <div class="smart-card-body">
                <form method="GET" action="{{ route('smarttools.index') }}" class="search-row">
                    <input class="form-control" name="q" value="{{ $query }}" placeholder="Search invoice no, customer, phone, item, company">
                    <button class="smart-btn" type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
                </form>

                @if ($query)
                    <div class="result-list">
                        @forelse ($searchResults as $result)
                            <a href="{{ $result['url'] }}" class="result-item">
                                <span class="pill">{{ $result['type'] }}</span>
                                <span>
                                    <span class="result-title">{{ $result['title'] }}</span><br>
                                    <span class="result-detail">{{ $result['detail'] }}</span>
                                </span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        @empty
                            <div class="result-detail">No results found.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>

        <div class="smart-card smart-section">
            <div class="smart-section-title">
                <h3>Stock Prediction</h3>
                <span class="pill">Based on last 30 days</span>
            </div>
            <div class="table-wrap">
                <table class="smart-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Stock</th>
                            <th>Sold 30 Days</th>
                            <th>Daily Avg</th>
                            <th>Days Left</th>
                            <th>Status</th>
                            <th>Suggested Buy Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stockPredictions as $prediction)
                            <tr>
                                <td>{{ $prediction['item'] }}</td>
                                <td>{{ number_format($prediction['stock'], 2) }} {{ $prediction['unit'] }}</td>
                                <td>{{ number_format($prediction['sold_30_days'], 2) }}</td>
                                <td>{{ number_format($prediction['daily_average'], 2) }}</td>
                                <td>{{ $prediction['days_left'] === null ? '-' : number_format($prediction['days_left'], 1) }}</td>
                                <td class="{{ $prediction['status'] === 'Buy soon' ? 'status-buy' : ($prediction['status'] === 'Watch' ? 'status-watch' : 'status-ok') }}">
                                    {{ $prediction['status'] }}
                                </td>
                                <td>{{ number_format($prediction['reorder_qty'], 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No stock prediction data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="smart-card smart-section">
            <div class="smart-section-title">
                <h3>Audit Log</h3>
                <div class="audit-header-summary">
                    <button type="button" class="audit-summary-pill audit-summary-updated" data-audit-filter="updated">
                        Updated today: {{ $todayAuditCounts['updated'] }}
                    </button>
                    <button type="button" class="audit-summary-pill audit-summary-deleted" data-audit-filter="deleted">
                        Deleted today: {{ $todayAuditCounts['deleted'] }}
                    </button>
                    <span class="pill">{{ $auditLogs->count() }} recent</span>
                </div>
            </div>
            <div class="table-wrap">
                <table class="smart-table audit-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Activity</th>
                            <th>What changed</th>
                            <th>Who</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $log)
                            <tr class="audit-row-{{ $log->event }}" data-audit-event="{{ $log->event }}">
                                <td class="audit-time">{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '-' }}</td>
                                <td><span class="pill">{{ ucfirst($log->event) }}</span></td>
                                @php
                                    $recordTitle = str_ireplace(
                                        ['customerledgerdetails', 'customerledgerdetail'],
                                        ['Payment', 'Payment'],
                                        (string) $log->title
                                    );
                                @endphp
                                <td class="audit-record">{{ $recordTitle }}</td>
                                <td class="audit-user">
                                    {{ $log->user_name ?? 'System' }}
                                    <small>account used</small>
                                </td>
                                <td>
                                    @php
                                        $newValues = $log->new_values ?? [];
                                        $oldValues = $log->old_values ?? [];
                                        $detailValues = count($newValues) ? $newValues : $oldValues;
                                        $auditLabels = [
                                            'subtotal' => 'Subtotal',
                                            'discount' => 'Discount',
                                            'total' => 'Total',
                                            'inv_type' => 'Invoice type',
                                            'inv_date' => 'Invoice date',
                                            'added_by' => 'Created by',
                                            'updated_by' => 'Updated by',
                                            'deleted_by' => 'Deleted by',
                                            'notes' => 'Notes',
                                            'particulars' => 'Description',
                                            'voucher_type' => 'Payment type',
                                        ];
                                        $hiddenAuditFields = ['id', 'customerid', 'invoiceid', 'created_at', 'updated_at', 'remember_token'];
                                        $displayAuditValue = function ($value) {
                                            if (is_null($value) || $value === '') {
                                                return '—';
                                            }
                                            return is_scalar($value) ? $value : json_encode($value);
                                        };
                                    @endphp
                                    <details class="audit-details">
                                        <summary>Show {{ count($detailValues) }} change{{ count($detailValues) === 1 ? '' : 's' }}</summary>
                                        <div class="audit-values">
                                            @if ($log->event === 'updated')
                                                @foreach ($newValues as $field => $value)
                                                    @if (!in_array($field, $hiddenAuditFields))
                                                        <div class="audit-change">
                                                            <strong>{{ $auditLabels[$field] ?? ucwords(str_replace('_', ' ', $field)) }}</strong>
                                                            <span class="audit-before">Before: {{ $displayAuditValue($oldValues[$field] ?? null) }}</span>
                                                            <span class="audit-after">After: {{ $displayAuditValue($value) }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                @foreach ($detailValues as $field => $value)
                                                    @if (!in_array($field, $hiddenAuditFields))
                                                        <div class="audit-detail-line">
                                                            <strong>{{ $auditLabels[$field] ?? ucwords(str_replace('_', ' ', $field)) }}:</strong>
                                                            {{ $displayAuditValue($value) }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No audit activity yet. Create, edit, or delete an invoice, customer, item, payment, expense, bank entry, or purchase order; the action will appear here automatically.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-audit-filter]').forEach(function (button) {
            button.addEventListener('click', function () {
                const eventName = this.dataset.auditFilter;
                const isActive = this.classList.contains('is-active');
                const rows = document.querySelectorAll('[data-audit-event]');

                document.querySelectorAll('[data-audit-filter]').forEach(function (item) {
                    item.classList.remove('is-active');
                });
                rows.forEach(function (row) {
                    row.classList.remove('audit-row-highlight');
                });

                if (isActive) {
                    return;
                }

                this.classList.add('is-active');
                const matchingRows = document.querySelectorAll('[data-audit-event="' + eventName + '"]');
                matchingRows.forEach(function (row) {
                    row.classList.add('audit-row-highlight');
                });

                if (matchingRows.length) {
                    matchingRows[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        });
    </script>

@endsection
