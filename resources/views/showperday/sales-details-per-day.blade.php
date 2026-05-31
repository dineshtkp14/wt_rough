@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content sales-day-page">
    @yield('breadcrumb')

    <div class="container-fluid px-3 px-xl-4">
        <section class="sales-day-filter">
            <div>
                <div class="section-label">
                    <i class="fa-solid fa-calendar-days"></i>
                    Sales Details Per Day
                </div>
                <h3>Daily Sales Summary</h3>
            </div>

            <form action="{{ url('/sales-details-per-day') }}" method="get" class="sales-day-form">
                <div class="input-group">
                    <span class="input-group-text">Start Date</span>
                    <input type="date" name="date1" value="{{ $from }}" class="form-control">
                </div>
                <div class="input-group">
                    <span class="input-group-text">End Date</span>
                    <input type="date" name="date2" value="{{ $to }}" class="form-control">
                </div>
                <button type="submit" class="sales-day-search-btn">
                    <i class="fa-solid fa-search"></i>
                    Search
                </button>
            </form>
        </section>

        <section class="sales-day-stats">
            <div class="stat-card cash">
                <span>Cash Sales</span>
                <strong>Rs {{ number_format((float) $totalCashSales, 2) }}</strong>
            </div>
            <div class="stat-card credit">
                <span>Credit Sales</span>
                <strong>Rs {{ number_format((float) $totalCreditSales, 2) }}</strong>
            </div>
            <div class="stat-card notes">
                <span>Credit Notes</span>
                <strong>Rs {{ number_format((float) $totalCreditNotes, 2) }}</strong>
            </div>
            <div class="stat-card total">
                <span>Total Sales</span>
                <strong>Rs {{ number_format((float) $grandTotalSales, 2) }}</strong>
            </div>
            <div class="stat-card net">
                <span>Net After Credit Notes</span>
                <strong>Rs {{ number_format((float) $grandNetSales, 2) }}</strong>
            </div>
        </section>

        <div class="sales-day-toolbar">
            <div>
                <h4>Per Day Entries</h4>
                <span>{{ $salesRows->total() }} records found</span>
            </div>
            <input autocomplete="off" class="form-control" id="filterInput" type="text" placeholder="Search date or amount">
        </div>

        <div class="sales-day-table-wrap">
            <table class="sales-day-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Nepali Date</th>
                        <th class="text-end">Credit Notes</th>
                        <th class="text-end">Credit Sales</th>
                        <th class="text-end">Cash Sales</th>
                        <th class="text-end">Total Sales</th>
                        <th class="text-end">Net Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesRows as $row)
                        <tr class="{{ now()->toDateString() === $row['date'] ? 'today-row' : '' }}">
                            <td>{{ ($salesRows->currentPage() - 1) * $salesRows->perPage() + $loop->iteration }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ \App\Support\NepaliDate::adToBsString($row['date'], 'en') }}</td>
                            <td class="text-end notes-cell">Rs {{ number_format((float) $row['credit_notes'], 2) }}</td>
                            <td class="text-end credit-cell">Rs {{ number_format((float) $row['credit_sales'], 2) }}</td>
                            <td class="text-end cash-cell">Rs {{ number_format((float) $row['cash_sales'], 2) }}</td>
                            <td class="text-end total-cell">Rs {{ number_format((float) $row['total_sales'], 2) }}</td>
                            <td class="text-end net-cell">Rs {{ number_format((float) $row['net_after_credit_notes'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-row">No sales records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="sales-day-footer">
            {{ $salesRows->links() }}
        </div>
    </div>

    <style>
        .sales-day-page {
            width: 100%;
        }

        .sales-day-filter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
            padding: 18px;
            border-top: 5px solid #0f8f5f;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .sales-day-filter h3 {
            margin: 5px 0 0;
            color: #111827;
            font-size: 28px;
            font-weight: 900;
        }

        .sales-day-form {
            display: grid;
            grid-template-columns: minmax(190px, 1fr) minmax(190px, 1fr) 130px;
            gap: 12px;
            width: min(720px, 100%);
        }

        .sales-day-form .input-group-text {
            font-weight: 800;
        }

        .sales-day-search-btn {
            border: 0;
            border-radius: 8px;
            background: #111827;
            color: #ffffff;
            font-weight: 900;
        }

        .sales-day-stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(150px, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            padding: 16px;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .07);
            border-top: 5px solid #64748b;
        }

        .stat-card span {
            display: block;
            color: #64748b;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .stat-card strong {
            display: block;
            margin-top: 6px;
            color: #111827;
            font-size: 20px;
            font-weight: 900;
            white-space: nowrap;
        }

        .stat-card.cash { border-top-color: #059669; }
        .stat-card.credit { border-top-color: #2563eb; }
        .stat-card.notes { border-top-color: #dc2626; }
        .stat-card.total { border-top-color: #7c3aed; }
        .stat-card.net { border-top-color: #b45309; }

        .sales-day-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin: 18px 0 10px;
        }

        .sales-day-toolbar h4 {
            margin: 0;
            color: #111827;
            font-size: 22px;
            font-weight: 900;
        }

        .sales-day-toolbar span {
            color: #64748b;
            font-weight: 800;
        }

        .sales-day-toolbar .form-control {
            max-width: 320px;
            min-height: 48px;
            border-radius: 8px;
            font-size: 17px;
            font-weight: 700;
        }

        .sales-day-table-wrap {
            overflow-x: auto;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
        }

        .sales-day-table {
            width: 100%;
            min-width: 1120px;
            margin: 0;
            border-collapse: collapse;
        }

        .sales-day-table th,
        .sales-day-table td {
            display: table-cell !important;
            padding: 13px 12px;
            border: 1px solid #cbd5e1 !important;
            font-size: 16px;
            vertical-align: middle;
        }

        .sales-day-table th {
            background: #3348d4;
            color: #ffffff;
            font-weight: 900;
            text-transform: uppercase;
        }

        .sales-day-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .sales-day-table tbody tr:hover td {
            background: #ecfeff;
        }

        .sales-day-table .today-row td {
            background: #dc2626 !important;
            color: #ffffff !important;
            font-weight: 900;
        }

        .cash-cell { color: #047857 !important; font-weight: 900; white-space: nowrap; }
        .credit-cell { color: #1d4ed8 !important; font-weight: 900; white-space: nowrap; }
        .notes-cell { color: #b91c1c !important; font-weight: 900; white-space: nowrap; }
        .total-cell { color: #5b21b6 !important; font-weight: 900; white-space: nowrap; }
        .net-cell { color: #92400e !important; font-weight: 900; white-space: nowrap; }

        .empty-row {
            padding: 36px !important;
            color: #64748b;
            font-weight: 900;
            text-align: center;
        }

        .sales-day-footer {
            margin-top: 12px;
        }

        @media (max-width: 1200px) {
            .sales-day-stats {
                grid-template-columns: repeat(2, minmax(150px, 1fr));
            }
        }

        @media (max-width: 900px) {
            .sales-day-filter,
            .sales-day-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .sales-day-form {
                grid-template-columns: 1fr;
            }

            .sales-day-toolbar .form-control {
                max-width: none;
            }
        }
    </style>
</div>
@endsection
