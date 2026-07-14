<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VAT Confirmation Letter</title>
    <style>
        *{box-sizing:border-box}body{background:linear-gradient(135deg,#dbe8f4,#f5f7fa);font-family:Arial,sans-serif;margin:0;padding-bottom:35px}.letter-actions{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;padding:16px}.letter-actions a,.letter-actions button{border:0;border-radius:7px;box-shadow:0 3px 9px rgba(15,23,42,.15);color:#fff;cursor:pointer;font-size:14px;font-weight:700;padding:11px 19px;text-decoration:none;transition:transform .15s}.letter-actions a:hover,.letter-actions button:hover{transform:translateY(-1px)}.print-button{background:#334155}.pdf-button{background:#dc2626}.back-button{background:#2563eb}.edit-button{background:#b45309}.confirmation-message,.confirmation-errors{border-radius:7px;margin:0 auto 14px;max-width:950px;padding:12px 16px}.confirmation-message{background:#dcfce7;color:#166534}.confirmation-errors{background:#fee2e2;color:#991b1b}.confirmation-errors ul{margin:6px 0 0;padding-left:20px}.confirmation-editor{background:#fff;border-top:5px solid #b45309;border-radius:10px;box-shadow:0 8px 24px rgba(15,23,42,.15);margin:0 auto 20px;max-width:950px;padding:20px}.confirmation-editor[hidden]{display:none}.editor-heading{align-items:center;display:flex;justify-content:space-between;margin-bottom:4px}.editor-heading h2{color:#7c2d12;font-size:21px;margin:0}.editor-heading button{background:transparent;border:0;color:#64748b;cursor:pointer;font-size:24px}.editor-help{color:#64748b;font-size:13px;margin:0 0 16px}.period-badge{background:#fff7ed;border:1px solid #fed7aa;border-radius:6px;color:#9a3412;font-size:13px;margin-bottom:16px;padding:9px 12px}.editor-table{border-collapse:collapse;width:100%}.editor-table th{background:#0b477f;color:#fff;font-size:12px;padding:9px}.editor-table td{border:1px solid #d5dee8;padding:8px}.editor-table input,.balance-row input,.balance-row select{border:1px solid #cbd5e1;border-radius:5px;font-size:14px;padding:9px;width:100%}.editor-table .calculated{background:#f1f5f9;color:#334155;font-weight:700;text-align:right}.editor-table .locked-row td{background:#eff6ff;font-weight:700}.balance-grid{display:grid;gap:14px;grid-template-columns:1fr 1fr;margin-top:18px}.balance-row{background:#f8fafc;border:1px solid #d5dee8;border-radius:7px;padding:12px}.balance-row label{display:block;font-size:13px;font-weight:700;margin-bottom:8px}.balance-fields{display:grid;gap:8px;grid-template-columns:2fr 1fr}.editor-save{text-align:right;margin-top:18px}.editor-save button{background:#15803d;border:0;border-radius:6px;color:#fff;cursor:pointer;font-size:14px;font-weight:700;padding:11px 21px}@media(max-width:700px){.confirmation-editor{border-radius:0}.editor-table{display:block;overflow-x:auto}.balance-grid{grid-template-columns:1fr}}@media print{body{background:#fff;padding:0}.letter-actions,.confirmation-message,.confirmation-errors,.confirmation-editor{display:none!important}}
    </style>
</head>
<body>
    @php
        $openingSide = $confirmationDetail?->opening_balance_side
            ?: (abs($openingBalance) < 0.01 ? 'nil' : ($openingBalance > 0 ? 'dr' : 'cr'));
        $closingSide = $confirmationDetail?->closing_balance_side
            ?: (abs($closingBalance) < 0.01 ? 'nil' : ($closingBalance > 0 ? 'dr' : 'cr'));
        $openingAmount = $confirmationDetail?->opening_balance_amount ?? abs($openingBalance);
        $closingAmount = $confirmationDetail?->closing_balance_amount ?? abs($closingBalance);
    @endphp

    <div class="letter-actions">
        <button type="button" class="edit-button" onclick="toggleConfirmationEditor(true)">Edit Confirmation Details</button>
        <button type="button" class="print-button" onclick="window.print()">Print Confirmation Letter</button>
        <a class="pdf-button" target="_blank"
            href="{{ route('vat-party-ledgers.confirmation.pdf', ['vatBill' => $anchorVatBill->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">
            PDF
        </a>
        <a class="back-button" href="{{ route('vat-party-ledgers.show', ['vatBill' => $anchorVatBill->id, 'firm_type' => $firmType, 'from_date' => $fromDate, 'to_date' => $toDate]) }}">Back to Ledger</a>
    </div>

    @if(session('confirmation_success'))
        <div class="confirmation-message">{{ session('confirmation_success') }}</div>
    @endif

    @if($errors->any())
        <div class="confirmation-errors">
            <strong>Please correct these details:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <section id="confirmationEditor" class="confirmation-editor" @if(!$errors->any()) hidden @endif>
        <div class="editor-heading">
            <h2>Edit Confirmation Details</h2>
            <button type="button" aria-label="Close" onclick="toggleConfirmationEditor(false)">&times;</button>
        </div>
        <p class="editor-help">Sales taxable amount and VAT are calculated automatically from VAT bills. Sales exempted amount, purchase and returns can be edited; totals calculate automatically.</p>
        <div class="period-badge">
            <strong>{{ $customer->name }}</strong> &nbsp;|&nbsp; {{ $firmType }} &nbsp;|&nbsp;
            Period (A.D.): {{ $periodFromAd }} to {{ $periodToAd }}
        </div>

        <form method="POST" action="{{ route('vat-party-ledgers.confirmation-details', $anchorVatBill) }}">
            @csrf
            <input type="hidden" name="firm_type" value="{{ $firmType }}">
            <input type="hidden" name="from_date" value="{{ $periodFromAd }}">
            <input type="hidden" name="to_date" value="{{ $periodToAd }}">

            <table class="editor-table">
                <thead><tr><th>Particulars</th><th>Exempted</th><th>Taxable</th><th>VAT 13%</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach([
                        'purchase' => ['Purchase', $purchaseRow],
                        'purchase_return' => ['Purchase Return', $purchaseReturnRow],
                        'sales_return' => ['Sales Return', $salesReturnRow],
                    ] as $key => [$label, $row])
                        <tr class="editable-transaction" data-key="{{ $key }}">
                            <td><strong>{{ $label }}</strong></td>
                            <td><input class="amount exempted" type="number" min="0" step="0.01" name="{{ $key }}_exempted" value="{{ old($key . '_exempted', $row['exempted']) }}"></td>
                            <td><input class="amount taxable" type="number" min="0" step="0.01" name="{{ $key }}_taxable" value="{{ old($key . '_taxable', $row['taxable']) }}"></td>
                            <td class="calculated vat">0.00</td>
                            <td class="calculated total">0.00</td>
                        </tr>
                    @endforeach
                    <tr class="locked-row">
                        <td>Sales (taxable automatic)</td>
                        <td><input class="amount" id="salesExempted" type="number" min="0" step="0.01" name="sales_exempted" value="{{ old('sales_exempted', $salesRow['exempted']) }}"></td>
                        <td>{{ number_format($salesRow['taxable'], 2) }}</td>
                        <td>{{ number_format($salesRow['vat'], 2) }}</td>
                        <td id="salesTotal" data-automatic-total="{{ $grandTotal }}">{{ number_format($salesRow['total'], 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="balance-grid">
                <div class="balance-row">
                    <label>Opening Balance</label>
                    <div class="balance-fields">
                        <input type="number" min="0" step="0.01" name="opening_balance_amount" value="{{ old('opening_balance_amount', $openingAmount) }}">
                        <select name="opening_balance_side">
                            @foreach(['nil' => 'Nil', 'dr' => 'Dr', 'cr' => 'Cr'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('opening_balance_side', $openingSide) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="balance-row">
                    <label>Closing Balance</label>
                    <div class="balance-fields">
                        <input type="number" min="0" step="0.01" name="closing_balance_amount" value="{{ old('closing_balance_amount', $closingAmount) }}">
                        <select name="closing_balance_side">
                            @foreach(['nil' => 'Nil', 'dr' => 'Dr', 'cr' => 'Cr'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('closing_balance_side', $closingSide) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="editor-save"><button type="submit">Save &amp; Update Letter</button></div>
        </form>
    </section>

    @include('vatbills._confirmation_letter')

    <script>
        function toggleConfirmationEditor(show) {
            const editor = document.getElementById('confirmationEditor');
            editor.hidden = !show;
            if (show) editor.scrollIntoView({behavior: 'smooth', block: 'start'});
        }

        function updateTransactionRow(row) {
            const exempted = parseFloat(row.querySelector('.exempted').value) || 0;
            const taxable = parseFloat(row.querySelector('.taxable').value) || 0;
            const vat = Math.round(taxable * 13) / 100;
            row.querySelector('.vat').textContent = vat.toFixed(2);
            row.querySelector('.total').textContent = (exempted + taxable + vat).toFixed(2);
        }

        document.querySelectorAll('.editable-transaction').forEach(function (row) {
            row.querySelectorAll('input').forEach(function (input) {
                input.addEventListener('input', function () { updateTransactionRow(row); });
            });
            updateTransactionRow(row);
        });

        const salesExempted = document.getElementById('salesExempted');
        const salesTotal = document.getElementById('salesTotal');
        function updateSalesTotal() {
            const exempted = parseFloat(salesExempted.value) || 0;
            const automaticTotal = parseFloat(salesTotal.dataset.automaticTotal) || 0;
            salesTotal.textContent = (exempted + automaticTotal).toFixed(2);
        }
        salesExempted.addEventListener('input', updateSalesTotal);
        updateSalesTotal();
    </script>
</body>
</html>
