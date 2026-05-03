@extends('layouts.master')

@section('page-css')
    <style>
        /* Warm Color Scheme - Orange/Teal/Gray instead of Blue */
        :root {
            --primary: #f97316;
            --primary-dark: #ea580c;
            --secondary: #14b8a6;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #06b6d4;
            --dark: #1f2937;
            --gray: #6b7280;
            --gray-light: #9ca3af;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e5e7eb;
        }

        .modern-dash {
            background: var(--bg);
            min-height: 100vh;
            padding-left: 300px;
            padding-top: 20px;
            padding-bottom: 20px;
            padding-right: 20px
        }

        .dash-hd {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .dash-hd h2 {
            font-weight: 700;
            color: var(--dark);
            margin: 0;
            font-size: 1.5rem;
        }

        .dt-badge {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 9999px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            color: var(--gray);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* Stats Grid */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #fff;
            flex-shrink: 0;
        }

        /* Warm color palette for icons */
        .stat-icon.orange {
            background: linear-gradient(135deg, #f97316, #ea580c);
        }

        .stat-icon.teal {
            background: linear-gradient(135deg, #14b8a6, #0d9488);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .stat-icon.amber {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .stat-icon.rose {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
        }

        .stat-icon.cyan {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .stat-icon.violet {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .stat-icon.pink {
            background: linear-gradient(135deg, #ec4899, #db2777);
        }

        .stat-val {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray);
            margin-top: 0.25rem;
        }

        .stat-delta {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .stat-delta.up {
            color: var(--success);
        }

        .stat-delta.down {
            color: var(--danger);
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-hd {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }

        .card-hd h5 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .card-bd {
            padding: 1rem 1.25rem;
        }

        /* Layout Grids */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .tables-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .alerts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Tables - FIXED HEADER COLORS */
        .table-modern {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .table-modern th {
            text-align: left;
            padding: 0.75rem;
            color: #374151;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #f3f4f6;
            border-bottom: 2px solid var(--border);
        }

        .table-modern td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border);
            color: #4b5563;
        }

        .table-modern tr:hover td {
            background: #f9fafb;
        }

        /* Badges - IMPROVED CONTRAST */
        .badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fecc01;
            color: #92400e;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background: #cffafe;
            color: #0e7490;
        }

        .badge-primary {
            background: #ffedd5;
            color: #9a3412;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-dot.paid {
            background: var(--success);
        }

        .status-dot.pending {
            background: var(--warning);
        }

        /* Payment Mode Badges - Stronger Contrast */
        .badge-mode {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            text-transform: capitalize;
        }

        .badge-cash {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-bank {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .badge-fonepay {
            background: #e0e7ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .badge-counter {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-default {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        /* Links */
        .view-all {
            font-size: 0.8rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .view-all:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Clickable Invoice / Receipt Links */
        .invoice-link,
        .receipt-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.15s;
        }

        .invoice-link:hover,
        .receipt-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Chart Containers */
        .chart-container {
            position: relative;
            height: 100%;
            min-height: 250px;
        }

        /* Responsive */
        @media (max-width: 1024px) {

            .charts-row,
            .tables-row,
            .alerts-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .modern-dash {
                padding: 1rem;
            }
        }
    </style>
</div>

<!-- Invoice Modal -->
<div id="invoiceModal" class="invoice-modal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h3>Invoice Details</h3>
            <button class="invoice-modal-close" onclick="closeInvoiceModal()">&times;</button>
        </div>
        <div class="invoice-modal-body" id="invoiceModalBody">
            <!-- Invoice content loaded via AJAX -->
        </div>
        <div class="invoice-modal-footer">
            <a id="invoicePrintLink" href="#" target="_blank" class="btn-print">
                <i class="fa-solid fa-print"></i> Print PDF
            </a>
            <button class="btn-close-modal" onclick="closeInvoiceModal()">Close</button>
        </div>
    </div>
</div>

<style>
    /* Invoice Modal Styles */
    .invoice-modal {
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

    .invoice-modal-content {
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

    .invoice-modal-header h3 {
        margin: 0;
        font-size: 1.25rem;
    }

    .invoice-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        line-height: 1;
    }

    .invoice-modal-close:hover {
        color: #ffdddd;
    }

    .invoice-modal-body {
        padding: 20px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .invoice-modal-footer {
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

    .btn-print:hover {
        background: #4338ca;
    }

    .btn-close-modal {
        background: #e5e7eb;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-close-modal:hover {
        background: #d1d5db;
    }

    /* Invoice display styles inside modal */
    .invoice-display {
        font-family: 'Noto Sans', Arial, sans-serif;
        color: #1f2937;
    }

    .invoice-display .inv-header {
        text-align: center;
        border-bottom: 2px solid #1f2937;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .invoice-display .inv-header h2 {
        font-size: 1.5rem;
        margin: 0 0 10px 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .invoice-display .inv-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 0.875rem;
    }

    .invoice-display .inv-meta-left,
    .invoice-display .inv-meta-right {
        line-height: 1.6;
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

    .invoice-display th,
    .invoice-display td {
        border: 1px solid #1f2937;
        padding: 8px 10px;
        text-align: left;
    }

    .invoice-display th {
        background: #f3f4f6;
        font-weight: 600;
    }

    .invoice-display .text-right {
        text-align: right;
    }

    .invoice-display .total-row {
        font-weight: 700;
        background: #f9fafb;
    }

    .invoice-display .notes {
        font-style: italic;
        color: #6b7280;
    }

    .invoice-display .footer-info {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #e5e7eb;
        font-size: 0.75rem;
        color: #6b7280;
    }

    @media print {
        .invoice-modal-header,
        .invoice-modal-footer {
            display: none;
        }
        .invoice-modal-content {
            box-shadow: none;
            margin: 0;
        }
    }
</style>

<script>
    function openInvoiceModal(invoiceId) {
        const modal = document.getElementById('invoiceModal');
        const body = document.getElementById('invoiceModalBody');
        const printLink = document.getElementById('invoicePrintLink');

        // Set print link
        printLink.href = '{{ url("billno/pdf/convert") }}?invoiceid=' + invoiceId;

        // Show modal with loading state
        body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading invoice...</p></div>';
        modal.style.display = 'block';

        // Fetch invoice data
        fetch('{{ route("customer.billno") }}?invoiceid=' + invoiceId)
            .then(response => response.text())
            .then(html => {
                // Extract invoice content from the returned HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Try to find the invoice table and details
                const table = doc.querySelector('table');
                const invoiceInfo = doc.querySelector('.thisisforhideandshow');

                if (table) {
                    // Build clean invoice display
                    let invoiceHtml = '<div class="invoice-display">';

                    // Header
                    invoiceHtml += '<div class="inv-header">';
                    invoiceHtml += '<h2>OM HARI TRADELINK</h2>';
                    invoiceHtml += '<p style="margin:5px 0;font-size:0.875rem;">Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>';
                    invoiceHtml += '<p style="margin:0;font-size:0.8rem;">Mobile No: 9860378262, 9848448624, 9812566284</p>';
                    invoiceHtml += '</div>';

                    // Get invoice details from the page
                    const allInvoices = doc.querySelectorAll('[class*="invoice"], [class*="bg-dark"]');
                    let invType = 'credit';
                    let invDate = '';
                    let customerName = '';
                    let customerAddress = '';
                    let customerPan = '';

                    // Try to extract data from the HTML
                    const rows = table.querySelectorAll('tr');
                    const metaText = doc.body.innerText;

                    // Parse customer info from meta text
                    const customerMatch = metaText.match(/Name:\s*([^\n]+)/);
                    if (customerMatch) customerName = customerMatch[1];

                    const addressMatch = metaText.match(/Address:\s*([^\n]+)/);
                    if (addressMatch) customerAddress = addressMatch[1];

                    const panMatch = metaText.match(/PAN No\.?\s*:?\s*(\d+)/);
                    if (panMatch) customerPan = panMatch[1];

                    // Invoice meta section
                    invoiceHtml += '<div class="inv-meta">';
                    invoiceHtml += '<div class="inv-meta-left">';
                    invoiceHtml += '<strong>INVOICE NO: ' + invoiceId + '</strong><br>';
                    if (customerPan) invoiceHtml += 'PAN No. ' + customerPan + '<br>';
                    invoiceHtml += '<br>';
                    invoiceHtml += '<strong>Name:</strong> ' + (customerName || 'N/A') + '<br>';
                    invoiceHtml += '<strong>Address:</strong> ' + (customerAddress || 'N/A') + '<br>';
                    invoiceHtml += '</div>';
                    invoiceHtml += '<div class="inv-meta-right">';
                    invoiceHtml += '<span class="inv-badge">Invoice Type: ' + invType + '</span><br><br>';
                    invoiceHtml += '<strong>Date:</strong> ' + new Date().toISOString().split('T')[0] + '<br>';
                    invoiceHtml += '</div>';
                    invoiceHtml += '</div>';

                    // Items table
                    invoiceHtml += '<table>';
                    invoiceHtml += '<thead><tr>';
                    invoiceHtml += '<th>#</th>';
                    invoiceHtml += '<th>ITEM ID</th>';
                    invoiceHtml += '<th>ITEM Name</th>';
                    invoiceHtml += '<th>Quantity</th>';
                    invoiceHtml += '<th>Unit</th>';
                    invoiceHtml += '<th>Sold Price</th>';
                    invoiceHtml += '<th>Amount</th>';
                    invoiceHtml += '</tr></thead>';
                    invoiceHtml += '<tbody>';

                    // Process rows
                    let sn = 1;
                    let subtotal = 0;
                    let discount = 0;
                    let total = 0;
                    let notes = '';

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 7 && !row.innerText.includes('Sub-Total') && !row.innerText.includes('Discount') && !row.innerText.includes('Total')) {
                            invoiceHtml += '<tr>';
                            invoiceHtml += '<td>' + sn + '</td>';
                            invoiceHtml += '<td>' + (cells[1]?.innerText || '') + '</td>';
                            invoiceHtml += '<td>' + (cells[2]?.innerText || '') + '</td>';
                            invoiceHtml += '<td>' + (cells[4]?.innerText || '') + '</td>';
                            invoiceHtml += '<td>' + (cells[5]?.innerText || '') + '</td>';
                            invoiceHtml += '<td>' + (cells[6]?.innerText || '') + '</td>';
                            invoiceHtml += '<td>' + (cells[7]?.innerText || cells[cells.length-1]?.innerText || '') + '</td>';
                            invoiceHtml += '</tr>';
                            sn++;
                        }
                        // Look for totals
                        if (row.innerText.includes('Sub-Total')) {
                            const val = row.innerText.match(/[\d,.]+/);
                            if (val) subtotal = val[0];
                        }
                        if (row.innerText.includes('Discount')) {
                            const val = row.innerText.match(/[\d,.]+/);
                            if (val) discount = val[0];
                        }
                        if (row.innerText.includes('Total Amount')) {
                            const val = row.innerText.match(/[\d,.]+/);
                            if (val) total = val[0];
                        }
                        if (row.innerText.includes('Notes:')) {
                            notes = row.innerText.replace('Notes:', '').trim();
                        }
                    });

                    // Summary rows
                    invoiceHtml += '<tr class="total-row">';
                    invoiceHtml += '<td colspan="5"></td>';
                    invoiceHtml += '<td class="text-right"><strong>Sub-Total:</strong></td>';
                    invoiceHtml += '<td><strong>' + (subtotal || '0.00') + '</strong></td>';
                    invoiceHtml += '</tr>';

                    invoiceHtml += '<tr class="total-row">';
                    invoiceHtml += '<td colspan="5"></td>';
                    invoiceHtml += '<td class="text-right"><strong>E-Discount:</strong></td>';
                    invoiceHtml += '<td><strong>' + (discount || '0.00') + '</strong></td>';
                    invoiceHtml += '</tr>';

                    invoiceHtml += '<tr class="total-row">';
                    invoiceHtml += '<td colspan="5" class="notes"><strong>Notes:</strong> ' + (notes || '') + '</td>';
                    invoiceHtml += '<td class="text-right"><strong>Total Amount:</strong></td>';
                    invoiceHtml += '<td><strong>' + (total || '0.00') + '</strong></td>';
                    invoiceHtml += '</tr>';

                    invoiceHtml += '</tbody></table>';

                    // Footer
                    invoiceHtml += '<div class="footer-info">';
                    invoiceHtml += '<p># Goods once sold won\'t be returned</p>';
                    invoiceHtml += '<p>Bill Created by: System</p>';
                    invoiceHtml += '</div>';

                    invoiceHtml += '</div>';
                    body.innerHTML = invoiceHtml;
                } else {
                    body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;">';
                    body.innerHTML += '<i class="fas fa-exclamation-circle fa-2x"></i>';
                    body.innerHTML += '<p>Could not load invoice details. Please try the Print PDF button.</p>';
                    body.innerHTML += '</div>';
                }
            })
            .catch(error => {
                body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;">';
                body.innerHTML += '<i class="fas fa-exclamation-circle fa-2x"></i>';
                body.innerHTML += '<p>Error loading invoice: ' + error.message + '</p>';
                body.innerHTML += '</div>';
            });
    }

    function closeInvoiceModal() {
        document.getElementById('invoiceModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('invoiceModal');
        if (event.target === modal) {
            closeInvoiceModal();
        }
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeInvoiceModal();
        }
    });
</script>

@section('content')
<div class="modern-dash">
    <!-- Header -->
    <div class="dash-hd">
        <h2>Dashboard Overview</h2>
        <span class="dt-badge">
            <i class="far fa-calendar"></i> {{ \App\Support\NepaliDate::adToBsString(now()->toDateString(), 'en') }}
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
                <h5>Today's Invoices</h5>
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
                                    <a href="#" class="invoice-link" onclick="openInvoiceModal({{ $inv['invoice_id'] }}); return false;">
                                        <strong>{{ $inv['id'] }}</strong>
                                    </a><br>
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
                <h5>Today's Payments</h5>
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
                                    <a href="{{ route('cashreceipt.search', ['receiptno' => $pay['payment_id']]) }}" class="receipt-link">
                                        <strong>{{ $pay['receipt'] }}</strong>
                                    </a><br>
                                    <small style="color:#9ca3af">{{ $pay['date'] }}</small>
                                </td>
                                <td>{{ $pay['customer'] }}</td>
                                <td>
                                    @php
                                        $modeKey = strtolower($pay['mode']);
                                        $modeClass = match(true) {
                                            str_contains($modeKey, 'cash') => 'badge-cash',
                                            str_contains($modeKey, 'bank') => 'badge-bank',
                                            str_contains($modeKey, 'fonepay') => 'badge-fonepay',
                                            str_contains($modeKey, 'counter') => 'badge-counter',
                                            default => 'badge-default',
                                        };
                                    @endphp
                                    <span class="badge-mode {{ $modeClass }}">{{ $pay['mode'] }}</span>
                                </td>
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

@endsection
