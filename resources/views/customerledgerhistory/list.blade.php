@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    @php
        $customer = $cusinfobyid->first();
        $dueAmount = (float) $dts - (float) $cts;
        $hasRows = $all && count($all) > 0;
        $hasCreditNotes = $all && collect($all)->contains('invoicetype', 'credit_note');
        $hasNilAccount = $all && collect($all)->contains('invoicetype', 'settlement');
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

                    <form action="{{ route('clhs.returnchoosendatehistroy') }}" method="get" id="chosendatepdfform">
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
                                <span class="input-group-text"><i class="fa-solid fa-calendar-days"></i> Start Date (B.S.)</span>
                                <input type="text" name="date1_bs"
                                    value="{{ old('date1_bs', request('date1_bs', !empty($fromdate) ? \App\Support\NepaliDate::adToBsString($fromdate, 'en') : '')) }}"
                                    class="form-control @error('date1_bs') is-invalid @enderror" placeholder="YYYY-MM-DD"
                                    inputmode="numeric" pattern="\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}" title="Enter B.S. date as YYYY-MM-DD">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-calendar-check"></i> End Date (B.S.)</span>
                                <input type="text" name="date2_bs"
                                    value="{{ old('date2_bs', request('date2_bs', !empty($todate) ? \App\Support\NepaliDate::adToBsString($todate, 'en') : '')) }}"
                                    class="form-control @error('date2_bs') is-invalid @enderror" placeholder="YYYY-MM-DD"
                                    inputmode="numeric" pattern="\d{4}[-\/.]\d{1,2}[-\/.]\d{1,2}" title="Enter B.S. date as YYYY-MM-DD">
                            </div>
                        </div>

                        @if($errors->has('date1_bs') || $errors->has('date2_bs'))
                            <div class="clhs-date-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first('date1_bs') ?: $errors->first('date2_bs') }}</div>
                        @endif

                        <button type="submit" class="clhs-search-btn">
                            <i class="fas fa-search"></i>
                            Search Ledger
                        </button>
                    </form>
                </section>

                @if($customeridonly)
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

                        @if(!empty(trim($customer->remarks ?? '')))
                            <div class="clhs-customer-note">
                                <span>Notes</span>
                                <b>{{ $customer->remarks }}</b>
                            </div>
                        @endif

                        <div class="clhs-due-card {{ $dueAmount < 0 ? 'is-negative' : '' }}">
                            <span>Total Due Amount</span>
                            <strong>{{ number_format($dueAmount, 2) }} -/</strong>
                            <small>{{ $amountToWords($dueAmount) }} only -/</small>
                        </div>

                        <div class="clhs-actions">
                            <a href="{{ route('cpayments.create', [
                                'customerid' => $customeridonly,
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
                @if($customeridonly)
                    <div class="clhs-toolbar-actions">
                        <a href="{{ url('/customer-ledger-dispute') . '?' . http_build_query(['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate]) }}"
                            class="clhs-primary-action dispute"
                            target="_blank">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>Dispute / Missing Bills</span>
                        </a>

                        <div class="dropdown clhs-reports-dropdown">
                            <button class="clhs-reports-trigger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="reports-trigger-icon"><i class="fa-solid fa-print"></i></span>
                                <span><strong>Print & Reports</strong><small>5 available actions</small></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end clhs-reports-menu">
                                <div class="reports-menu-heading"><strong>Print & Reports</strong><small>Choose the document you need</small></div>

                                <a href="{{ route('clhspdf.convert', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate]) }}"
                                    onclick="openPdfInNewTab(event, this.href); return false;"
                                    class="clhs-report-item {{ !$hasRows ? 'pdf-link-disabled' : '' }}">
                                    <span class="report-icon ledger"><i class="fa-solid fa-book-open"></i></span>
                                    <span><strong>Print Ledger</strong><small>Complete customer ledger statement</small></span>
                                    <i class="fa-solid fa-chevron-right report-arrow"></i>
                                </a>
                                <a href="{{ route('print.all.customer.invoices', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate, 'invoice_type' => 'credit']) }}"
                                    onclick="openPdfInNewTab(event, this.href); return false;"
                                    class="clhs-report-item {{ !$hasRows ? 'pdf-link-disabled' : '' }}">
                                    <span class="report-icon invoices"><i class="fa-regular fa-file-lines"></i></span>
                                    <span><strong>All Invoices</strong><small>Print every credit invoice in this period</small></span>
                                    <i class="fa-solid fa-chevron-right report-arrow"></i>
                                </a>
                                <a href="{{ route('print.all.customer.creditnotes', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate]) }}"
                                    onclick="openPdfInNewTab(event, this.href); return false;"
                                    class="clhs-report-item {{ !$hasCreditNotes ? 'pdf-link-disabled' : '' }}">
                                    <span class="report-icon creditnotes"><i class="fa-solid fa-file-invoice"></i></span>
                                    <span><strong>All Credit Notes</strong><small>Print available customer credit notes</small></span>
                                    <i class="fa-solid fa-chevron-right report-arrow"></i>
                                </a>
                                <a href="{{ route('customer.printallcashreceipts', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate, 'ledger_mode' => 'credit']) }}"
                                    onclick="openPdfInNewTab(event, this.href); return false;"
                                    class="clhs-report-item">
                                    <span class="report-icon receipts"><i class="fa-solid fa-receipt"></i></span>
                                    <span><strong>All Cash Receipts</strong><small>Print customer payment receipts</small></span>
                                    <i class="fa-solid fa-chevron-right report-arrow"></i>
                                </a>
                                <a href="{{ route('clhspdf.convert', ['customerid' => $customeridonly, 'date1' => $fromdate, 'date2' => $todate, 'after_nil' => 1]) }}"
                                    onclick="openPdfInNewTab(event, this.href); return false;"
                                    class="clhs-report-item {{ !$hasNilAccount ? 'pdf-link-disabled' : '' }}">
                                    <span class="report-icon after-nil"><i class="fa-solid fa-rotate-right"></i></span>
                                    <span><strong>Ledger After Nil Account</strong><small>Print entries after the latest settlement</small></span>
                                    <i class="fa-solid fa-chevron-right report-arrow"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="clhs-table-wrap">
                <table class="clhs-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nepali Date</th>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Voucher Type</th>
                            <th>Invoice Type</th>
                            <th>Invoice No</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($hasRows)
                            @foreach ($all as $i)
                                @php
                                    $isPayment = $i->invoicetype == 'payment';
                                    $isSettlement = $i->invoicetype == 'settlement';
                                    $isCreditNote = $i->invoicetype == 'credit_note';
                                @endphp
                                <tr class="{{ $isPayment ? 'is-payment-row' : '' }} {{ $isSettlement ? 'is-settlement-row' : '' }} {{ \Carbon\Carbon::parse($i->date)->isToday() ? 'clhs-today-row' : '' }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="clhs-row-date-picker">
                                            <span>{{ \App\Support\NepaliDate::adToBsString($i->date, 'en') }}</span>
                                            <button type="button" onclick="setLedgerDateFromRow('{{ \App\Support\NepaliDate::adToBsString($i->date, 'en') }}', 'date1_bs')" title="Use as start date">Start</button>
                                            <button type="button" onclick="setLedgerDateFromRow('{{ \App\Support\NepaliDate::adToBsString($i->date, 'en') }}', 'date2_bs')" title="Use as end date">End</button>
                                        </div>
                                    </td>
                                    <td>{{ $i->date }}</td>
                                    <td>{{ $i->particulars }}</td>
                                    <td>{{ $i->voucher_type }}</td>
                                    <td>
                                        <span class="clhs-type-badge {{ $isPayment ? 'payment' : ($isSettlement ? 'settlement' : ($isCreditNote ? 'credit-note' : 'credit')) }}">
                                            {{ $isSettlement ? 'Nil Account' : ($isCreditNote ? 'Credit Note' : $i->invoicetype) }}
                                            @if($isPayment)
                                                CR-({{ $i->id }})
                                            @endif
                                        </span>
                                        @if($isPayment)
                                            <button type="button"
                                                onclick="openPaymentModal({{ $i->id }})"
                                                class="clhs-view-payment-btn">
                                                View
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($i->invoiceid))
                                            <span class="clhs-invoice-number">{{ $isCreditNote ? 'CN-' : '' }}{{ $i->invoiceid }}</span>
                                            @if($isCreditNote)
                                                <button type="button"
                                                    onclick="openCreditNoteModal({{ $i->invoiceid }})"
                                                    class="clhs-view-invoice-btn">
                                                    View
                                                </button>
                                            @else
                                                <button type="button"
                                                    onclick="openInvoiceModal({{ $i->invoiceid }})"
                                                    class="clhs-view-invoice-btn">
                                                    View
                                                </button>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-end">{{ number_format((float) $i->debit, 2) }}</td>
                                    <td class="text-end">{{ number_format((float) $i->credit, 2) }}</td>
                                    <td>{{ $i->created_at }}</td>
                                </tr>
                            @endforeach
                            <tr class="clhs-total-row">
                                <td colspan="7" class="text-end">Total</td>
                                <td class="text-end total-amount">{{ number_format((float) $dts, 2) }}</td>
                                <td class="text-end total-amount">{{ number_format((float) $cts, 2) }}</td>
                                <td></td>
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
        </div>

        <div id="invoiceModal" class="invoice-modal">
            <div class="invoice-modal-content">
                <div class="invoice-modal-header">
                    <h3>Invoice Details</h3>
                    <button class="invoice-modal-close" onclick="closeInvoiceModal()">&times;</button>
                </div>
                <div class="invoice-modal-body" id="invoiceModalBody"></div>
                <div class="invoice-modal-footer">
                    <a id="invoicePrintLink" href="#" target="_blank" class="btn-print">
                        <i class="fas fa-print"></i> Print PDF
                    </a>
                    <button class="btn-close-modal" onclick="closeInvoiceModal()">Close</button>
                </div>
            </div>
        </div>

        <div id="paymentModal" class="payment-modal">
            <div class="payment-modal-content">
                <div class="payment-modal-header">
                    <h3>Payment Details</h3>
                    <button class="payment-modal-close" onclick="closePaymentModal()">&times;</button>
                </div>
                <div class="payment-modal-body" id="paymentModalBody"></div>
                <div class="payment-modal-footer">
                    <a id="paymentPrintLink" href="#" target="_blank" class="btn-print payment-print-btn">
                        <i class="fas fa-print"></i> Print Receipt
                    </a>
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
    </div>

    <script>
        function openPdfInNewTab(event, url) {
            event.preventDefault();
            var newTab = window.open(url, '_blank');
            if (newTab) newTab.focus();
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
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
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
                        html += '<tr><td>' + (i + 1) + '</td><td>' + (item.item_name || '') + '</td><td>' + (item.quantity || '') + '</td><td>' + (item.price || '') + '</td><td>' + (item.subtotal || '') + '</td></tr>';
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
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
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

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }

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

        window.addEventListener('click', function(event) {
            if (event.target === document.getElementById('invoiceModal')) closeInvoiceModal();
            if (event.target === document.getElementById('paymentModal')) closePaymentModal();
            if (event.target === document.getElementById('creditNoteModal')) closeCreditNoteModal();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeInvoiceModal();
                closePaymentModal();
                closeCreditNoteModal();
            }
        });
    </script>

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
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
            font-weight: 700;
            gap: 7px;
        }

        .clhs-date-grid .form-control {
            border-color: #bfdbfe;
            font-weight: 700;
            letter-spacing: .03em;
        }

        .clhs-date-grid .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .14);
        }

        .clhs-date-error {
            align-items: center;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
            color: #b91c1c;
            display: flex;
            font-size: 13px;
            font-weight: 800;
            gap: 7px;
            margin: -6px 0 14px;
            padding: 9px 11px;
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

        .clhs-customer-note {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 5px solid #f97316;
            border-radius: 6px;
            margin: -2px 0 14px;
            padding: 10px 12px;
        }

        .clhs-customer-note span {
            color: #9a3412;
            display: block;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .clhs-customer-note b {
            color: #111827;
            display: block;
            font-size: 16px;
            line-height: 1.35;
            margin-top: 3px;
            white-space: pre-wrap;
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

        .customer-ledger-payment-btn.disabled {
            opacity: .55;
            pointer-events: none;
        }

        .pdf-link-disabled {
            opacity: .55;
            pointer-events: none;
        }

        .clhs-toolbar-actions,
        .clhs-print-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .clhs-print-stack {
            margin-top: 14px;
        }

        .clhs-toolbar-actions {
            align-items: stretch;
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
            width: auto;
        }

        .clhs-print-all-btn i {
            font-size: 18px;
        }

        .clhs-print-all-btn span,
        .clhs-print-all-btn i {
            color: #ffffff !important;
        }

        .clhs-print-all-btn.invoices {
            background: #198754;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.invoices:hover {
            background: #146c43;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.receipts {
            background: #17c5df;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.receipts:hover {
            background: #0fb5ce;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.creditnotes {
            background: #d97706;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.creditnotes:hover {
            background: #b45309;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.after-nil {
            background: #7c3aed;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.after-nil:hover {
            background: #6d28d9;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.dispute {
            background: #dc2626;
            color: #ffffff !important;
        }

        .clhs-print-all-btn.dispute:hover {
            background: #b91c1c;
            color: #ffffff !important;
        }

        .clhs-toolbar {
            align-items: center;
            background: #ffffff;
            border: 1px solid #dbe3ee;
            border-left: 5px solid #4f46e5;
            border-radius: 10px;
            box-shadow: 0 5px 16px rgba(15, 23, 42, .06);
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin: 18px 0 10px;
            padding: 14px 16px;
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

        .clhs-primary-action,
        .clhs-reports-trigger {
            align-items: center;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 800;
            gap: 9px;
            justify-content: center;
            min-height: 46px;
            padding: 9px 14px;
            text-decoration: none !important;
            transition: box-shadow .15s ease, transform .15s ease;
            white-space: nowrap;
        }

        .clhs-primary-action.dispute {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c !important;
        }

        .clhs-primary-action.dispute span,
        .clhs-primary-action.dispute i {
            color: #be123c !important;
        }

        .clhs-primary-action:hover,
        .clhs-reports-trigger:hover,
        .clhs-reports-trigger[aria-expanded="true"] {
            box-shadow: 0 6px 14px rgba(15, 23, 42, .13);
            transform: translateY(-1px);
        }

        .clhs-reports-trigger {
            background: #1e3a8a;
            color: #ffffff;
            min-width: 190px;
            padding: 6px 13px 6px 7px;
            text-align: left;
        }

        .clhs-reports-trigger:after {
            margin-left: auto;
        }

        .reports-trigger-icon {
            align-items: center;
            background: rgba(255,255,255,.16);
            border-radius: 7px;
            color: #ffffff !important;
            display: flex;
            flex: 0 0 35px;
            height: 35px;
            justify-content: center;
        }

        .clhs-reports-trigger>span:nth-child(2) {
            display: flex;
            flex-direction: column;
        }

        .clhs-reports-trigger strong,
        .clhs-reports-trigger small {
            color: #ffffff;
            line-height: 1.2;
        }

        .clhs-reports-trigger small {
            font-size: 10px;
            font-weight: 600;
            opacity: .72;
        }

        .clhs-reports-menu {
            border: 1px solid #dbe3ee;
            border-radius: 12px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, .2);
            margin-top: 8px !important;
            overflow: hidden;
            padding: 7px;
            width: 390px;
            z-index: 1050;
        }

        .reports-menu-heading {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 8px 8px 4px 4px;
            display: flex;
            flex-direction: column;
            margin-bottom: 5px;
            padding: 10px 12px;
        }

        .reports-menu-heading strong {
            color: #172554;
            font-size: 14px;
        }

        .reports-menu-heading small {
            color: #64748b;
            font-size: 11px;
        }

        .clhs-report-item {
            align-items: center;
            border: 1px solid transparent;
            border-radius: 9px;
            color: #334155 !important;
            display: flex;
            gap: 10px;
            padding: 9px 10px;
            text-decoration: none !important;
            transition: background .15s ease, border-color .15s ease;
        }

        .clhs-report-item:hover {
            background: #f8fafc;
            border-color: #dbeafe;
        }

        .clhs-report-item>span:nth-child(2) {
            display: flex;
            flex: 1;
            flex-direction: column;
            min-width: 0;
        }

        .clhs-report-item strong {
            color: #1e293b;
            font-size: 13px;
            line-height: 1.2;
        }

        .clhs-report-item small {
            color: #64748b;
            font-size: 10px;
            line-height: 1.25;
            margin-top: 2px;
        }

        .report-icon {
            align-items: center;
            border-radius: 8px;
            color: #ffffff !important;
            display: flex;
            flex: 0 0 36px;
            height: 36px;
            justify-content: center;
        }

        .report-icon.ledger { background: #4f46e5; }
        .report-icon.invoices { background: #059669; }
        .report-icon.creditnotes { background: #d97706; }
        .report-icon.receipts { background: #0891b2; }
        .report-icon.after-nil { background: #7c3aed; }
        .report-arrow { color: #94a3b8; font-size: 10px; }

        .clhs-report-item.pdf-link-disabled {
            filter: grayscale(.65);
            opacity: .45;
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

        .clhs-table th:nth-child(8),
        .clhs-table th:nth-child(9),
        .clhs-table td:nth-child(8),
        .clhs-table td:nth-child(9) {
            min-width: 130px;
            white-space: nowrap;
        }

        .clhs-table th:nth-child(2),
        .clhs-table td:nth-child(2) {
            min-width: 150px;
            white-space: nowrap;
            width: 1%;
        }

        .clhs-table th:nth-child(3),
        .clhs-table td:nth-child(3) {
            min-width: 125px;
            white-space: nowrap;
        }

        .clhs-table th:nth-child(10),
        .clhs-table td:nth-child(10) {
            min-width: 175px;
            white-space: nowrap;
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

        .clhs-table tbody tr.is-settlement-row:hover td {
            background: #ecfff5 !important;
        }

        .clhs-table tbody tr.clhs-today-row td {
            background: red !important;
            color: #ffffff !important;
        }

        .clhs-table tbody tr.clhs-today-row:hover td {
            background: red !important;
        }

        .clhs-table tbody tr.clhs-today-row .clhs-type-badge {
            background: transparent;
            color: #ffffff;
            padding-left: 0;
        }

        .clhs-table tbody tr.clhs-today-row .clhs-view-invoice-btn {
            background: #06b6d4;
            color: #ffffff;
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

        .clhs-type-badge.payment {
            background: #dcfce7;
            color: #166534;
        }

        .clhs-type-badge.credit-note {
            background: #fef3c7;
            color: #92400e;
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

        .clhs-view-invoice-btn {
            background: #06b6d4;
            border: 0;
            border-radius: 5px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 10px;
        }

        .clhs-view-invoice-btn:hover {
            background: #0891b2;
        }

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

        .clhs-view-payment-btn:hover {
            background: #0891b2;
        }

        .clhs-total-row td {
            background: #111827 !important;
            color: #ffffff !important;
            font-size: 20px;
            font-weight: 900;
            white-space: nowrap;
        }

        .clhs-total-row .total-amount {
            font-size: 22px;
            min-width: 150px;
            padding: 16px 14px;
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

        .invoice-modal {
            background-color: rgba(15, 23, 42, 0.62);
            display: none;
            height: 100%;
            left: 0;
            overflow: auto;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 99999;
        }

        .payment-modal,
        .credit-note-modal {
            background-color: rgba(15, 23, 42, 0.62);
            display: none;
            height: 100%;
            left: 0;
            overflow: auto;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 99999;
        }

        .invoice-modal-content {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
            margin: 22px auto;
            max-width: 900px;
            overflow: hidden;
            width: 90%;
        }

        .payment-modal-content,
        .credit-note-modal-content {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
            margin: 22px auto;
            max-width: 980px;
            overflow: hidden;
            width: 90%;
        }

        .invoice-modal-header {
            align-items: center;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
        }

        .payment-modal-header {
            align-items: center;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
        }

        .credit-note-modal-header {
            align-items: center;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            padding: 15px 20px;
        }

        .invoice-modal-header h3,
        .payment-modal-header h3,
        .credit-note-modal-header h3 {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0;
        }

        .invoice-modal-close,
        .payment-modal-close,
        .credit-note-modal-close {
            background: none;
            border: none;
            color: #ffffff;
            cursor: pointer;
            font-size: 28px;
            line-height: 1;
        }

        .invoice-modal-body,
        .payment-modal-body,
        .credit-note-modal-body {
            max-height: 70vh;
            overflow-y: auto;
            padding: 20px;
        }

        .invoice-modal-footer,
        .payment-modal-footer,
        .credit-note-modal-footer {
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            padding: 15px 20px;
        }

        .btn-print,
        .btn-close-modal {
            align-items: center;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            font-size: 0.875rem;
            font-weight: 700;
            gap: 6px;
            padding: 8px 16px;
            text-decoration: none;
        }

        .btn-print {
            background: #f97316;
            border: 0;
            color: #ffffff;
        }

        .btn-print:hover {
            background: #ea580c;
            color: #ffffff;
        }

        .payment-print-btn {
            background: #10b981;
        }

        .payment-print-btn:hover {
            background: #059669;
        }

        .credit-note-print-btn {
            background: #d97706;
        }

        .credit-note-print-btn:hover {
            background: #b45309;
        }

        .btn-close-modal {
            background: #e5e7eb;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .invoice-display {
            color: #1f2937;
            font-family: 'Noto Sans', Arial, sans-serif;
        }

        .invoice-display .inv-meta {
            display: grid;
            font-size: 0.875rem;
            gap: 15px;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 20px;
        }

        .invoice-display .inv-meta-right {
            text-align: right;
        }

        .invoice-display .inv-badge {
            background: #1f2937;
            color: #ffffff;
            display: inline-block;
            font-size: 0.75rem;
            padding: 4px 12px;
            text-transform: uppercase;
        }

        .invoice-display table {
            border-collapse: collapse;
            display: table !important;
            font-size: 0.875rem;
            margin-top: 15px;
            width: 100%;
        }

        .invoice-display thead {
            display: table-header-group !important;
        }

        .invoice-display tbody {
            display: table-row-group !important;
            height: auto !important;
            overflow: visible !important;
        }

        .invoice-display tr {
            display: table-row !important;
            width: auto !important;
        }

        .invoice-display th,
        .invoice-display td {
            border: 1px solid #1f2937 !important;
            display: table-cell !important;
            padding: 8px 10px;
            text-align: left;
        }

        .invoice-display th {
            background: #f97316;
            color: #ffffff;
            font-weight: 800;
        }

        .invoice-display .text-right {
            text-align: right;
        }

        .invoice-display .total-row {
            background: #f9fafb;
            font-weight: 800;
        }

        .payment-display {
            color: #1f2937;
            font-family: 'Noto Sans', Arial, sans-serif;
        }

        .payment-display .receipt-header {
            border-bottom: 2px solid #10b981;
            margin-bottom: 20px;
            padding-bottom: 15px;
            text-align: center;
        }

        .payment-display .receipt-header h2 {
            color: #10b981;
            font-size: 1.5rem;
            letter-spacing: 1px;
            margin: 0;
            text-transform: uppercase;
        }

        .payment-display .receipt-meta {
            display: grid;
            font-size: 0.875rem;
            gap: 15px;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 20px;
        }

        .payment-display .receipt-meta-right {
            text-align: right;
        }

        .payment-display .receipt-badge {
            background: #10b981;
            border-radius: 4px;
            color: #ffffff;
            display: inline-block;
            font-size: 0.75rem;
            padding: 4px 12px;
            text-transform: uppercase;
        }

        .payment-display .amount-box {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 8px;
            color: #ffffff;
            margin: 20px 0;
            padding: 20px;
            text-align: center;
        }

        .payment-display .amount-value {
            font-size: 2rem;
            font-weight: 800;
        }

        .payment-display .payment-details {
            background: #f9fafb;
            border-radius: 8px;
            margin-top: 20px;
            padding: 15px;
        }

        .payment-display .payment-details-row {
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }

        .payment-display .payment-details-label {
            color: #4b5563;
            font-weight: 800;
        }

        .clhs-row-date-picker {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            min-width: 0;
        }

        .clhs-row-date-picker span {
            flex-basis: 100%;
            font-weight: 800;
        }

        .clhs-row-date-picker button {
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            color: #2f3fd0;
            font-size: 0.75rem;
            font-weight: 800;
            line-height: 1;
            padding: 5px 7px;
        }

        .clhs-row-date-picker button:hover {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
        }

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
                width: 100%;
            }

            .clhs-primary-action,
            .clhs-reports-dropdown,
            .clhs-reports-trigger {
                width: 100%;
            }

            .clhs-reports-menu {
                max-width: calc(100vw - 38px);
                width: 390px;
            }
        }
    </style>

    <script>
        window.setLedgerDateFromRow = function (date, fieldName) {
            const input = document.querySelector('#chosendatepdfform input[name="' + fieldName + '"]');
            if (!input) return;

            input.value = date;
            input.dispatchEvent(new Event('change', { bubbles: true }));
            input.focus();
        };

        $(document).on('customer:selected', function () {
            const form = document.getElementById('chosendatepdfform');
            const customerInput = document.getElementById('customerIdInput');

            if (form && customerInput && customerInput.value) {
                form.submit();
            }
        });
    </script>
@stop
