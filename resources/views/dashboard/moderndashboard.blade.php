@extends('layouts.master')
@section('content')

@section('page-css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.css">
<style>
.modern-dash{background:#f3f4f6;min-height:100vh;padding:1.5rem}
.dash-hd{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem}
.dash-hd h2{font-weight:700;color:#111827;margin:0;font-size:1.5rem}
.dt-badge{background:#fff;border:1px solid #e5e7eb;border-radius:9999px;padding:.4rem 1rem;font-size:.85rem;color:#6b7280;display:inline-flex;align-items:center;gap:.4rem}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;margin-bottom:1.5rem}
.stat-card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;padding:1.25rem;display:flex;align-items:center;gap:1rem;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.stat-icon{width:48px;height:48px;border-radius:.75rem;display:flex;align-items:center;justify-content:center;font-size:1.25rem;color:#fff;flex-shrink:0}
.purple{background:#4f46e5}.blue{background:#3b82f6}.green{background:#10b981}.orange{background:#f59e0b}
.red{background:#ef4444}.teal{background:#14b8a6}.indigo{background:#6366f1}.pink{background:#ec4899}
.stat-val{font-size:1.5rem;font-weight:700;color:#111827;line-height:1}
.stat-label{font-size:.875rem;color:#6b7280;margin-top:.25rem}
.stat-delta{font-size:.75rem;margin-top:.25rem;font-weight:500}
.up{color:#10b981}.down{color:#ef4444}
.card{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;box-shadow:0 1px 3px rgba(0,0,0,.05);overflow:hidden}
.card-hd{padding:1rem 1.25rem;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center}
.card-hd h5{font-size:1rem;font-weight:600;color:#111827;margin:0}
.card-bd{padding:1rem 1.25rem}
.charts-row{display:grid;grid-template-columns:2fr 1fr;gap:1rem;margin-bottom:1.5rem}
.tables-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem}
.alerts-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.table-modern{width:100%;border-collapse:collapse;font-size:.875rem}
.table-modern th{text-align:left;padding:.6rem .75rem;color:#6b7280;font-weight:600;font-size:.75rem;text-transform:uppercase;border-bottom:1px solid #f3f4f6}
.table-modern td{padding:.6rem .75rem;border-bottom:1px solid #f9fafb;color:#374151}
.table-modern tr:hover td{background:#f9fafb}
.badge{font-size:.75rem;padding:.25rem .5rem;border-radius:.375rem;font-weight:500}
.badge-success{background:#d1fae5;color:#065f46}.badge-warning{background:#fef3c7;color:#92400e}
.badge-danger{background:#fee2e2;color:#991b1b}.badge-info{background:#dbeafe;color:#1e40af}
.status-dot{width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:.25rem}
.status-dot.paid{background:#10b981}.status-dot.pending{background:#f59e0b}
@media(max-width:1024px){.charts-row,.tables-row,.alerts-row{grid-template-columns:1fr}}
@media(max-width:640px){.stat-grid{grid-template-columns:1fr 1fr}}
</style>
@endsection

<div class="modern-dash">
    <!-- Header -->
    <div class="dash-hd">
        <h2>Dashboard Overview</h2>
        <span class="dt-badge">
            <i class="far fa-calendar"></i> {{ now()->format('l, F d, Y') }}
        </span>
    </div>

    <!-- Stats Cards -->
    <div class="stat-grid">
        @php
            $cards = [
                [
                    'i' => 'fas fa-box',
                    'c' => 'orange',
                    'v' => number_format($stats['total_items']),
                    'l' => 'Total Items',
                    'd' => 'Inventory count',
                    'u' => true,
                ],
                [
                    'i' => 'fas fa-users',
                    'c' => 'teal',
                    'v' => number_format($stats['total_customers']),
                    'l' => 'Customers',
                    'd' => 'Active accounts',
                    'u' => true,
                ],
                [
                    'i' => 'fas fa-building',
                    'c' => 'green',
                    'v' => number_format($stats['total_companies']),
                    'l' => 'Suppliers',
                    'd' => 'Partners',
                    'u' => true,
                ],
                [
                    'i' => 'fas fa-file-invoice',
                    'c' => 'amber',
                    'v' => number_format($stats['today_invoices']),
                    'l' => 'Invoices Today',
                    'd' => number_format($stats['month_invoices']) . ' this month',
                    'u' => true,
                ],
                [
                    'i' => 'fas fa-rotate-left',
                    'c' => 'rose',
                    'v' => number_format($stats['today_credit_notes']),
                    'l' => 'Credit Notes',
                    'd' => number_format($stats['month_credit_notes']) . ' this month',
                    'u' => false,
                ],
                [
                    'i' => 'fas fa-building-columns',
                    'c' => 'cyan',
                    'v' => 'Rs ' . number_format($stats['bank_balance'], 2),
                    'l' => 'Bank Balance',
                    'd' => 'Available funds',
                    'u' => true,
                ],
                [
                    'i' => 'fas fa-receipt',
                    'c' => 'violet',
                    'v' => 'Rs ' . number_format($stats['month_expenses'], 2),
                    'l' => 'Expenses',
                    'd' => 'This month',
                    'u' => false,
                ],
                [
                    'i' => 'fas fa-triangle-exclamation',
                    'c' => 'pink',
                    'v' => $stats['low_stock_items'] + $stats['out_of_stock_items'],
                    'l' => 'Stock Alerts',
                    'd' => $stats['out_of_stock_items'] . ' out of stock',
                    'u' => false,
                ],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="stat-card">
                <div class="stat-icon {{ $card['c'] }}">
                    <i class="{{ $card['i'] }}"></i>
                </div>
                <div>
                    <div class="stat-val">{{ $card['v'] }}</div>
                    <div class="stat-label">{{ $card['l'] }}</div>
                    <div class="stat-delta {{ $card['u'] ? 'up' : 'down' }}">{{ $card['d'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="charts-row">
        <div class="card">
            <div class="card-hd">
                <h5>Sales Trend (30 Days)</h5>
                <span class="badge badge-primary">Rs {{ number_format(array_sum($dailySales['data']), 2) }} total</span>
            </div>
            <div class="card-bd">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-hd">
                <h5>Sales by Payment Mode</h5>
            </div>
            <div class="card-bd">
                <div class="chart-container">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="tables-row">
        <div class="card">
            <div class="card-hd">
                <h5>Recent Invoices</h5>
                <a href="{{ route('itemsales.index') }}" class="view-all">View all</a>
            </div>
            <div class="card-bd">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentInvoices as $inv)
                            <tr>
                                <td>
                                    <strong>{{ $inv['id'] }}</strong><br>
                                    <small style="color:#9ca3af">{{ $inv['date'] }}</small>
                                </td>
                                <td>{{ $inv['customer'] }}</td>
                                <td>Rs {{ number_format($inv['amount'], 2) }}</td>
                                <td>
                                    <span
                                        class="badge {{ $inv['status'] == 'paid' ? 'badge-success' : 'badge-warning' }}">
                                        <span class="status-dot {{ $inv['status'] }}"></span>
                                        {{ ucfirst($inv['status']) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-hd">
                <h5>Recent Payments</h5>
                <a href="{{ route('cpayments.index') }}" class="view-all">View all</a>
            </div>
            <div class="card-bd">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Receipt</th>
                            <th>Customer</th>
                            <th>Mode</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentPayments as $pay)
                            <tr>
                                <td>
                                    <strong>{{ $pay['receipt'] }}</strong><br>
                                    <small style="color:#9ca3af">{{ $pay['date'] }}</small>
                                </td>
                                <td>{{ $pay['customer'] }}</td>
                                <td><span class="badge badge-info">{{ $pay['mode'] }}</span></td>
                                <td>Rs {{ number_format($pay['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alerts & Top Items Row -->
    <div class="alerts-row">
        <div class="card">
            <div class="card-hd">
                <h5>Low Stock Alerts</h5>
                <span class="badge badge-danger">Action needed</span>
            </div>
            <div class="card-bd">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Company</th>
                            <th>Stock</th>
                            <th>Threshold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lowStockAlerts as $alert)
                            <tr>
                                <td>{{ $alert['item'] }}</td>
                                <td>{{ $alert['company'] }}</td>
                                <td><strong style="color:#ef4444">{{ $alert['current'] }}</strong></td>
                                <td>{{ $alert['threshold'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-hd">
                <h5>Top Selling Items</h5>
            </div>
            <div class="card-bd">
                <div class="chart-container">
                    <canvas id="topItemsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Status -->
    <div class="card" style="margin-top:1rem">
        <div class="card-hd">
            <h5>Stock Status Distribution</h5>
        </div>
        <div class="card-bd">
            <div class="chart-container">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    // Warm color palette
    const colors = {
        primary: '#f97316',
        primaryLight: 'rgba(249, 115, 22, 0.1)',
        secondary: '#14b8a6',
        success: '#22c55e',
        warning: '#f59e0b',
        danger: '#ef4444',
        gray: '#9ca3af'
    };

    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailySales['labels']) !!},
            datasets: [{
                label: 'Daily Sales',
                data: {!! json_encode($dailySales['data']) !!},
                borderColor: colors.primary,
                backgroundColor: colors.primaryLight,
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: colors.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rs ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rs ' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value);
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Payment Mode Chart
    const payCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(payCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($paymentModes['labels']) !!},
            datasets: [{
                data: {!! json_encode($paymentModes['data']) !!},
                backgroundColor: [colors.success, colors.warning, colors.secondary],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        color: '#4b5563'
                    }
                }
            }
        }
    });

    // Top Items Chart
    const topCtx = document.getElementById('topItemsChart').getContext('2d');
    new Chart(topCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($topItems['labels']) !!},
            datasets: [{
                label: 'Units Sold',
                data: {!! json_encode($topItems['data']) !!},
                backgroundColor: colors.secondary,
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.x + ' units';
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        color: '#6b7280'
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                y: {
                    ticks: {
                        color: '#4b5563'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Stock Status Chart
    const stockCtx = document.getElementById('stockChart').getContext('2d');
    new Chart(stockCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($stockStatus['labels']) !!},
            datasets: [{
                label: 'Items',
                data: {!! json_encode($stockStatus['data']) !!},
                backgroundColor: [colors.success, colors.warning, colors.danger],
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' items';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#6b7280'
                    },
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    ticks: {
                        color: '#4b5563'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>

@stop
