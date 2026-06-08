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
                <span class="pill">{{ $auditLogs->count() }} recent</span>
            </div>
            <div class="table-wrap">
                <table class="smart-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Action</th>
                            <th>Record</th>
                            <th>User</th>
                            <th>Changed Values</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditLogs as $log)
                            <tr>
                                <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '-' }}</td>
                                <td><span class="pill">{{ $log->event }}</span></td>
                                <td>{{ $log->title }}</td>
                                <td>{{ $log->user_name ?? 'System' }}</td>
                                <td>
                                    @foreach (($log->new_values ?? []) as $field => $value)
                                        <strong>{{ $field }}</strong>: {{ is_scalar($value) ? $value : json_encode($value) }}<br>
                                    @endforeach
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

@endsection
