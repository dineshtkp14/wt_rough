@extends('layouts.master')

@section('content')
@include('layouts.breadcrumb')

<style>
    .checktoday-container {
        padding: 20px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5e7eb;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-hd {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-hd h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .card-hd.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .btn-print-all {
        background: rgba(255,255,255,0.2);
        color: white;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-print-all:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    th {
        background: #f9fafb;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: #6b7280;
    }

    tr:hover {
        background: #f9fafb;
    }

    .invoice-link, .receipt-link {
        color: #f97316;
        text-decoration: none;
        font-weight: 500;
        cursor: pointer;
    }

    .receipt-link {
        color: #10b981;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .mode-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .mode-cash {
        background: #d1fae5;
        color: #065f46;
    }

    .mode-bank {
        background: #dbeafe;
        color: #1e40af;
    }

    .mode-fonepay {
        background: #fce7f3;
        color: #9d174d;
    }

    .mode-counter {
        background: #fef3c7;
        color: #92400e;
    }

    .mode-default {
        background: #f3f4f6;
        color: #374151;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .summary-icon.orange {
        background: #fff7ed;
        color: #f97316;
    }

    .summary-icon.green {
        background: #ecfdf5;
        color: #10b981;
    }

    .summary-info h3 {
        font-size: 24px;
        font-weight: 700;
        margin: 0;
        color: #1f2937;
    }

    .summary-info p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }
</style>

<div class="checktoday-container">
    <h2 class="section-title">Today's Transactions - {{ date('Y-m-d') }}</h2>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-icon orange">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="summary-info">
                <h3>{{ count($recentInvoices) }}</h3>
                <p>Total Invoices Today</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon green">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="summary-info">
                <h3>{{ count($recentPayments) }}</h3>
                <p>Total Payments Today</p>
            </div>
        </div>
    </div>

    <!-- Today's Invoices -->
    <div class="card">
        <div class="card-hd" style="flex-wrap: wrap; gap: 10px;">
            <h5><i class="fas fa-file-invoice me-2"></i>Today's Invoices</h5>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('invoice.index') }}" class="btn-print-all">
                    <i class="fas fa-eye"></i> View All
                </a>
                <a href="{{ route('invoice.print.all.today') }}" target="_blank" class="btn-print-all">
                    <i class="fas fa-print"></i> Print All Today
                </a>
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Date (Miti)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentInvoices as $inv)
                    <tr>
                        <td>
                            <a href="javascript:void(0)" onclick="openInvoiceModal({{ $inv['invoice_id'] }})" class="invoice-link">
                                {{ $inv['id'] }}
                            </a>
                        </td>
                        <td>{{ $inv['customer'] }}</td>
                        <td>Rs {{ number_format($inv['amount'], 2) }}</td>
                        <td>{{ $inv['type'] }}</td>
                        <td>{{ $inv['date'] }}</td>
                        <td>
                            <span class="status-badge status-{{ $inv['status'] }}">
                                {{ ucfirst($inv['status']) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No invoices found for today</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Today's Payments -->
    <div class="card">
        <div class="card-hd green" style="flex-wrap: wrap; gap: 10px;">
            <h5><i class="fas fa-money-bill-wave me-2"></i>Today's Payments</h5>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('cpayments.index') }}" class="btn-print-all">
                    <i class="fas fa-eye"></i> View All
                </a>
                <a href="{{ route('payment.print.all.today') }}" target="_blank" class="btn-print-all">
                    <i class="fas fa-print"></i> Print All Today
                </a>
            </div>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Receipt</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Date (Miti)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $pay)
                    @php
                        $modeKey = strtolower($pay['mode']);
                        $modeClass = match(true) {
                            str_contains($modeKey, 'cash') => 'mode-cash',
                            str_contains($modeKey, 'bank') => 'mode-bank',
                            str_contains($modeKey, 'fonepay') => 'mode-fonepay',
                            str_contains($modeKey, 'counter') => 'mode-counter',
                            default => 'mode-default',
                        };
                    @endphp
                    <tr>
                        <td>
                            <a href="javascript:void(0)" onclick="openPaymentModal({{ $pay['payment_id'] }})" class="receipt-link">
                                {{ $pay['receipt'] }}
                            </a>
                        </td>
                        <td>{{ $pay['customer'] }}</td>
                        <td>Rs {{ number_format($pay['amount'], 2) }}</td>
                        <td>
                            <span class="mode-badge {{ $modeClass }}">
                                {{ $pay['mode'] }}
                            </span>
                        </td>
                        <td>{{ $pay['date'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>No payments found for today</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
@include('dashboard.partials.invoice_modal')

<!-- Payment Modal -->
@include('dashboard.partials.payment_modal')

<script>
function openInvoiceModal(invoiceId) {
    // This will be implemented if needed
    console.log('Open invoice modal:', invoiceId);
}

function openPaymentModal(paymentId) {
    // This will be implemented if needed
    console.log('Open payment modal:', paymentId);
}
</script>

@endsection
