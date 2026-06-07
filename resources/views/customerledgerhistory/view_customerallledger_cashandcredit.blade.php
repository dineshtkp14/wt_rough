@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    @php
        $customer = $cusinfoforpdfok->first();
        $dueAmount = (float) $allnotcash - (float) $cts;
        $hasRows = $all && count($all) > 0;
        $hasCreditNotes = $creditnoteledger && count($creditnoteledger) > 0;
        $amountToWords = function ($num) use (&$amountToWords) {
            $num = (int) floor($num);
            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            if ($num < 0) return 'Minus ' . $amountToWords(abs($num));
            if ($num === 0) return 'Zero';

            $words = '';
            if ($num >= 10000000) { $words .= $amountToWords(floor($num / 10000000)) . ' Crore '; $num %= 10000000; }
            if ($num >= 100000) { $words .= $amountToWords(floor($num / 100000)) . ' Lakh '; $num %= 100000; }
            if ($num >= 1000) { $words .= $amountToWords(floor($num / 1000)) . ' Thousand '; $num %= 1000; }
            if ($num >= 100) { $words .= $amountToWords(floor($num / 100)) . ' Hundred '; $num %= 100; }
            if ($num >= 20) { $words .= $tens[floor($num / 10)] . ' '; $num %= 10; }
            if ($num > 0) { $words .= $ones[$num] . ' '; }

            return trim($words);
        };
    @endphp

    <div class="main-content clhs-page">
        @yield('breadcrumb')

        <div class="container-fluid px-3 px-xl-4">
            <div class="card customer-card mb-4" id="customerCard" style="display: none;">
                <div class="card-body">
                    <h5 class="card-title">Customer Info</h5>
                    <p><span>ID: </span><span id="customerId">...</span></p>
                    <p class="card-text"><span>Name: </span><span id="customerName">...</span></p>
                    <p><span>Address: </span><span id="customerAddress">...</span></p>
                    <p><span>E-mail: </span><span id="customerEmail">...</span></p>
                    <p><span>PhoneNo: </span><span id="customerPhone">...</span></p>
                </div>

                <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                    <i class="fas fa-user"></i>
                </div>
            </div>

            <div class="clhs-top-grid">
                <section class="clhs-panel clhs-search-panel">
                    <div class="clhs-section-title">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Find Customer Ledger
                    </div>

                    <form action="{{ route('returnchoosendatehistroycashandcredit') }}" method="get" id="chosendatepdfform">
                        <div class="search-box clhs-customer-search">
                            <input id="customerIdInput" name="customerid" hidden>
                            <input type="text"
                                class="search-input @error('customerid') is-invalid @enderror"
                                placeholder="Search Customer"
                                id="searchCustomerInput"
                                data-api="customer_search"
                                autocomplete="off">
                            @error('customerid')
                                <p class="invalid-feedback m-0">{{ $message }}</p>
                            @enderror
                            <i class="fas fa-search search-icon"></i>

                            <div class="result-wrapper" id="customerResultWrapper" style="display: none;">
                                <div class="result-box d-flex justify-content-start align-items-center" id="customerLoadingResultBox">
                                    <i class="fas fa-spinner" id="spinnerIcon"></i>
                                    <h1 class="m-0 px-2">Loading</h1>
                                </div>
                                <div class="result-box d-flex justify-content-start align-items-center d-none" id="customerNotFoundResultBox">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <h1 class="m-0 px-2">Record Not Found</h1>
                                </div>
                                <div id="customerResultList"></div>
                            </div>
                        </div>

                        <div class="clhs-date-grid">
                            <div class="input-group">
                                <span class="input-group-text">Start Date</span>
                                <input type="date" name="date1" value="{{ request('date1', $from ?? '') }}" class="form-control">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">End Date</span>
                                <input type="date" name="date2" value="{{ request('date2', $to ?? '') }}" class="form-control">
                            </div>
                        </div>

                        <button type="submit" class="clhs-search-btn">
                            <i class="fas fa-search"></i>
                            Search Ledger
                        </button>
                    </form>
                </section>

                @if($cid)
                    <section class="clhs-panel clhs-summary-panel">
                        <div class="clhs-summary-head">
                            <div>
                                <div class="clhs-section-title">
                                    <i class="fa-solid fa-user"></i>
                                    Customer Summary
                                </div>
                                <h3>{{ $customer->name ?? 'Select a customer' }}</h3>
                                <p>{{ $customer->address ?? 'No address selected' }}</p>
                            </div>
                            <a href="{{ route('chequedeposit.create') }}" class="clhs-cheque-btn">
                                <i class="fas fa-money-bill-wave"></i>
                                Cheque Deposit
                            </a>
                        </div>

                        <div class="clhs-customer-meta">
                            <div><span>Phone</span><b>{{ $customer->phoneno ?? '-' }}</b></div>
                            <div><span>Alternate Phone</span><b>{{ $customer->alternate_phoneno ?? $customer->phoneno ?? '-' }}</b></div>
                            <div><span>Email</span><b>{{ $customer->email ?? '-' }}</b></div>
                        </div>

                        <div class="clhs-due-card {{ $dueAmount < 0 ? 'is-negative' : '' }}">
                            <span>Total Due Amount</span>
                            <strong>{{ number_format($dueAmount, 2) }} -/</strong>
                            <small>{{ $amountToWords($dueAmount) }} only -/</small>
                        </div>

                        <div class="clhs-actions">
                            <a href="{{ route('cpayments.create', [
                                'customerid' => $cid,
                                'amount' => $dueAmount,
                                'totaldueamountfornotclear' => $dueAmount,
                                'cname' => $customer
                                    ? trim(($customer->name ?? '') . ' | ' . ($customer->address ?? '') . ' | ' . ($customer->phoneno ?? ''))
                                    : null,
                            ]) }}"
                                class="customer-ledger-payment-btn">
                                <i class="fa-solid fa-money-bill-wave"></i>
                                Customer Ledger Payment
                            </a>
                        </div>
                    </section>
                @endif
            </div>

            <div class="clhs-toolbar">
                <div>
                    <h4>Ledger Entries</h4>
                    <span>{{ $hasRows ? count($all) . ' records found' : 'No records found' }}</span>
                </div>
                @if($cid)
                    <div class="clhs-toolbar-actions">
                        <a href="{{ route('print.all.customer.invoices', ['customerid' => $cid, 'date1' => $from, 'date2' => $to]) }}"
                            onclick="openPdfInNewTab(event, this.href); return false;"
                            class="clhs-print-all-btn invoices {{ !$hasRows ? 'pdf-link-disabled' : '' }}">
                            <i class="fa-regular fa-file-lines"></i>
                            <span>Print All Invoices</span>
                        </a>
                        <a href="{{ route('print.all.customer.creditnotes', ['customerid' => $cid, 'date1' => $from, 'date2' => $to]) }}"
                            onclick="openPdfInNewTab(event, this.href); return false;"
                            class="clhs-print-all-btn creditnotes {{ !$hasCreditNotes ? 'pdf-link-disabled' : '' }}">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>Print All Credit Notes</span>
                        </a>
                        <a href="{{ route('customer.printallcashreceipts', ['customerid' => $cid, 'date1' => $from, 'date2' => $to, 'ledger_mode' => 'cash_credit']) }}"
                            onclick="openPdfInNewTab(event, this.href); return false;"
                            class="clhs-print-all-btn receipts">
                            <i class="fa-solid fa-receipt"></i>
                            <span>Print All Cash Receipts</span>
                        </a>
                        <a href="{{ route('pdfreturnchoosendatehistroycashandcredit.convert', ['customerid' => $cid, 'date1' => $from, 'date2' => $to]) }}"
                            onclick="openPdfInNewTab(event, this.href); return false;"
                            class="clhs-print-btn {{ !$hasRows ? 'pdf-link-disabled' : '' }}">
                            <span>Print</span>
                            <i class="fa-solid fa-print"></i>
                        </a>
                    </div>
                @endif
            </div>

            <div class="clhs-table-wrap">
                <table class="clhs-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Nepali Date</th>
                            <th>Created At</th>
                            <th>Particulars</th>
                            <th>Voucher Type</th>
                            <th>Invoice Type</th>
                            <th>Invoice No</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($hasRows)
                            @foreach ($all as $i)
                                @php
                                    $isPayment = $i->invoicetype == 'payment';
                                    $isSettlement = $i->invoicetype == 'settlement';
                                    $isCash = $i->invoicetype == 'cash';
                                    $isCreditNote = $i->invoicetype == 'credit_note';
                                @endphp
                                <tr class="{{ $isPayment ? 'is-payment-row' : '' }} {{ $isSettlement ? 'is-settlement-row' : '' }} {{ \Carbon\Carbon::parse($i->date)->isToday() ? 'clhs-today-row' : '' }}">
                                    <td>{{ method_exists($all, 'firstItem') ? $all->firstItem() + $loop->index : $loop->iteration }}</td>
                                    <td>{{ $i->date }}</td>
                                    <td>{{ \App\Support\NepaliDate::adToBsString($i->date ?? now()->toDateString(), 'en') }}</td>
                                    <td>{{ $i->created_at }}</td>
                                    <td>{{ $i->particulars }}</td>
                                    <td>{{ $i->voucher_type }}</td>
                                    <td>
                                        <span class="clhs-type-badge {{ $isPayment ? 'payment' : ($isSettlement ? 'settlement' : ($isCash ? 'cash' : ($isCreditNote ? 'credit-note' : 'credit'))) }}">
                                            {{ $isSettlement ? 'Nil Account' : ($isCreditNote ? 'Credit Note' : $i->invoicetype) }}
                                            @if($isPayment)
                                                CR-({{ $i->id }})
                                            @endif
                                        </span>
                                        @if($isPayment)
                                            <button type="button" onclick="openPaymentModal({{ $i->id }})" class="clhs-view-payment-btn">View</button>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($i->invoiceid))
                                            <span class="clhs-invoice-number">{{ $isCreditNote ? 'CN-' : '' }}{{ $i->invoiceid }}</span>
                                            @if($isCreditNote)
                                                <button type="button" onclick="openCreditNoteModal({{ $i->invoiceid }})" class="clhs-view-invoice-btn">View</button>
                                            @else
                                                <button type="button" onclick="openInvoiceModal({{ $i->invoiceid }})" class="clhs-view-invoice-btn">View</button>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format((float) $i->debit, 2) }}</td>
                                    <td class="text-end">{{ number_format((float) $i->credit, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="clhs-total-row">
                                <td colspan="8" class="text-end">Total</td>
                                <td class="text-end">{{ number_format((float) $allnotcash, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $cts, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="10" class="clhs-empty-state">
                                    <i class="fa-solid fa-file-circle-question"></i>
                                    Select a customer and search to view ledger records.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $all ? $all->links() : '' }}
            </div>

            <div class="clhs-toolbar credit-note-toolbar">
                <div>
                    <h4>Credit Notes Details</h4>
                    <span>{{ $hasCreditNotes ? count($creditnoteledger) . ' records found' : 'No credit notes found' }}</span>
                </div>
            </div>

            <div class="clhs-table-wrap mb-4">
                <table class="clhs-table clhs-credit-note-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Voucher Type</th>
                            <th>CN Invoice No</th>
                            <th class="text-end">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($hasCreditNotes)
                            @foreach ($creditnoteledger as $i)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $i->date }}</td>
                                    <td>{{ $i->particulars }}</td>
                                    <td>{{ $i->voucher_type }}</td>
                                    <td>{{ $i->invoiceid }}</td>
                                    <td class="text-end">{{ number_format((float) $i->debit, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="clhs-total-row">
                                <td colspan="5" class="text-end">Total</td>
                                <td class="text-end">{{ number_format((float) $debittotalcrnotes, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="6" class="clhs-empty-state">
                                    <i class="fa-solid fa-file-circle-question"></i>
                                    No credit notes found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
<!-- Invoice Modal -->
<div id="invoiceModal" class="invoice-modal">
    <div class="invoice-modal-content">
        <div class="invoice-modal-header">
            <h3>Invoice Details</h3>
            <button class="invoice-modal-close" onclick="closeInvoiceModal()">&times;</button>
        </div>
        <div class="invoice-modal-body" id="invoiceModalBody"></div>
        <div class="invoice-modal-footer">
            <a id="invoicePrintLink" href="#" target="_blank" class="btn-print"><i class="fas fa-print"></i> Print PDF</a>
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
        <div class="payment-modal-body" id="paymentModalBody"></div>
        <div class="payment-modal-footer">
            <a id="paymentPrintLink" href="#" target="_blank" class="btn-print"><i class="fas fa-print"></i> Print Receipt</a>
            <button class="btn-close-modal" onclick="closePaymentModal()">Close</button>
        </div>
    </div>
</div>

<div id="creditNoteModal" class="credit-note-modal">
    <div class="credit-note-modal-content">
        <div class="credit-note-modal-header">
            <h3>Credit Note Details</h3>
            <button class="credit-note-modal-close" onclick="closeCreditNoteModal()">&times;</button>
        </div>
        <div class="credit-note-modal-body" id="creditNoteModalBody"></div>
        <div class="credit-note-modal-footer">
            <a id="creditNotePrintLink" href="#" target="_blank" class="btn-print credit-note-print-btn">
                <i class="fas fa-print"></i> Print PDF
            </a>
            <button class="btn-close-modal" onclick="closeCreditNoteModal()">Close</button>
        </div>
    </div>
</div>

<style>
.clhs-page {
    flex: 1 1 auto;
    width: 100%;
}

.clhs-top-grid {
    display: grid;
    gap: 18px;
    grid-template-columns: minmax(360px, 1.1fr) minmax(420px, 1fr);
    margin-bottom: 18px;
}

.clhs-panel {
    background: #ffffff;
    border: 1px solid #dbe3ef;
    border-radius: 8px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.07);
    padding: 18px;
}

.clhs-search-panel {
    border-top: 5px solid #0f8f5f;
}

.clhs-summary-panel {
    border-top: 5px solid #5d5ced;
}

.clhs-section-title {
    align-items: center;
    color: #64748b;
    display: flex;
    font-size: 13px;
    font-weight: 800;
    gap: 8px;
    letter-spacing: .02em;
    margin-bottom: 10px;
    text-transform: uppercase;
}

.clhs-customer-search .search-input {
    border-color: #cbd5e1;
    font-size: 20px;
    min-height: 54px;
}

.clhs-date-grid {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    margin: 16px 0;
}

.clhs-date-grid .input-group-text {
    background: #f1f5f9;
    font-weight: 700;
}

.clhs-search-btn {
    align-items: center;
    background: #1f2933;
    border: 0;
    border-radius: 6px;
    color: #ffffff;
    display: inline-flex;
    font-size: 24px;
    font-weight: 800;
    gap: 12px;
    justify-content: center;
    min-height: 62px;
    width: 100%;
}

.clhs-search-btn:hover {
    background: #111827;
}

.clhs-summary-head {
    align-items: flex-start;
    display: flex;
    gap: 16px;
    justify-content: space-between;
}

.clhs-summary-head h3 {
    color: #111827;
    font-size: 26px;
    font-weight: 900;
    line-height: 1.05;
    margin: 0 0 4px;
}

.clhs-summary-head p {
    color: #475569;
    font-size: 18px;
    margin: 0;
}

.clhs-cheque-btn {
    background: #2563eb;
    border: 4px solid #dc3545;
    border-radius: 6px;
    color: #ffffff !important;
    display: inline-flex;
    font-weight: 800;
    gap: 8px;
    padding: 8px 12px;
    text-decoration: none !important;
    white-space: nowrap;
}

.clhs-cheque-btn:hover {
    background: #1d4ed8;
    color: #ffffff !important;
}

.clhs-customer-meta {
    display: grid;
    gap: 10px;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    margin: 14px 0;
}

.clhs-customer-meta div {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px;
}

.clhs-customer-meta span,
.clhs-due-card span {
    color: #64748b;
    display: block;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
}

.clhs-customer-meta b {
    color: #111827;
    display: block;
    font-size: 16px;
    margin-top: 3px;
    word-break: break-word;
}

.clhs-due-card {
    background: #138c55;
    border-radius: 8px;
    color: #ffffff;
    padding: 14px 18px;
}

.clhs-due-card.is-negative {
    background: #dc2626;
}

.clhs-due-card span {
    color: rgba(255, 255, 255, .86);
}

.clhs-due-card strong {
    display: block;
    font-size: 28px;
    font-weight: 900;
    line-height: 1.1;
    margin-top: 4px;
}

.clhs-due-card small {
    display: block;
    font-size: 14px;
    margin-top: 6px;
    text-transform: capitalize;
}

.customer-ledger-payment-btn {
    align-items: center;
    background: #dc3545 !important;
    border: 5px solid #ffc107 !important;
    border-radius: 6px;
    color: #ffffff !important;
    display: inline-flex;
    font-size: 24px;
    font-weight: 500;
    gap: 8px;
    margin-top: 12px;
    padding: 8px 14px;
    text-decoration: none !important;
    white-space: nowrap;
}

.customer-ledger-payment-btn:hover {
    background: #c82333 !important;
    color: #ffffff !important;
}

.customer-ledger-payment-btn.disabled,
.pdf-link-disabled {
    opacity: .55;
    pointer-events: none;
}

.clhs-toolbar {
    align-items: center;
    display: flex;
    gap: 16px;
    justify-content: space-between;
    margin: 18px 0 10px;
}

.clhs-toolbar h4 {
    color: #111827;
    font-size: 22px;
    font-weight: 900;
    margin: 0;
}

.clhs-toolbar span {
    color: #64748b;
    font-size: 14px;
    font-weight: 700;
}

.clhs-toolbar-actions {
    align-items: stretch;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end;
}

.clhs-print-btn {
    align-items: stretch;
    border: 1px solid #2563eb;
    border-radius: 8px;
    color: #3730a3;
    display: inline-flex;
    font-size: 19px;
    font-weight: 900;
    overflow: hidden;
    text-decoration: none;
    text-transform: uppercase;
}

.clhs-print-btn span {
    padding: 13px 24px;
}

.clhs-print-btn i {
    align-items: center;
    background: #6366f1;
    color: #ffffff;
    display: inline-flex;
    padding: 0 16px;
}

.clhs-print-all-btn {
    align-items: center;
    border-radius: 6px;
    display: inline-flex;
    font-size: 16px;
    font-weight: 700;
    gap: 8px;
    justify-content: center;
    line-height: 1.25;
    min-height: 50px;
    padding: 9px 14px;
    text-align: center;
    text-decoration: none;
}

.clhs-print-all-btn span,
.clhs-print-all-btn i {
    color: #ffffff !important;
}

.clhs-print-all-btn.invoices {
    background: #198754;
}

.clhs-print-all-btn.invoices:hover {
    background: #146c43;
}

.clhs-print-all-btn.receipts {
    background: #17c5df;
}

.clhs-print-all-btn.receipts:hover {
    background: #0fb5ce;
}

.clhs-print-all-btn.creditnotes {
    background: #d97706;
}

.clhs-print-all-btn.creditnotes:hover {
    background: #b45309;
}

.clhs-table-wrap {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    overflow-x: auto;
}

.clhs-table {
    border-collapse: collapse;
    margin: 0;
    min-width: 1120px;
    width: 100%;
}

.clhs-credit-note-table {
    min-width: 760px;
}

.clhs-table thead {
    display: table-header-group !important;
}

.clhs-table tbody {
    display: table-row-group !important;
    height: auto !important;
    overflow: visible !important;
}

.clhs-table tr {
    display: table-row !important;
    width: auto !important;
}

.clhs-table th,
.clhs-table td {
    border: 1px solid #cbd5e1 !important;
    display: table-cell !important;
    font-size: 16px;
    padding: 12px 10px;
    vertical-align: middle;
}

.clhs-table th {
    background: #5d5ced;
    color: #ffffff;
    font-weight: 900;
    position: sticky;
    top: 0;
    z-index: 1;
}

.clhs-table tbody tr:nth-child(even) td {
    background: #f8fafc;
}

.clhs-table tbody tr:hover td {
    background: #ecfeff;
}

.clhs-table tbody tr.is-settlement-row td {
    background: #f7fffb !important;
    border-bottom: 4px solid #00ff88;
    border-top: 4px solid #00ff88;
    box-shadow: inset 0 2px 0 #39ff14, inset 0 -2px 0 #39ff14;
    color: #064e3b;
    font-weight: 900;
}

.clhs-table tbody tr.clhs-today-row td {
    background: red !important;
    color: #ffffff !important;
}

.clhs-table tbody tr.clhs-today-row .clhs-type-badge {
    background: transparent;
    color: #ffffff;
    padding-left: 0;
}

.clhs-type-badge {
    border-radius: 999px;
    display: inline-flex;
    font-size: 13px;
    font-weight: 900;
    padding: 5px 10px;
    text-transform: uppercase;
}

.clhs-type-badge.credit {
    background: #fee2e2;
    color: #991b1b;
}

.clhs-type-badge.cash {
    background: #dbeafe;
    color: #1e40af;
}

.clhs-type-badge.credit-note {
    background: #fef3c7;
    color: #92400e;
}

.clhs-type-badge.payment {
    background: #dcfce7;
    color: #166534;
}

.clhs-type-badge.settlement {
    background: #00ff88;
    box-shadow: 0 0 12px rgba(0, 255, 136, 0.75);
    color: #052e16;
}

.clhs-invoice-number {
    display: inline-block;
    font-weight: 800;
    margin-right: 8px;
}

.clhs-view-invoice-btn,
.clhs-view-payment-btn {
    background: #06b6d4;
    border: 0;
    border-radius: 5px;
    color: #ffffff;
    display: inline-block;
    font-size: 13px;
    font-weight: 800;
    margin-left: 6px;
    padding: 6px 10px;
}

.clhs-view-invoice-btn:hover,
.clhs-view-payment-btn:hover {
    background: #0891b2;
}

.clhs-total-row td {
    background: #111827 !important;
    color: #ffffff;
    font-size: 18px;
    font-weight: 900;
}

.clhs-empty-state {
    color: #64748b;
    font-size: 18px !important;
    font-weight: 800;
    padding: 34px !important;
    text-align: center;
}

.clhs-empty-state i {
    display: block;
    font-size: 30px;
    margin-bottom: 8px;
}

.credit-note-toolbar {
    margin-top: 26px;
}

.invoice-modal, .payment-modal, .credit-note-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); overflow: auto; }
.invoice-modal-content, .payment-modal-content, .credit-note-modal-content { background-color: #fff; margin: 20px auto; width: 90%; max-width: 980px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); }
.invoice-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; border-radius: 8px 8px 0 0; }
.payment-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-radius: 8px 8px 0 0; }
.credit-note-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border-radius: 8px 8px 0 0; }
.invoice-modal-header h3, .payment-modal-header h3, .credit-note-modal-header h3 { margin: 0; font-size: 1.25rem; }
.invoice-modal-close, .payment-modal-close, .credit-note-modal-close { background: none; border: none; color: white; font-size: 28px; cursor: pointer; line-height: 1; }
.invoice-modal-body, .payment-modal-body, .credit-note-modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
.invoice-modal-footer, .payment-modal-footer, .credit-note-modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 15px 20px; border-top: 1px solid #e5e7eb; background: #f9fafb; border-radius: 0 0 8px 8px; }
.btn-print, .btn-close-modal { padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-print { background: #f97316; color: white; border: none; }
.btn-print:hover { background: #ea580c; }
.credit-note-print-btn { background: #d97706; }
.credit-note-print-btn:hover { background: #b45309; }
.btn-close-modal { background: #e5e7eb; color: #374151; border: 1px solid #d1d5db; }
.invoice-display { font-family: 'Noto Sans', Arial, sans-serif; color: #1f2937; }
.invoice-display .inv-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 0.875rem; }
.invoice-display .inv-meta-right { text-align: right; }
.invoice-display .inv-badge { display: inline-block; background: #1f2937; color: white; padding: 4px 12px; font-size: 0.75rem; text-transform: uppercase; }
.invoice-display table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 0.875rem; }
.invoice-display th, .invoice-display td { border: 1px solid #1f2937; padding: 8px 10px; text-align: left; }
.invoice-display th { background: #f97316; color: white; font-weight: 600; }
.invoice-display .text-right { text-align: right; }
.invoice-display .total-row { font-weight: 700; background: #f9fafb; }
.payment-display { font-family: 'Noto Sans', Arial, sans-serif; color: #1f2937; }
.payment-display .receipt-header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 15px; margin-bottom: 20px; }
.payment-display .receipt-header h2 { font-size: 1.5rem; margin: 0; text-transform: uppercase; letter-spacing: 1px; color: #10b981; }
.payment-display .receipt-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; font-size: 0.875rem; }
.payment-display .receipt-meta-right { text-align: right; }
.payment-display .receipt-badge { display: inline-block; background: #10b981; color: white; padding: 4px 12px; font-size: 0.75rem; text-transform: uppercase; border-radius: 4px; }
.payment-display .amount-box { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0; }
.payment-display .amount-value { font-size: 2rem; font-weight: 700; }
.payment-display .payment-details { background: #f9fafb; padding: 15px; border-radius: 8px; margin-top: 20px; }
.payment-display .payment-details-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb; }
.payment-display .payment-details-label { font-weight: 600; color: #4b5563; }

@media (max-width: 1100px) {
    .clhs-top-grid,
    .clhs-customer-meta {
        grid-template-columns: 1fr;
    }

    .clhs-toolbar {
        align-items: stretch;
        flex-direction: column;
    }

    .clhs-toolbar-actions {
        justify-content: flex-start;
    }
}

@media (max-width: 700px) {
    .clhs-date-grid,
    .clhs-summary-head {
        grid-template-columns: 1fr;
    }

    .clhs-summary-head {
        align-items: stretch;
        flex-direction: column;
    }

    .clhs-print-btn,
    .clhs-print-all-btn,
    .customer-ledger-payment-btn,
    .clhs-cheque-btn {
        justify-content: center;
        width: 100%;
    }

    .clhs-toolbar-actions {
        flex-direction: column;
    }
}
</style>

<script>
function openPdfInNewTab(event, url) {
    event.preventDefault();
    var newTab = window.open(url, '_blank');
    newTab.focus();
}

function openInvoiceModal(invoiceId) {
    const modal = document.getElementById('invoiceModal');
    const body = document.getElementById('invoiceModalBody');
    const printLink = document.getElementById('invoicePrintLink');
    printLink.href = '{{ url("billno/pdf/convert") }}?invoiceid=' + invoiceId;
    body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading invoice...</p></div>';
    modal.style.display = 'block';
    fetch('{{ route("api.invoice.data") }}?invoiceid=' + invoiceId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
    .then(data => {
        if (data.error) throw new Error(data.error);
        let html = '<div class="invoice-display">';
        html += '<div class="inv-meta">';
        html += '<div>';
        html += '<div style="margin-bottom: 8px;"><strong>INVOICE NO: ' + data.invoice_id + '</strong></div>';
        html += '<div style="margin-bottom: 4px;"><strong>Name:</strong> ' + (data.customer.name || 'N/A') + '</div>';
        html += '<div style="margin-bottom: 4px;"><strong>Address:</strong> ' + (data.customer.address || 'N/A') + '</div>';
        if (data.customer.phoneno) html += '<div style="margin-bottom: 4px;"><strong>Contact:</strong> ' + data.customer.phoneno + '</div>';
        html += '<div><strong>Customer Id:</strong> ' + (data.customer.id || 'N/A') + '</div>';
        html += '</div>';
        html += '<div class="inv-meta-right">';
        html += '<span class="inv-badge">INVOICE TYPE: ' + (data.type || 'credit').toUpperCase() + '</span>';
        html += '<div style="margin-top: 15px;">';
        html += '<div style="margin-bottom: 4px;"><strong>Date:</strong> ' + data.date + '</div>';
        html += '<div><strong>Miti:</strong> ' + (data.nepali_date || '') + '</div>';
        html += '</div>';
        html += '</div>';
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
        html += '<div class="footer-info" style="margin-top: 15px; font-size: 0.875rem; color: #6b7280;"><p>Bill Created by: ' + (data.added_by || 'System') + '</p></div>';
        html += '</div>';
        body.innerHTML = html;
    })
    .catch(error => {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;"><i class="fas fa-exclamation-circle fa-2x"></i><p>Error: ' + error.message + '</p></div>';
    });
}
function closeInvoiceModal() { document.getElementById('invoiceModal').style.display = 'none'; }

function openPaymentModal(paymentId) {
    const modal = document.getElementById('paymentModal');
    const body = document.getElementById('paymentModalBody');
    const printLink = document.getElementById('paymentPrintLink');
    printLink.href = '{{ route("cashreceipt.convert") }}?receiptno=' + paymentId;
    body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading payment...</p></div>';
    modal.style.display = 'block';
    fetch('{{ route("api.payment.data") }}?paymentid=' + paymentId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
    .then(data => {
        if (data.error) throw new Error(data.error);
        let html = '<div class="payment-display">';
        html += '<div class="receipt-header"><h2>Payment Receipt</h2></div>';
        html += '<div class="receipt-meta">';
        html += '<div>';
        html += '<div style="margin-bottom: 4px;"><strong>Receipt No:</strong> ' + data.receipt_no + '</div>';
        html += '<div style="margin-bottom: 4px;"><strong>Customer:</strong> ' + (data.customer.name || 'N/A') + '</div>';
        html += '<div><strong>Address:</strong> ' + (data.customer.address || 'N/A') + '</div>';
        html += '</div>';
        html += '<div class="receipt-meta-right">';
        html += '<span class="receipt-badge">' + data.mode.toUpperCase() + '</span>';
        html += '<div style="margin-top: 15px;">';
        html += '<div style="margin-bottom: 4px;"><strong>Date:</strong> ' + data.date + '</div>';
        html += '<div><strong>Miti:</strong> ' + (data.nepali_date || '') + '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '<div class="amount-box"><div>Amount Received</div><div class="amount-value">Rs ' + parseFloat(data.amount || 0).toFixed(2) + '</div></div>';
        html += '<div class="payment-details">';
        html += '<div class="payment-details-row"><span class="payment-details-label">Payment Mode:</span><span>' + data.mode + '</span></div>';
        html += '<div class="payment-details-row"><span class="payment-details-label">Particulars:</span><span>' + (data.particulars || 'N/A') + '</span></div>';
        html += '</div></div>';
        body.innerHTML = html;
    })
    .catch(error => {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;"><i class="fas fa-exclamation-circle fa-2x"></i><p>Error: ' + error.message + '</p></div>';
    });
}
function closePaymentModal() { document.getElementById('paymentModal').style.display = 'none'; }

function renderCreditNoteHtml(data) {
    let html = '<div class="invoice-display">';
    html += '<div class="inv-meta">';
    html += '<div>';
    html += '<div style="margin-bottom: 8px;"><strong>CREDIT NOTE NO: ' + data.invoice_no + '</strong></div>';
    html += '<div style="margin-bottom: 4px;"><strong>Name:</strong> ' + (data.customer.name || 'N/A') + '</div>';
    html += '<div style="margin-bottom: 4px;"><strong>Address:</strong> ' + (data.customer.address || 'N/A') + '</div>';
    if (data.customer.phoneno) html += '<div style="margin-bottom: 4px;"><strong>Contact:</strong> ' + data.customer.phoneno + '</div>';
    html += '<div><strong>Customer Id:</strong> ' + (data.customer.id || 'N/A') + '</div>';
    html += '</div>';
    html += '<div class="inv-meta-right">';
    html += '<span class="inv-badge">CREDIT NOTE / SALES RETURN</span>';
    html += '<div style="margin-top: 15px;">';
    html += '<div style="margin-bottom: 4px;"><strong>Date:</strong> ' + (data.date || '') + '</div>';
    html += '<div><strong>Miti:</strong> ' + (data.nepali_date || '') + '</div>';
    html += '</div></div></div>';
    html += '<table><thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Unit</th><th>Price</th><th>Amount</th></tr></thead><tbody>';
    data.items.forEach((item, i) => {
        html += '<tr><td>' + (i + 1) + '</td><td>' + (item.item_name || '') + '</td><td>' + (item.quantity || '') + '</td><td>' + (item.unit || '') + '</td><td>' + (item.price || '') + '</td><td>' + (item.subtotal || '') + '</td></tr>';
    });
    html += '<tr class="total-row"><td colspan="5" class="text-right"><strong>Sub-Total:</strong></td><td><strong>Rs ' + parseFloat(data.subtotal || 0).toFixed(2) + '</strong></td></tr>';
    html += '<tr class="total-row"><td colspan="5" class="text-right"><strong>Extra Discount:</strong></td><td><strong>Rs ' + parseFloat(data.discount || 0).toFixed(2) + '</strong></td></tr>';
    html += '<tr class="total-row"><td colspan="5" class="text-right"><strong>Total Amount:</strong></td><td><strong>Rs ' + parseFloat(data.total || 0).toFixed(2) + '</strong></td></tr>';
    if (data.notes) html += '<tr><td colspan="6"><strong>Notes:</strong> ' + data.notes + '</td></tr>';
    html += '</tbody></table>';
    html += '<div class="footer-info" style="margin-top: 15px; font-size: 0.875rem; color: #6b7280;"><p>Created by: ' + (data.added_by || 'System') + '</p></div>';
    html += '</div>';
    return html;
}

function openCreditNoteModal(invoiceId) {
    const modal = document.getElementById('creditNoteModal');
    const body = document.getElementById('creditNoteModalBody');
    const printLink = document.getElementById('creditNotePrintLink');
    printLink.href = '{{ route("creditnotesbillno.convert") }}?invoiceid=' + invoiceId;
    body.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Loading credit note...</p></div>';
    modal.style.display = 'block';
    fetch('{{ route("api.creditnote.data") }}?invoiceid=' + invoiceId, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => { if (!response.ok) throw new Error('HTTP ' + response.status); return response.json(); })
    .then(data => {
        if (data.error) throw new Error(data.error);
        body.innerHTML = renderCreditNoteHtml(data);
    })
    .catch(error => {
        body.innerHTML = '<div style="text-align:center;padding:40px;color:#dc2626;"><i class="fas fa-exclamation-circle fa-2x"></i><p>Error: ' + error.message + '</p></div>';
    });
}
function closeCreditNoteModal() {
    const modal = document.getElementById('creditNoteModal');
    const body = document.getElementById('creditNoteModalBody');
    modal.style.display = 'none';
    body.innerHTML = '';
}

window.onclick = function(event) { if (event.target === document.getElementById('invoiceModal')) closeInvoiceModal(); if (event.target === document.getElementById('paymentModal')) closePaymentModal(); if (event.target === document.getElementById('creditNoteModal')) closeCreditNoteModal(); }
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeInvoiceModal(); closePaymentModal(); closeCreditNoteModal(); } });
</script>

</div>

@stop
