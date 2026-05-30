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

<div class="main-content">
<div class="checktoday-container" style="padding: 20px;">
    <h2 class="section-title">Today's Transactions - {{ date('Y-m-d') }}</h2>

    <div class="summary-cards">
        <div class="summary-card">
            <div class="summary-icon orange">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="summary-info">
                <h3>{{ $todayInvoices }} <small style="font-size:14px;color:#6b7280;">(Rs {{ number_format($todayInvoicesTotal, 2) }})</small></h3>
                <p>Total Invoices Today</p>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon green">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="summary-info">
                <h3>{{ $todayPayments }} <small style="font-size:14px;color:#6b7280;">(Rs {{ number_format($todayPaymentsTotal, 2) }})</small></h3>
                <p>Cash Receipt Amount Today</p>
            </div>
        </div>
    </div>

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
</div>

<!-- Invoice Modal -->
<div id="invoiceModal" class="invoice-modal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h3>Invoice Details</h3>
            <button class="invoice-modal-close" onclick="closeInvoiceModal()">&times;</button>
        </div>
        <div class="invoice-modal-body" id="invoiceModalBody">
        </div>
        <div class="invoice-modal-footer">
            <a id="invoicePrintLink" href="#" target="_blank" class="btn-print">
                <i class="fas fa-print"></i> Print PDF
            </a>
            <button class="btn-close-modal" onclick="closeInvoiceModal()">Close</button>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="payment-modal">
    <div class="payment-modal-content">
        <div class="payment-modal-header">
            <h3>Payment Details</h3>
            <button class="payment-modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="payment-modal-body" id="paymentModalBody">
        </div>
        <div class="payment-modal-footer">
            <a id="paymentPrintLink" href="#" target="_blank" class="btn-print">
                <i class="fas fa-print"></i> Print Receipt
            </a>
            <button class="btn-close-modal" onclick="closePaymentModal()">Close</button>
        </div>
    </div>
</div>

<style>
    .invoice-modal, .payment-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        overflow: auto;
    }

    .invoice-modal-content, .payment-modal-content {
        background-color: #fff;
        margin: 20px auto;
        width: 90%;
        max-width: 900px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .invoice-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .payment-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .invoice-modal-header h3, .payment-modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
    }

    .invoice-modal-close, .payment-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        line-height: 1;
    }

    .invoice-modal-body, .payment-modal-body {
        padding: 20px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .invoice-modal-footer, .payment-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 15px 20px;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        border-radius: 0 0 8px 8px;
    }

    .btn-print, .btn-close-modal {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-print {
        background: #4f46e5;
        color: white;
        border: none;
    }

    .btn-close-modal {
        background: #e5e7eb;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .invoice-display {
        font-family: 'Noto Sans', Arial, sans-serif;
        color: #1f2937;
    }

    .invoice-display .inv-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 0.875rem;
    }

    .invoice-display .inv-meta-right {
        text-align: right;
    }

    .invoice-display .inv-badge {
        display: inline-block;
        background: #1f2937;
        color: white;
        padding: 4px 12px;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .invoice-display table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 0.875rem;
    }

    .invoice-display th, .invoice-display td {
        border: 1px solid #1f2937;
        padding: 8px 10px;
        text-align: left;
    }

    .invoice-display th {
        background: #f97316;
        color: white;
        font-weight: 600;
    }

    .invoice-display .text-right {
        text-align: right;
    }

    .invoice-display .total-row {
        font-weight: 700;
        background: #f9fafb;
    }

    .payment-display {
        font-family: 'Noto Sans', Arial, sans-serif;
        color: #1f2937;
    }

    .payment-display .receipt-header {
        text-align: center;
        border-bottom: 2px solid #10b981;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .payment-display .receipt-header h2 {
        font-size: 1.5rem;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #10b981;
    }

    .payment-display .receipt-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 0.875rem;
    }

    .payment-display .receipt-meta-right {
        text-align: right;
    }

    .payment-display .receipt-badge {
        display: inline-block;
        background: #10b981;
        color: white;
        padding: 4px 12px;
        font-size: 0.75rem;
        text-transform: uppercase;
        border-radius: 4px;
    }

    .payment-display .amount-box {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        margin: 20px 0;
    }

    .payment-display .amount-value {
        font-size: 2rem;
        font-weight: 700;
    }

    .payment-display .payment-details {
        background: #f9fafb;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .payment-display .payment-details-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .payment-display .payment-details-label {
        font-weight: 600;
        color: #4b5563;
    }
</style>

