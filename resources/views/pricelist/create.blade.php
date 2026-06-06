@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content price-create-page">
    <div class="container-fluid">
        @yield('breadcrumb')

        @if ($errors->any())
            <div class="alert alert-danger">
                <b>Please check the rows.</b>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $oldPriceItems = old('items', [[
                'itemname' => '',
                'costprice' => '',
                'saleprice' => '',
                'wholesaleprice' => '',
                'note' => '',
            ]]);
        @endphp

        <form action="{{ route('pricelists.store') }}" method="post" id="priceListBulkForm">
            @csrf

            <div class="price-panel">
                <div class="price-panel-header">
                    <div>
                        <span>Add Price List</span>
                        <strong>Bulk Item Entry</strong>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle price-entry-table">
                        <colgroup>
                            <col style="width: 72px;">
                            <col>
                            <col style="width: 170px;">
                            <col style="width: 170px;">
                            <col style="width: 190px;">
                            <col style="width: 240px;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>
                                    <div class="price-number-head">
                                        <span>#</span>
                                        <button type="button" class="price-icon-btn" id="addPriceRowBtn" title="Add row">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </th>
                                <th>Item Name</th>
                                <th>Cost Price</th>
                                <th>Sale Price</th>
                                <th>Wholesale Price</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="priceRows"></tbody>
                    </table>
                </div>
            </div>

            <div class="price-savebar">
                <span id="priceRowCount">0 / 12 rows</span>
                <button type="submit" class="price-save-btn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Price List
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var maxRows = 12;
        var tbody = document.getElementById('priceRows');
        var addBtn = document.getElementById('addPriceRowBtn');
        var rowCount = document.getElementById('priceRowCount');
        var oldItems = @json($oldPriceItems);

        function renumberRows() {
            tbody.querySelectorAll('tr').forEach(function (row, index) {
                row.querySelector('.price-row-number').textContent = index + 1;
                row.querySelectorAll('[data-field]').forEach(function (input) {
                    input.name = 'items[' + index + '][' + input.getAttribute('data-field') + ']';
                });
            });

            var count = tbody.querySelectorAll('tr').length;
            rowCount.textContent = count + ' / ' + maxRows + ' rows';
            addBtn.disabled = count >= maxRows;
        }

        function addRow(values) {
            if (tbody.querySelectorAll('tr').length >= maxRows) return;

            var row = document.createElement('tr');
            row.innerHTML = [
                '<td><span class="price-row-number"></span><button type="button" class="price-remove-btn" title="Remove row"><i class="fa-solid fa-trash"></i></button></td>',
                '<td><input type="text" class="form-control" data-field="itemname" required autocomplete="off"></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="costprice" required></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="saleprice" required></td>',
                '<td><input type="number" step="0.01" min="0" class="form-control" data-field="wholesaleprice"></td>',
                '<td><input type="text" class="form-control" data-field="note" autocomplete="off"></td>'
            ].join('');

            tbody.appendChild(row);

            Object.keys(values || {}).forEach(function (field) {
                var input = row.querySelector('[data-field="' + field + '"]');
                if (input) input.value = values[field] || '';
            });

            renumberRows();
        }

        addBtn.addEventListener('click', function () {
            addRow({});
        });

        tbody.addEventListener('click', function (event) {
            var removeBtn = event.target.closest('.price-remove-btn');
            if (!removeBtn) return;
            if (tbody.querySelectorAll('tr').length === 1) return;
            removeBtn.closest('tr').remove();
            renumberRows();
        });

        oldItems.slice(0, maxRows).forEach(function (item) {
            addRow(item || {});
        });

        if (!tbody.querySelectorAll('tr').length) {
            addRow({});
        }
    })();
</script>

<style>
    .price-create-page {
        box-sizing: border-box;
        flex: 1 1 auto;
        width: 100%;
    }

    .price-create-page .container-fluid {
        max-width: 1680px;
        width: 100%;
    }

    .price-panel {
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .07);
        overflow: hidden;
        width: 100%;
    }

    .price-panel-header {
        align-items: center;
        background: #f8fafc;
        border-bottom: 1px solid #dbe3ef;
        display: flex;
        justify-content: space-between;
        padding: 10px 14px;
    }

    .price-panel-header span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .price-panel-header strong {
        color: #172033;
        display: block;
        font-size: 18px;
        font-weight: 900;
    }

    .price-icon-btn,
    .price-save-btn {
        align-items: center;
        background: #0f766e;
        border: 0;
        border-radius: 8px;
        color: #ffffff;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        justify-content: center;
    }

    .price-icon-btn {
        height: 42px;
        width: 44px;
    }

    .price-icon-btn:disabled {
        background: #cbd5e1;
        color: #64748b;
    }

    .price-create-page table.price-entry-table {
        margin: 0;
        min-width: 1100px;
        table-layout: fixed !important;
        width: 100%;
    }

    .price-create-page table.price-entry-table th,
    .price-create-page table.price-entry-table td {
        display: table-cell !important;
        padding: 4px 6px;
        vertical-align: middle;
        white-space: nowrap;
    }

    .price-number-head {
        align-items: center;
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .price-number-head .price-icon-btn {
        height: 42px;
        width: 44px;
        font-size: 18px;
    }

    .price-create-page table.price-entry-table thead {
        display: table-header-group !important;
    }

    .price-create-page table.price-entry-table tbody {
        display: table-row-group !important;
    }

    .price-create-page table.price-entry-table tr {
        display: table-row !important;
        width: auto !important;
    }

    .price-entry-table .form-control {
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        min-height: 36px;
        padding: 5px 8px;
    }

    .price-row-number {
        display: inline-block;
        font-weight: 900;
        min-width: 18px;
    }

    .price-remove-btn {
        align-items: center;
        background: #ffffff;
        border: 1px solid #ef4444;
        border-radius: 8px;
        color: #dc2626;
        display: inline-flex;
        height: 32px;
        justify-content: center;
        margin-left: 4px;
        width: 32px;
    }

    .price-savebar {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        box-shadow: 0 -8px 24px rgba(15, 23, 42, .08);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 12px;
        padding: 10px;
        position: sticky;
        bottom: 0;
        z-index: 20;
    }

    .price-savebar span {
        color: #475569;
        font-weight: 900;
    }

    .price-save-btn {
        min-height: 42px;
        padding: 0 16px;
    }

    @media (max-width: 900px) {
        .price-entry-table {
            min-width: 980px;
        }

        .price-savebar {
            align-items: stretch;
            flex-direction: column;
        }
    }
</style>
@stop
