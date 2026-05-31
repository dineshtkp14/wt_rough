@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content sales-perday-legacy">
    @yield('breadcrumb')

    <div class="container-fluid px-3 px-xl-4">
        <div class="card sales-card">
            <div class="card-header sales-card-header">
                <div class="sales-header-left">
                    <a href="{{ route('items.create') }}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
                    <span class="sales-title-text">Total No Of Items</span>
                    <span class="counter-badge">
                        In Conunter : {{ $totalCashAndPaymentToday }} - {{ $totalCreditNotesTodaySUM }} = {{ $totalCashAndPaymentToday - $totalCreditNotesTodaySUM }}
                    </span>
                </div>

                <a href="{{ route('showonlysalesperdayinone_table.pp') }}" class="check-sales-btn">
                    <i class="fas fa-check"></i> Check All Sales Amount
                </a>
            </div>

            <div class="card-body sales-card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="table-title-wrap">
                            <span class="table-title-badge">CREDIT NOTES-LEDGER</span>
                        </div>

                        <table class="legacy-mini-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forsalesreturn as $sale)
                                    <tr @if(now()->format('Y-m-d') == $sale->date) class="today-row" @endif>
                                        <td>{{ $sale->date }}</td>
                                        <td>{{ $sale->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $forsalesreturn->links() }}
                    </div>

                    <div class="col-md-9">
                        <div class="table-title-wrap">
                            <span class="table-title-badge">Total Cash in Counter</span>
                        </div>

                        <table class="legacy-main-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>(C-N)</th>
                                    <th class="total-head">Total</th>
                                    <th>Counter Check</th>
                                    <th>Bank Deposit Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($totalSalesAndPayments as $data)
                                    <tr @if(now()->format('Y-m-d') == $data['date']) class="today-row" @endif>
                                        <td>{{ $data['date'] }}</td>
                                        <td>{{ $data['total'] }} -{{ $data['credit_notes_total'] }}</td>
                                        <td class="total-cell"><span>{{ $data['total'] - $data['credit_notes_total'] }}</span></td>
                                        <td class="text-center">
                                            @if ($data['counter_deposit'] == 'yes')
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($data['bank_deposit'] == 'yes')
                                                <i class="fas fa-check-circle fa-2x"></i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $totalSalesAndPayments->appends(['page' => $totalSalesAndPayments->currentPage()])->withPath(route('showonlysalesperday.pp'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .sales-perday-legacy .sales-card {
            border: 1px solid rgba(0, 0, 0, .125) !important;
            border-radius: 8px !important;
            box-shadow: none !important;
            overflow: hidden !important;
        }

        .sales-perday-legacy .sales-card-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 16px !important;
            padding: 18px 26px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #d8dee8 !important;
            color: #111827 !important;
        }

        .sales-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .sales-title-text {
            font-size: 23px;
            font-weight: 800;
        }

        .counter-badge {
            display: inline-flex;
            align-items: center;
            min-height: 60px;
            padding: 0 22px;
            border-radius: 8px;
            background: #212529;
            color: #ffffff;
            font-size: 24px;
            font-weight: 500;
        }

        .check-sales-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 58px;
            padding: 0 18px;
            border: 5px solid #ffc107;
            border-radius: 8px;
            background: #0d6efd;
            color: #ffffff !important;
            font-size: 20px;
            font-weight: 500;
            text-decoration: none;
            white-space: nowrap;
        }

        .sales-perday-legacy .sales-card-body {
            overflow-x: auto !important;
            padding: 22px 14px !important;
            background: #ffffff !important;
        }

        .table-title-wrap {
            text-align: center;
            margin-bottom: 10px;
        }

        .table-title-badge {
            display: inline-flex;
            align-items: center;
            min-height: 46px;
            padding: 0 16px;
            border-radius: 8px;
            background: #ffc107;
            color: #000000;
            font-size: 20px;
            font-weight: 900;
        }

        .sales-perday-legacy table,
        .sales-perday-legacy table.table {
            width: 100% !important;
            min-width: 0 !important;
            margin: 0 0 1rem !important;
            border-collapse: collapse !important;
            border-spacing: 0 !important;
            table-layout: auto !important;
            color: #111827 !important;
            --bs-table-bg: transparent !important;
            --bs-table-color: #111827 !important;
            --bs-table-striped-bg: transparent !important;
            --bs-table-striped-color: #111827 !important;
            --bs-table-hover-bg: transparent !important;
            --bs-table-hover-color: #111827 !important;
        }

        .legacy-mini-table {
            border: 5px solid #1f2933 !important;
        }

        .legacy-main-table {
            border: 1px solid #ffc107 !important;
        }

        .sales-perday-legacy table thead,
        .sales-perday-legacy table tbody,
        .sales-perday-legacy table tr {
            display: table-header-group;
        }

        .sales-perday-legacy table tbody {
            display: table-row-group !important;
        }

        .sales-perday-legacy table tr {
            display: table-row !important;
        }

        .sales-perday-legacy table th,
        .sales-perday-legacy table td {
            display: table-cell !important;
            padding: 10px 12px !important;
            border: 1px solid #b8b8b8 !important;
            background: transparent !important;
            color: #111827 !important;
            font-size: 26px !important;
            font-weight: 500 !important;
            line-height: 1.25 !important;
            text-align: left !important;
            text-transform: none !important;
            vertical-align: middle !important;
            white-space: normal !important;
        }

        .sales-perday-legacy table th {
            position: static !important;
            background: #5d5ced !important;
            color: #ffffff !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
        }

        .sales-perday-legacy table tbody tr:nth-child(even) td {
            background: #f2f2f2 !important;
        }

        .sales-perday-legacy table tbody tr:hover td {
            background: #f2f2f2 !important;
        }

        .sales-perday-legacy table .today-row td,
        .sales-perday-legacy table tr.today-row td {
            background: red !important;
            color: #ffffff !important;
            font-weight: 900 !important;
        }

        .sales-perday-legacy .total-head,
        .sales-perday-legacy .total-cell {
            background: #212529 !important;
            color: #ffffff !important;
        }

        .sales-perday-legacy .total-cell span {
            text-decoration: underline;
        }

        .sales-perday-legacy .text-center {
            text-align: center !important;
        }

        @media (max-width: 992px) {
            .sales-perday-legacy .sales-card-header {
                align-items: stretch !important;
                flex-direction: column !important;
            }

            .counter-badge,
            .check-sales-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</div>
@endsection