<script>
function openInvoiceModal(invoiceId) {
    const modal = document.getElementById('invoiceModal');
    const body = document.getElementById('invoiceModalBody');
    const printLink = document.getElementById('invoicePrintLink');

    printLink.href = '{{ url("billno/pdf/convert") }}?invoiceid=' + invoiceId;
    body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading invoice...</p></div>';
    modal.style.display = 'block';

    fetch('{{ route("api.invoice.data") }}?invoiceid=' + invoiceId, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.error) throw new Error(data.error);

        let html = '<div class="invoice-display">';
        html += '<div class="inv-meta">';
        html += '<div><strong>INVOICE NO: ' + data.invoice_id + '</strong><br>';
        html += '<strong>Name:</strong> ' + (data.customer.name || 'N/A') + '<br>';
        html += '<strong>Address:</strong> ' + (data.customer.address || 'N/A') + '<br>';
        if (data.customer.phoneno) html += '<strong>Contact:</strong> ' + data.customer.phoneno + '<br>';
        html += '<strong>Customer Id:</strong> ' + (data.customer.id || 'N/A') + '</div>';
        html += '<div class="inv-meta-right">';
        html += '<span class="inv-badge">Invoice Type: ' + (data.type || 'credit') + '</span><br><br>';
        html += '<strong>Date:</strong> ' + data.date + '<br>';
        html += '<strong>Miti:</strong> ' + (data.nepali_date || '') + '</div>';
        html += '</div>';

        html += '<table><thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Price</th><th>Amount</th></tr></thead><tbody>';
        let totalQuantity = 0;
        data.items.forEach((item, i) => {
            totalQuantity += parseFloat(item.quantity || 0);
            html += '<tr><td>' + (i+1) + '</td><td>' + (item.item_name || '') + '</td><td>' + (item.quantity || '') + '</td><td>' + (item.price || '') + '</td><td>' + (item.subtotal || '') + '</td></tr>';
        });
        html += '<tr class="total-row"><td colspan="2" class="text-right"><strong>Total Quantity:</strong></td><td><strong>' + (Number.isInteger(totalQuantity) ? totalQuantity : totalQuantity.toFixed(2)) + '</strong></td><td></td><td></td></tr>';
        html += '<tr class="total-row"><td colspan="3"></td><td class="text-right"><strong>Total:</strong></td><td><strong>Rs ' + parseFloat(data.total || 0).toFixed(2) + '</strong></td></tr>';
        html += '</tbody></table>';
        html += '<div class="footer-info"><p>Bill Created by: ' + (data.added_by || 'System') + '</p></div>';
        html += '</div>';
        body.innerHTML = html;
    })
    .catch(error => {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;"><i class="fas fa-exclamation-circle fa-2x"></i><p>Error: ' + error.message + '</p></div>';
    });
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'none';
}

function openPaymentModal(paymentId) {
    const modal = document.getElementById('paymentModal');
    const body = document.getElementById('paymentModalBody');
    const printLink = document.getElementById('paymentPrintLink');

    printLink.href = '{{ route("cashreceipt.convert") }}?receiptno=' + paymentId;
    body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading payment...</p></div>';
    modal.style.display = 'block';

    fetch('{{ route("api.payment.data") }}?paymentid=' + paymentId, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.error) throw new Error(data.error);

        let html = '<div class="payment-display">';
        html += '<div class="receipt-header"><h2>Payment Receipt</h2></div>';
        html += '<div class="receipt-meta">';
        html += '<div><strong>Receipt No:</strong> ' + data.receipt_no + '<br>';
        html += '<strong>Customer:</strong> ' + (data.customer.name || 'N/A') + '<br>';
        html += '<strong>Address:</strong> ' + (data.customer.address || 'N/A') + '</div>';
        html += '<div class="receipt-meta-right">';
        html += '<span class="receipt-badge">' + data.mode + '</span><br><br>';
        html += '<strong>Date:</strong> ' + data.date + '<br>';
        html += '<strong>Miti:</strong> ' + (data.nepali_date || '') + '</div>';
        html += '</div>';
        html += '<div class="amount-box"><div>Amount Received</div><div class="amount-value">Rs ' + parseFloat(data.amount || 0).toFixed(2) + '</div></div>';
        html += '<div class="payment-details">';
        html += '<div class="payment-details-row"><span class="payment-details-label">Payment Mode:</span><span>' + data.mode + '</span></div>';
        html += '<div class="payment-details-row"><span class="payment-details-label">Customer ID:</span><span>' + (data.customer.id || 'N/A') + '</span></div>';
        html += '</div></div>';
        body.innerHTML = html;
    })
    .catch(error => {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;"><i class="fas fa-exclamation-circle fa-2x"></i><p>Error: ' + error.message + '</p></div>';
    });
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target === document.getElementById('invoiceModal')) closeInvoiceModal();
    if (event.target === document.getElementById('paymentModal')) closePaymentModal();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeInvoiceModal();
        closePaymentModal();
    }
});
</script>

@endsection
