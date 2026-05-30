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
                    <div class="card-header fw-bold">Fixed Item Set</div>
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Search Fixed Set</label>
                                <div class="temporary-fixed-search">
                                    <input type="text" class="form-control" id="fixedSetSearch"
                                        placeholder="Search code like bcm300c" autocomplete="off">
                                    <div class="temporary-fixed-results" id="fixedSetResults" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Code</label>
                                <input type="text" class="form-control" id="fixedSetCode" placeholder="bcm300c"
                                    autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="fixedSetName" placeholder="0.5hp bharu bcm300c"
                                    autocomplete="off">
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="button" class="btn btn-success" id="saveFixedSetBtn">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                </button>
                                <button type="button" class="btn btn-primary" id="updateFixedSetBtn" disabled>
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" id="deleteFixedSetBtn" disabled>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">
                            Choose a saved code to fill rows. Edit rows below, then update or save as a new fixed set.
                        </small>
                        <div class="mt-2">
                            <small class="fw-bold" id="fixedSetMessage"></small>
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
                                <div class="temporary-amount-words mt-2">
                                    <b>Amount in words:</b>
                                    <span id="tempAmountWords">Zero only /-</span>
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
            var fixedSetSearch = document.getElementById('fixedSetSearch');
            var fixedSetResults = document.getElementById('fixedSetResults');
            var fixedSetCode = document.getElementById('fixedSetCode');
            var fixedSetName = document.getElementById('fixedSetName');
            var fixedSetMessage = document.getElementById('fixedSetMessage');
            var saveFixedSetBtn = document.getElementById('saveFixedSetBtn');
            var updateFixedSetBtn = document.getElementById('updateFixedSetBtn');
            var deleteFixedSetBtn = document.getElementById('deleteFixedSetBtn');
            var fixedSetSearchTimer = null;
            var selectedFixedSetId = null;
            var fixedSetUrl = "{{ route('temporaryinvoice.fixed-item-sets.index') }}";
            var fixedSetStoreUrl = "{{ route('temporaryinvoice.fixed-item-sets.store') }}";
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function money(value) {
                return Number(value || 0).toFixed(2);
            }

            function amountWords(value) {
                var words = convertNumberToWords(Math.floor(value || 0));
                return words.toLowerCase().indexOf('only') === -1 ? words + ' only /-' : words;
            }

            function setFixedSetMessage(message, isError) {
                fixedSetMessage.textContent = message || '';
                fixedSetMessage.className = isError ? 'fw-bold text-danger' : 'fw-bold text-success';
            }

            function fixedSetEndpoint(id) {
                return fixedSetUrl + '/' + id;
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
                var total = Math.max(0, subtotal - discount);
                document.getElementById('tempSubtotal').value = money(subtotal);
                document.getElementById('tempTotal').value = money(total);
                document.getElementById('tempAmountWords').textContent = amountWords(total);
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

            function clearRows() {
                tbody.innerHTML = '';
            }

            function fillRows(items) {
                clearRows();
                items.forEach(function (item) {
                    addRow({
                        item_name: item.item_name,
                        quantity: item.quantity,
                        unit: item.unit || '',
                        price: item.price
                    });
                });

                if (items.length === 0) {
                    addRow();
                }

                renumberRows();
                calculateTotals();
            }

            function collectRows() {
                return Array.from(tbody.querySelectorAll('tr')).map(function (row) {
                    return {
                        item_name: row.querySelector('[data-field="item_name"]').value.trim(),
                        quantity: row.querySelector('[data-field="quantity"]').value || 0,
                        unit: row.querySelector('[data-field="unit"]').value.trim(),
                        price: row.querySelector('[data-field="price"]').value || 0
                    };
                }).filter(function (item) {
                    return item.item_name !== '';
                });
            }

            function fixedSetPayload() {
                return {
                    code: fixedSetCode.value.trim().toLowerCase(),
                    name: fixedSetName.value.trim(),
                    items: collectRows()
                };
            }

            function renderFixedSetResults(sets) {
                fixedSetResults.innerHTML = '';

                if (!sets.length) {
                    fixedSetResults.innerHTML = '<div class="temporary-fixed-empty">No fixed set found.</div>';
                    fixedSetResults.style.display = 'block';
                    return;
                }

                sets.forEach(function (set) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'temporary-fixed-result';
                    btn.innerHTML = '<b>' + escapeHtml(set.code) + '</b><span>' +
                        escapeHtml(set.name) + '</span><small>' + set.items.length + ' items</small>';
                    btn.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        selectedFixedSetId = set.id;
                        fixedSetSearch.value = set.code;
                        fixedSetCode.value = set.code;
                        fixedSetName.value = set.name;
                        updateFixedSetBtn.disabled = false;
                        deleteFixedSetBtn.disabled = false;
                        fixedSetResults.style.display = 'none';
                        fillRows(set.items);
                        setFixedSetMessage('Fixed set loaded: ' + set.code);
                    });
                    fixedSetResults.appendChild(btn);
                });

                fixedSetResults.style.display = 'block';
            }

            function searchFixedSets(query) {
                fetch(fixedSetUrl + '?q=' + encodeURIComponent(query || ''), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) { return response.json(); })
                    .then(renderFixedSetResults)
                    .catch(function () {
                        setFixedSetMessage('Could not search fixed sets.', true);
                    });
            }

            function saveFixedSet(url, method) {
                var payload = fixedSetPayload();
                if (!payload.code || !payload.name || payload.items.length === 0) {
                    setFixedSetMessage('Enter code, name, and at least one item row.', true);
                    return;
                }

                fetch(url, {
                    method: method,
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                })
                    .then(function (response) {
                        if (!response.ok) {
                            return response.json().then(function (data) {
                                throw new Error(data.message || 'Could not save fixed set.');
                            });
                        }
                        return response.json();
                    })
                    .then(function (set) {
                        selectedFixedSetId = set.id;
                        fixedSetCode.value = set.code;
                        fixedSetName.value = set.name;
                        fixedSetSearch.value = set.code;
                        updateFixedSetBtn.disabled = false;
                        deleteFixedSetBtn.disabled = false;
                        setFixedSetMessage('Fixed set saved: ' + set.code);
                    })
                    .catch(function (error) {
                        setFixedSetMessage(error.message, true);
                    });
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
            fixedSetSearch.addEventListener('input', function () {
                clearTimeout(fixedSetSearchTimer);
                fixedSetSearchTimer = setTimeout(function () {
                    searchFixedSets(fixedSetSearch.value.trim());
                }, 250);
            });
            fixedSetSearch.addEventListener('focus', function () {
                searchFixedSets(fixedSetSearch.value.trim());
            });
            fixedSetSearch.addEventListener('blur', function () {
                setTimeout(function () {
                    fixedSetResults.style.display = 'none';
                }, 180);
            });
            saveFixedSetBtn.addEventListener('click', function () {
                selectedFixedSetId = null;
                saveFixedSet(fixedSetStoreUrl, 'POST');
            });
            updateFixedSetBtn.addEventListener('click', function () {
                if (!selectedFixedSetId) return;
                saveFixedSet(fixedSetEndpoint(selectedFixedSetId), 'PUT');
            });
            deleteFixedSetBtn.addEventListener('click', function () {
                if (!selectedFixedSetId) return;
                if (!confirm('Delete this fixed item set?')) return;

                fetch(fixedSetEndpoint(selectedFixedSetId), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) throw new Error('Could not delete fixed set.');
                        selectedFixedSetId = null;
                        fixedSetCode.value = '';
                        fixedSetName.value = '';
                        fixedSetSearch.value = '';
                        updateFixedSetBtn.disabled = true;
                        deleteFixedSetBtn.disabled = true;
                        setFixedSetMessage('Fixed set deleted.');
                    })
                    .catch(function (error) {
                        setFixedSetMessage(error.message, true);
                    });
            });
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

        #addTempRowBtn i {
            font-size: 32px;
        }

        .temporary-amount-words {
            color: #111827;
            font-size: 16px;
            line-height: 1.35;
            text-transform: capitalize;
        }

        .temporary-fixed-search {
            position: relative;
        }

        .temporary-fixed-results {
            background: #ffffff;
            border: 1px solid #ced4da;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
            left: 0;
            max-height: 260px;
            overflow-y: auto;
            position: absolute;
            right: 0;
            top: calc(100% + 4px);
            z-index: 9999;
        }

        .temporary-fixed-result {
            background: #ffffff;
            border: 0;
            border-bottom: 1px solid #e5e7eb;
            display: grid;
            gap: 2px;
            padding: 8px 10px;
            text-align: left;
            width: 100%;
        }

        .temporary-fixed-result:hover {
            background: #ecfeff;
        }

        .temporary-fixed-result small,
        .temporary-fixed-result span {
            color: #64748b;
        }

        .temporary-fixed-empty {
            color: #dc3545;
            padding: 8px 10px;
        }
    </style>
@stop
