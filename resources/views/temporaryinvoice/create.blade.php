@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    <div class="main-content temporary-invoice-create">
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

            <div class="temporary-page-title">Temporary Invoice</div>

            <form action="{{ route('temporaryinvoice.store') }}" method="post" id="temporaryInvoiceForm">
                @csrf

                <div class="temporary-panel mb-4">
                    <div class="temporary-panel-header">
                        <div>
                            <span>Step 1</span>
                            <strong>Customer Details</strong>
                        </div>
                    </div>
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

                <div class="temporary-panel mb-4">
                    <div class="temporary-panel-header">
                        <div>
                            <span>Step 2</span>
                            <strong>Items And Total</strong>
                        </div>
                        <div class="temporary-panel-actions">
                            <a href="{{ route('temporaryinvoice.index') }}" class="temporary-secondary-btn">
                                <i class="fa-solid fa-list"></i> View Temporary Invoices
                            </a>
                            <button class="temporary-tool-btn primary" type="button" data-bs-toggle="modal"
                                data-bs-target="#fixedSetModal" title="Fixed item set">
                                <i class="fa-solid fa-layer-group"></i>
                            </button>
                            <button class="temporary-tool-btn success" type="button" id="addTempRowBtn" title="Add row">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
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
                                    <col style="width: 190px;">
                                    <col style="width: 170px;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>
                                            #
                                        </th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Rate</th>
                                        <th>Discount</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="temporaryInvoiceRows"></tbody>
                            </table>
                        </div>

                        <div class="row g-3 justify-content-between">
                            <div class="col-lg-6">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control temporary-notes" rows="4">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-lg-3 ms-auto temporary-create-totals">
                                <div class="input-group mb-2 temporary-discount-summary">
                                    <span class="input-group-text">Total Discount</span>
                                    <input type="text" class="form-control" id="tempDiscountAmountTotal" readonly>
                                </div>
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

                <div class="modal fade temporary-fixed-modal" id="fixedSetModal" tabindex="-1"
                    aria-labelledby="fixedSetModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="fixedSetModalLabel">Fixed Item Set</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Search Fixed Set</label>
                                        <div class="temporary-fixed-search">
                                            <input type="text" class="form-control" id="fixedSetSearch"
                                                placeholder="Search code like bcm300c" autocomplete="off">
                                            <div class="temporary-fixed-results" id="fixedSetResults" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Code</label>
                                        <input type="text" class="form-control" id="fixedSetCode" placeholder="bcm300c"
                                            autocomplete="off">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Name</label>
                                        <input type="text" class="form-control" id="fixedSetName"
                                            placeholder="0.5hp bharu bcm300c" autocomplete="off">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="fw-bold" id="fixedSetMessage"></small>
                                </div>
                            </div>
                            <div class="modal-footer temporary-fixed-actions">
                                <button type="button" class="temporary-tool-btn success" id="saveFixedSetBtn"
                                    title="Save fixed set">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                </button>
                                <button type="button" class="temporary-tool-btn primary" id="updateFixedSetBtn" disabled
                                    title="Update fixed set">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="temporary-tool-btn danger" id="deleteFixedSetBtn" disabled
                                    title="Delete fixed set">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="temporary-savebar">
                    <a href="{{ route('temporaryinvoice.index') }}" class="temporary-secondary-btn">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="temporary-primary-btn">
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
            var fixedSetModal = document.getElementById('fixedSetModal');
            var saveFixedSetBtn = document.getElementById('saveFixedSetBtn');
            var updateFixedSetBtn = document.getElementById('updateFixedSetBtn');
            var deleteFixedSetBtn = document.getElementById('deleteFixedSetBtn');
            var fixedSetSearchTimer = null;
            var priceListSearchTimer = null;
            var selectedFixedSetId = null;
            var fixedSetUrl = "{{ route('temporaryinvoice.fixed-item-sets.index') }}";
            var fixedSetStoreUrl = "{{ route('temporaryinvoice.fixed-item-sets.store') }}";
            var priceListUrl = "{{ url('temporaryinvoice-price-list-suggestions') }}";
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
                var discountAmountTotal = 0;
                tbody.querySelectorAll('tr').forEach(function (row) {
                    var qty = parseFloat(row.querySelector('[data-field="quantity"]').value) || 0;
                    var price = parseFloat(row.querySelector('[data-field="price"]').value) || 0;
                    var discountPercent = parseFloat(row.querySelector('[data-field="discount_percent"]').value) || 0;
                    var gross = qty * price;
                    var discountAmount = Math.max(0, gross * discountPercent / 100);
                    var amount = Math.max(0, gross - discountAmount);
                    row.querySelector('.discount-amount-display').value = money(discountAmount);
                    row.querySelector('.amount-display').value = money(amount);
                    discountAmountTotal += discountAmount;
                    subtotal += amount;
                });

                var discount = parseFloat(discountInput.value) || 0;
                var total = Math.max(0, subtotal - discount);
                document.getElementById('tempSubtotal').value = money(subtotal);
                document.getElementById('tempDiscountAmountTotal').value = money(discountAmountTotal);
                document.getElementById('tempTotal').value = money(total);
                document.getElementById('tempAmountWords').textContent = amountWords(total);
            }

            function closePriceListResults() {
                tbody.querySelectorAll('.temporary-price-results').forEach(function (results) {
                    results.style.display = 'none';
                });
            }

            function addRow(values) {
                var row = document.createElement('tr');
                row.innerHTML = [
                    '<td><span class="row-number"></span><button type="button" class="btn btn-outline-danger btn-sm remove-temp-row ms-2"><i class="fa-solid fa-trash"></i></button></td>',
                    '<td><div class="temporary-price-search"><input type="text" class="form-control" data-field="item_name" required autocomplete="off"><div class="temporary-price-results" style="display: none;"></div></div></td>',
                    '<td><input type="number" step="0.01" min="0" class="form-control temp-calc" data-field="quantity" required></td>',
                    '<td><input type="text" class="form-control" data-field="unit" autocomplete="off"></td>',
                    '<td><input type="number" step="0.01" min="0" class="form-control temp-calc" data-field="price" required></td>',
                    '<td><div class="temporary-discount-cell"><input type="number" step="0.01" min="0" max="100" class="form-control temp-calc" data-field="discount_percent" placeholder="%"><input type="text" class="form-control discount-amount-display" readonly title="Discount amount"></div></td>',
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

            function renderPriceListResults(input, items) {
                var row = input.closest('tr');
                var results = row.querySelector('.temporary-price-results');
                results.innerHTML = '';

                if (!items.length) {
                    results.innerHTML = '<div class="temporary-price-empty">No price list item found.</div>';
                    results.style.display = 'block';
                    return;
                }

                items.forEach(function (item) {
                    var btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'temporary-price-result';
                    btn.innerHTML = '<b>' + escapeHtml(item.item_name) + '</b><span>Sale: ' +
                        money(item.sale_price) + ' | Wholesale: ' + money(item.wholesale_price) + '</span>';
                    btn.addEventListener('mousedown', function (event) {
                        event.preventDefault();
                        row.querySelector('[data-field="item_name"]').value = item.item_name;
                        row.querySelector('[data-field="price"]').value = item.sale_price;
                        if (!row.querySelector('[data-field="quantity"]').value) {
                            row.querySelector('[data-field="quantity"]').value = 1;
                        }
                        closePriceListResults();
                        calculateTotals();
                    });
                    results.appendChild(btn);
                });

                results.style.display = 'block';
            }

            function searchPriceList(input) {
                var query = input.value.trim();
                if (query.length < 1) {
                    closePriceListResults();
                    return;
                }

                fetch(priceListUrl + '?q=' + encodeURIComponent(query), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (items) {
                        renderPriceListResults(input, items);
                    })
                    .catch(closePriceListResults);
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
                        price: item.price,
                        discount_percent: 0
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
                        price: row.querySelector('[data-field="price"]').value || 0,
                        discount_percent: row.querySelector('[data-field="discount_percent"]').value || 0
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
                        if (window.bootstrap && fixedSetModal) {
                            bootstrap.Modal.getOrCreateInstance(fixedSetModal).hide();
                        }
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

                if (event.target.matches('[data-field="item_name"]')) {
                    clearTimeout(priceListSearchTimer);
                    var input = event.target;
                    priceListSearchTimer = setTimeout(function () {
                        searchPriceList(input);
                    }, 220);
                }
            });

            tbody.addEventListener('focusin', function (event) {
                if (event.target.matches('[data-field="item_name"]') && event.target.value.trim()) {
                    searchPriceList(event.target);
                }
            });

            tbody.addEventListener('focusout', function (event) {
                if (event.target.matches('[data-field="item_name"]')) {
                    setTimeout(closePriceListResults, 180);
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
        .temporary-invoice-create {
            box-sizing: border-box;
            flex: 1 1 auto;
            width: 100%;
        }

        .temporary-invoice-create .container-fluid {
            max-width: 1680px;
            width: 100%;
        }

        .temporary-invoice-create .mainbreadcrumb {
            display: none;
        }

        .temporary-invoice-create .temporary-panel.mb-4 {
            margin-bottom: 10px !important;
        }

        .temporary-page-title {
            color: #172033;
            font-size: 22px;
            font-weight: 900;
            line-height: 1;
            margin: 0 0 8px;
            text-transform: uppercase;
        }

        .temporary-create-head {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: flex-end;
            margin-bottom: 14px;
        }

        .temporary-create-head span,
        .temporary-panel-header span {
            color: #64748b;
            display: block;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .temporary-create-head h3 {
            color: #172033;
            font-size: 28px;
            font-weight: 900;
            margin: 2px 0 0;
        }

        .temporary-panel {
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
            overflow: visible;
            position: relative;
        }

        .temporary-panel-header {
            align-items: center;
            background: #f8fafc;
            border-bottom: 1px solid #dbe3ef;
            display: flex;
            gap: 14px;
            justify-content: space-between;
            padding: 8px 14px;
        }

        .temporary-panel-header strong {
            color: #172033;
            display: block;
            font-size: 17px;
            font-weight: 900;
            margin-top: 0;
        }

        .temporary-panel-actions {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .temporary-invoice-create .form-label {
            color: #334155;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .temporary-invoice-create .form-control {
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            min-height: 38px;
        }

        .temporary-invoice-create .card-body {
            padding: 10px 14px;
        }

        .temporary-create-totals {
            max-width: 380px;
            min-width: 320px;
        }

        .temporary-create-totals .input-group-text {
            justify-content: flex-end;
            min-width: 118px;
            padding-left: 10px;
            padding-right: 10px;
            white-space: nowrap;
        }

        .temporary-invoice-create .row.g-3 {
            --bs-gutter-y: .35rem;
        }

        .temporary-fixed-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }

        .temporary-tool-btn {
            align-items: center;
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            display: inline-flex;
            height: 42px;
            justify-content: center;
            width: 44px;
        }

        .temporary-tool-btn.success {
            background: #0f766e;
        }

        .temporary-tool-btn.primary {
            background: #2563eb;
        }

        .temporary-tool-btn.danger {
            background: #dc2626;
        }

        .temporary-tool-btn:disabled {
            background: #cbd5e1;
            color: #64748b;
        }

        .temporary-primary-btn,
        .temporary-secondary-btn {
            align-items: center;
            border-radius: 8px;
            display: inline-flex;
            font-weight: 900;
            gap: 8px;
            justify-content: center;
            min-height: 46px;
            padding: 0 16px;
            text-decoration: none !important;
        }

        .temporary-primary-btn {
            background: #0f766e;
            border: 0;
            color: #ffffff !important;
        }

        .temporary-secondary-btn {
            background: #ffffff;
            border: 1px solid #94a3b8;
            color: #334155 !important;
        }

        .temporary-invoice-table {
            table-layout: fixed;
            width: 100%;
        }

        .temporary-invoice-create .table-responsive {
            overflow: visible;
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
            padding: 2px 4px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .temporary-invoice-table .form-control {
            min-height: 34px;
            padding: 4px 8px;
            width: 100%;
            min-width: 0;
        }

        .temporary-discount-cell {
            display: grid;
            gap: 4px;
            grid-template-columns: minmax(58px, .85fr) minmax(78px, 1fr);
        }

        .temporary-discount-cell .discount-amount-display {
            background: #f8fafc;
        }

        .temporary-invoice-table .row-number {
            display: inline-flex;
            min-width: 22px;
            text-align: center;
            font-weight: 900;
        }

        .temporary-invoice-table .remove-temp-row {
            align-items: center;
            display: inline-flex;
            height: 34px;
            justify-content: center;
            margin-left: 4px !important;
            padding: 0;
            width: 34px;
        }

        .temporary-price-search {
            position: relative;
        }

        .temporary-price-results {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
            left: 0;
            max-height: 260px;
            overflow-y: auto;
            position: absolute;
            right: 0;
            top: calc(100% + 4px);
            z-index: 10001;
        }

        .temporary-price-result {
            background: #ffffff;
            border: 0;
            border-bottom: 1px solid #e5e7eb;
            display: grid;
            gap: 2px;
            padding: 8px 10px;
            text-align: left;
            width: 100%;
        }

        .temporary-price-result:hover {
            background: #ecfeff;
        }

        .temporary-price-result b {
            color: #111827;
            font-size: 15px;
            line-height: 1.2;
        }

        .temporary-price-result span {
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
        }

        .temporary-price-empty {
            color: #dc3545;
            font-weight: 700;
            padding: 8px 10px;
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

        .temporary-fixed-modal .modal-content,
        .temporary-fixed-modal .modal-body {
            overflow: visible;
        }

        .temporary-fixed-modal .modal-title {
            color: #172033;
            font-weight: 900;
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
            z-index: 10000;
        }

        .temporary-fixed-result {
            background: #ffffff;
            border: 0;
            border-bottom: 1px solid #e5e7eb;
            display: grid;
            gap: 2px;
            min-height: 70px;
            padding: 10px 14px;
            text-align: left;
            width: 100%;
        }

        .temporary-fixed-result:hover {
            background: #ecfeff;
        }

        .temporary-fixed-result small,
        .temporary-fixed-result span {
            color: #64748b;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
        }

        .temporary-fixed-result b {
            color: #111827;
            font-size: 18px;
            line-height: 1.15;
        }

        .temporary-fixed-empty {
            color: #dc3545;
            padding: 8px 10px;
        }

        .temporary-notes {
            min-height: 132px;
            resize: vertical;
        }

        .temporary-savebar {
            align-items: center;
            background: #ffffff;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            bottom: 0;
            box-shadow: 0 -8px 24px rgba(15, 23, 42, .08);
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 14px;
            padding: 12px;
            position: sticky;
            z-index: 20;
        }

        @media (max-width: 760px) {
            .temporary-create-head,
            .temporary-panel-header,
            .temporary-savebar {
                align-items: stretch;
                flex-direction: column;
            }

            .temporary-fixed-actions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .temporary-tool-btn,
            .temporary-primary-btn,
            .temporary-secondary-btn {
                width: 100%;
            }

            .temporary-invoice-table {
                min-width: 920px;
            }

            .temporary-invoice-create .table-responsive {
                overflow-x: auto;
            }

            .temporary-create-totals {
                min-width: 0;
                width: 100%;
            }

        }
    </style>
@stop
