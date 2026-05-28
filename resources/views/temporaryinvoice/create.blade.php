@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    <div class="main-content">
        @yield('breadcrumb')

        <div class="container-fluid">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <b>Please check the form.</b>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-end align-items-center mb-2" style="margin-top: -58px;">
                <a href="{{ route('temporaryinvoice.index') }}" class="btn btn-outline-primary">
                    <i class="fa-solid fa-list"></i> View Temporary Invoices
                </a>
            </div>

            <form action="{{ route('temporaryinvoice.store') }}" method="post" id="temporaryInvoiceForm">
                @csrf

                <div class="card mb-4">
                    <div class="card-header fw-bold">Customer Details</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control"
                                    value="{{ old('customer_name') }}" required autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="customer_address" class="form-control"
                                    value="{{ old('customer_address') }}" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Contact No</label>
                                <input type="text" name="contact_number" class="form-control"
                                    value="{{ old('contact_number') }}" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="invoice_date" class="form-control"
                                    value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required>
                                <small class="text-muted">
                                    Nepali Date: {{ \App\Support\NepaliDate::adToBsString(old('invoice_date', now()->toDateString()), 'en') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <span></span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle temporary-invoice-table">
                                <colgroup>
                                    <col style="width: 100px;">
                                    <col>
                                    <col style="width: 150px;">
                                    <col style="width: 130px;">
                                    <col style="width: 150px;">
                                    <col style="width: 170px;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>
                                            <button class="btn btn-success btn-sm" type="button" id="addTempRowBtn">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="temporaryInvoiceRows"></tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Subtotal</span>
                                    <input type="text" class="form-control" id="tempSubtotal" readonly>
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Discount</span>
                                    <input type="number" step="0.01" min="0" name="discount" class="form-control"
                                        id="tempDiscount" value="{{ old('discount', 0) }}">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Total</span>
                                    <input type="text" class="form-control fw-bold" id="tempTotal" readonly>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Save Temporary Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var tbody = document.getElementById('temporaryInvoiceRows');
            var addBtn = document.getElementById('addTempRowBtn');
            var discountInput = document.getElementById('tempDiscount');

            function money(value) {
                return Number(value || 0).toFixed(2);
            }

            function renumberRows() {
                tbody.querySelectorAll('tr').forEach(function (row, index) {
                    row.querySelector('.row-number').textContent = index + 1;
                    row.querySelectorAll('input').forEach(function (input) {
                        var field = input.getAttribute('data-field');
                        if (field) {
                            input.name = 'items[' + index + '][' + field + ']';
                        }
                    });
                });
            }

            function calculateTotals() {
                var subtotal = 0;
                tbody.querySelectorAll('tr').forEach(function (row) {
                    var qty = parseFloat(row.querySelector('[data-field="quantity"]').value) || 0;
                    var price = parseFloat(row.querySelector('[data-field="price"]').value) || 0;
                    var amount = qty * price;
                    row.querySelector('.amount-display').value = money(amount);
                    subtotal += amount;
                });

                var discount = parseFloat(discountInput.value) || 0;
                document.getElementById('tempSubtotal').value = money(subtotal);
                document.getElementById('tempTotal').value = money(Math.max(0, subtotal - discount));
            }

            function addRow(values) {
                var row = document.createElement('tr');
                row.innerHTML = [
                    '<td><span class="row-number"></span><button type="button" class="btn btn-outline-danger btn-sm remove-temp-row ms-2"><i class="fa-solid fa-trash"></i></button></td>',
                    '<td><input type="text" class="form-control" data-field="item_name" required autocomplete="off"></td>',
                    '<td><input type="number" step="0.01" min="0" class="form-control temp-calc" data-field="quantity" required></td>',
                    '<td><input type="text" class="form-control" data-field="unit" autocomplete="off"></td>',
                    '<td><input type="number" step="0.01" min="0" class="form-control temp-calc" data-field="price" required></td>',
                    '<td><input type="text" class="form-control amount-display" readonly></td>'
                ].join('');

                tbody.appendChild(row);

                if (values) {
                    Object.keys(values).forEach(function (key) {
                        var input = row.querySelector('[data-field="' + key + '"]');
                        if (input) input.value = values[key];
                    });
                }

                renumberRows();
                calculateTotals();
            }

            addBtn.addEventListener('click', function () {
                addRow();
            });

            tbody.addEventListener('input', function (event) {
                if (event.target.classList.contains('temp-calc')) {
                    calculateTotals();
                }
            });

            tbody.addEventListener('click', function (event) {
                var removeBtn = event.target.closest('.remove-temp-row');
                if (!removeBtn) return;
                if (tbody.querySelectorAll('tr').length === 1) return;
                removeBtn.closest('tr').remove();
                renumberRows();
                calculateTotals();
            });

            discountInput.addEventListener('input', calculateTotals);
            addRow();
        })();
    </script>

    <style>
        .temporary-invoice-table {
            table-layout: fixed;
            width: 100%;
        }

        .temporary-invoice-table thead {
            display: table-header-group !important;
        }

        .temporary-invoice-table tbody {
            display: table-row-group !important;
            height: auto !important;
            overflow: visible !important;
        }

        .temporary-invoice-table tr {
            display: table-row !important;
            width: auto !important;
        }

        .temporary-invoice-table th,
        .temporary-invoice-table td {
            display: table-cell !important;
            vertical-align: middle;
            white-space: nowrap;
        }

        .temporary-invoice-table .form-control {
            width: 100%;
            min-width: 0;
        }

        .temporary-invoice-table .row-number {
            text-align: center;
            font-weight: 700;
        }
    </style>
@stop
