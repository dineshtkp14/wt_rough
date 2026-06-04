@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    <div class="main-content temporary-invoice-index">
        @yield('breadcrumb')

        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="temporary-page-head">
                <div>
                    <span>Temporary Invoice</span>
                    <h3>Temporary Invoices</h3>
                </div>
                <a href="{{ route('temporaryinvoice.create') }}" class="temporary-primary-btn">
                    <i class="fa-solid fa-plus"></i> New Temporary Invoice
                </a>
            </div>

            <form class="temporary-filter-panel" method="get" action="{{ route('temporaryinvoice.index') }}">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-lg-5 col-md-12">
                            <label class="temporary-field-label">Search</label>
                            <input type="text" name="search" class="form-control" id="temporaryInvoiceLiveSearch"
                                placeholder="Customer name, contact, address, or invoice no"
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <label class="temporary-field-label">From</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-lg-3 col-md-5">
                            <label class="temporary-field-label">To</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-lg-1 col-md-2 temporary-filter-reset">
                            <a href="{{ route('temporaryinvoice.index') }}" class="temporary-icon-btn" title="Reset filters">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="temporary-table-panel">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th style="width: 180px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="temporaryInvoiceTableBody">
                                @include('temporaryinvoice._rows', ['temporaryInvoices' => $temporaryInvoices])
                            </tbody>
                        </table>
                    </div>

                    <div class="temporary-pagination" id="temporaryInvoicePagination">
                        {{ $temporaryInvoices->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="temporary-invoice-modal" id="temporaryInvoiceModal" aria-hidden="true">
            <div class="temporary-invoice-modal-dialog">
                <div class="temporary-invoice-modal-header">
                    <div>
                        <h4 id="temporaryInvoiceModalTitle">Invoice Details</h4>
                    </div>
                    <button type="button" class="temporary-invoice-modal-close" id="temporaryInvoiceModalClose">
                        &times;
                    </button>
                </div>
                <div class="temporary-invoice-modal-body" id="temporaryInvoiceModalBody">
                    <div class="temporary-invoice-modal-loading">Loading...</div>
                </div>
                <div class="temporary-invoice-modal-footer">
                    <a href="#" class="btn temporary-invoice-print-btn" id="temporaryInvoiceModalPrint" target="_blank">
                        <i class="fa-solid fa-print"></i> Print PDF
                    </a>
                    <button type="button" class="btn btn-secondary" id="temporaryInvoiceModalCloseFooter">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var input = document.getElementById('temporaryInvoiceLiveSearch');
            var form = input ? input.closest('form') : null;
            var tbody = document.getElementById('temporaryInvoiceTableBody');
            var pagination = document.getElementById('temporaryInvoicePagination');
            var modal = document.getElementById('temporaryInvoiceModal');
            var modalTitle = document.getElementById('temporaryInvoiceModalTitle');
            var modalBody = document.getElementById('temporaryInvoiceModalBody');
            var modalPrint = document.getElementById('temporaryInvoiceModalPrint');
            var printFrame = null;
            var timer = null;

            if (!input || !form || !tbody || !pagination) return;

            function money(value) {
                return Number(value || 0).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>"']/g, function (char) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    }[char];
                });
            }

            function amountWords(value) {
                var words = convertNumberToWords(Math.floor(value || 0));
                return words.toLowerCase().indexOf('only') === -1 ? words + ' only /-' : words;
            }

            function openModal() {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            }

            function closeModal() {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }

            function printInvoice(url) {
                if (!printFrame) {
                    printFrame = document.createElement('iframe');
                    printFrame.style.position = 'fixed';
                    printFrame.style.right = '0';
                    printFrame.style.bottom = '0';
                    printFrame.style.width = '0';
                    printFrame.style.height = '0';
                    printFrame.style.border = '0';
                    document.body.appendChild(printFrame);
                }

                printFrame.onload = function () {
                    setTimeout(function () {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                    }, 200);
                };

                printFrame.src = url;
            }

            function invoiceHtml(data) {
                var rows = data.items.map(function (item, index) {
                    return '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td>' + escapeHtml(item.item_name) + '</td>' +
                        '<td>' + escapeHtml(item.quantity) + '</td>' +
                        '<td>' + money(item.price) + '</td>' +
                        '<td>' + money(item.subtotal) + '</td>' +
                    '</tr>';
                }).join('');

                return '<div class="temporary-invoice-popup-meta-row">' +
                    '<div>' +
                    '<p><b>INVOICE NO:</b> ' + escapeHtml(data.invoice_number) + '</p>' +
                    '<p><b>Name:</b> ' + escapeHtml(data.customer_name) + '</p>' +
                    '<p><b>Address:</b> ' + escapeHtml(data.customer_address || '-') + '</p>' +
                    '<p><b>Contact:</b> ' + escapeHtml(data.contact_number || '-') + '</p>' +
                    '</div>' +
                    '<div class="temporary-invoice-popup-meta-right">' +
                    '<div class="temporary-invoice-popup-badge">INVOICE TYPE: TEMPORARY</div>' +
                    '<p><b>Date:</b> ' + escapeHtml(data.invoice_date) + '</p>' +
                    '</div></div>' +
                    '<div class="table-responsive"><table class="temporary-invoice-popup-table">' +
                    '<colgroup><col style="width: 6%;"><col style="width: 46%;"><col style="width: 12%;"><col style="width: 18%;"><col style="width: 18%;"></colgroup>' +
                    '<thead><tr><th>#</th><th>ITEM</th><th>QTY</th><th>PRICE</th><th>AMOUNT</th></tr></thead>' +
                    '<tbody>' + rows + '</tbody>' +
                    '<tfoot>' +
                    '<tr><td colspan="3"></td><th>Total:</th><th>Rs ' + money(data.total) + '</th></tr>' +
                    '</tfoot></table></div>' +
                    '<div class="temporary-invoice-popup-words"><b>Amount in words:</b> ' + escapeHtml(amountWords(data.total)) + '</div>' +
                    (data.notes ? '<div class="temporary-invoice-popup-notes"><b>Notes:</b> ' + escapeHtml(data.notes) + '</div>' : '');
            }

            function search(page) {
                var params = new URLSearchParams(new FormData(form));
                if (page) {
                    params.set('page', page);
                }

                fetch("{{ route('temporaryinvoice.live-search') }}?" + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        tbody.innerHTML = data.html;
                        pagination.innerHTML = data.pagination;
                    });
            }

            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    search();
                }, 450);
            });

            form.querySelectorAll('input[type="date"]').forEach(function (dateInput) {
                dateInput.addEventListener('change', search);
            });

            pagination.addEventListener('click', function (event) {
                var link = event.target.closest('a');
                if (!link) return;

                event.preventDefault();
                var url = new URL(link.href);
                search(url.searchParams.get('page') || 1);
            });

            tbody.addEventListener('click', function (event) {
                var printLink = event.target.closest('.temporary-invoice-print-direct');
                if (printLink) {
                    event.preventDefault();
                    printInvoice(printLink.dataset.url);
                    return;
                }

                var link = event.target.closest('.temporary-invoice-view-btn');
                if (!link) return;

                event.preventDefault();
                modalTitle.textContent = 'Invoice Details';
                modalPrint.href = '#';
                modalBody.innerHTML = '<div class="temporary-invoice-modal-loading">Loading...</div>';
                openModal();

                fetch(link.dataset.url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        modalTitle.textContent = 'Invoice Details';
                        modalPrint.href = data.print_url;
                        modalBody.innerHTML = invoiceHtml(data);
                    })
                    .catch(function () {
                        modalBody.innerHTML = '<div class="alert alert-danger mb-0">Invoice could not be loaded.</div>';
                    });
            });

            document.getElementById('temporaryInvoiceModalClose').addEventListener('click', closeModal);
            document.getElementById('temporaryInvoiceModalCloseFooter').addEventListener('click', closeModal);
            modalPrint.addEventListener('click', function (event) {
                event.preventDefault();
                if (modalPrint.href && modalPrint.href !== '#') {
                    printInvoice(modalPrint.href);
                }
            });
            modal.addEventListener('click', function (event) {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
            });
        })();
    </script>

    <style>
        .temporary-invoice-modal {
            align-items: flex-start;
            background: rgba(15, 23, 42, 0.62);
            display: none;
            inset: 0;
            justify-content: center;
            overflow-y: auto;
            padding: 18px 12px;
            position: fixed;
            z-index: 999999;
        }

        .temporary-invoice-modal.is-open {
            display: flex;
        }

        .temporary-invoice-modal-dialog {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
            max-height: calc(100vh - 36px);
            max-width: 900px;
            overflow: hidden;
            width: min(900px, 92vw);
        }

        .temporary-invoice-modal-header,
        .temporary-invoice-modal-footer {
            align-items: center;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 12px;
        }

        .temporary-invoice-modal-header {
            background: #0f766e;
            color: #ffffff;
        }

        .temporary-invoice-modal-header h4 {
            font-size: 23px;
            font-weight: 700;
            margin: 0;
        }

        .temporary-invoice-modal-label,
        .temporary-invoice-popup-words span {
            color: #ffffff;
            display: block;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .temporary-invoice-modal-close {
            background: transparent;
            border: 0;
            color: #ffffff;
            font-size: 24px;
            line-height: 1;
        }

        .temporary-invoice-modal-body {
            max-height: calc(100vh - 150px);
            overflow-y: auto;
            padding: 24px 26px 18px;
        }

        .temporary-invoice-modal-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            justify-content: flex-end;
        }

        .temporary-invoice-print-btn {
            background: #0f766e;
            border-color: #0f766e;
            color: #ffffff;
            font-weight: 700;
        }

        .temporary-invoice-print-btn:hover {
            background: #115e59;
            border-color: #115e59;
            color: #ffffff;
        }

        .temporary-invoice-modal-loading {
            font-size: 18px;
            font-weight: 700;
            padding: 32px;
            text-align: center;
        }

        .temporary-invoice-popup-meta-row {
            align-items: flex-start;
            display: flex;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 24px;
        }

        .temporary-invoice-popup-meta-row p {
            color: #1f2937;
            font-size: 17px;
            line-height: 1.28;
            margin: 0 0 4px;
        }

        .temporary-invoice-popup-meta-right {
            min-width: 230px;
            text-align: right;
        }

        .temporary-invoice-popup-badge {
            background: #1f2937;
            color: #ffffff;
            display: inline-block;
            font-size: 15px;
            margin-bottom: 14px;
            padding: 5px 14px;
            text-transform: uppercase;
        }

        .temporary-invoice-popup-table {
            table-layout: fixed;
        }

        .temporary-invoice-popup-table {
            border-collapse: collapse;
            font-size: 17px;
            min-width: 760px;
            width: 100%;
        }

        .temporary-invoice-popup-table thead {
            display: table-header-group !important;
        }

        .temporary-invoice-popup-table tbody {
            display: table-row-group !important;
            height: auto !important;
            overflow: visible !important;
        }

        .temporary-invoice-popup-table tfoot {
            display: table-footer-group !important;
        }

        .temporary-invoice-popup-table tr {
            display: table-row !important;
            width: auto !important;
        }

        .temporary-invoice-popup-table th,
        .temporary-invoice-popup-table td {
            display: table-cell !important;
            vertical-align: middle;
        }

        .temporary-invoice-popup-table th {
            background: #3348d4;
            border: 1px solid #1f2937 !important;
            color: #ffffff;
            padding: 9px 12px;
            white-space: nowrap;
        }

        .temporary-invoice-popup-table td,
        .temporary-invoice-popup-table tfoot th {
            background: #f8fafc;
            border: 1px solid #9ca3af !important;
            color: #1f2937;
            padding: 9px 12px;
        }

        .temporary-invoice-popup-table td {
            color: #374151;
            overflow-wrap: anywhere;
        }

        .temporary-invoice-popup-words {
            color: #6b7280;
            font-size: 16px;
            margin-top: 16px;
            text-transform: capitalize;
        }

        .temporary-invoice-popup-notes {
            color: #6b7280;
            font-size: 13px;
            margin-top: 8px;
        }

        @media (max-width: 760px) {
            .temporary-invoice-popup-meta-row {
                flex-direction: column;
            }

            .temporary-invoice-popup-meta-right {
                text-align: left;
                width: 100%;
            }
        }

        .temporary-invoice-index .container-fluid {
            max-width: 1680px;
        }

        .temporary-page-head {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .temporary-page-head span,
        .temporary-field-label {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .temporary-page-head h3 {
            color: #172033;
            font-size: 28px;
            font-weight: 900;
            margin: 2px 0 0;
        }

        .temporary-primary-btn,
        .temporary-icon-btn {
            align-items: center;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 900;
            justify-content: center;
            text-decoration: none !important;
        }

        .temporary-primary-btn {
            background: #0f766e;
            color: #ffffff !important;
            gap: 8px;
            min-height: 46px;
            padding: 0 16px;
        }

        .temporary-icon-btn {
            border: 1px solid #94a3b8;
            color: #334155 !important;
            height: 46px;
            width: 46px;
        }

        .temporary-filter-panel,
        .temporary-table-panel {
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
            margin-bottom: 14px;
        }

        .temporary-filter-panel .form-control {
            height: 46px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
        }

        .temporary-filter-reset {
            align-items: end;
            display: flex;
        }

        .temporary-table-panel table {
            min-width: 1120px;
            table-layout: fixed;
        }

        .temporary-table-panel th {
            background: #3348d4 !important;
        }

        .temporary-pagination {
            margin-top: 12px;
        }

        .temporary-row-actions {
            display: inline-grid;
            gap: 6px;
            grid-template-columns: repeat(3, 38px);
        }

        .temporary-row-action {
            align-items: center;
            border: 0;
            border-radius: 7px;
            color: #ffffff !important;
            display: inline-flex;
            height: 36px;
            justify-content: center;
            text-decoration: none !important;
            width: 38px;
        }

        .temporary-row-action.view {
            background: #2563eb;
        }

        .temporary-row-action.print {
            background: #0f766e;
        }

        .temporary-row-action.delete {
            background: #dc2626;
        }

        @media (max-width: 700px) {
            .temporary-page-head {
                align-items: stretch;
                flex-direction: column;
            }

            .temporary-primary-btn {
                width: 100%;
            }
        }
    </style>
@stop
