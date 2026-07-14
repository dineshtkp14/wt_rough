@php($showActions = $showActions ?? false)
<div class="party-ledger">
    <div class="ledger-header">
        <div class="firm-monogram">{{ $firmType === 'Malika & Nav Durga Traders' ? 'MN' : 'DD' }}</div>
        <div class="firm-heading">
            <h1>{{ $firmType === 'Malika & Nav Durga Traders' ? 'MALIKA AND NAWADURGA TRADERS' : strtoupper(str_replace('&', 'AND', $firmType)) }}</h1>
            <div class="heading-line"></div>
            <div class="firm-address"><i class="fa-solid fa-location-dot"></i> Tikapur, Kailali</div>
            <div class="firm-vat-number">VAT No: {{ $firmVatNo }}</div>
            <div class="firm-contact-number">Contact No: {{ $firmContactNumbers }}</div>
            <div class="ledger-title">PARTY LEDGER</div>
        </div>
    </div>

    <div class="party-meta">
        <div class="party-identity">
            <span><strong>Party Name:</strong> {{ $customer->name }}{{ $customer->address ? '-' . $customer->address : '' }}</span>
            <span><strong>Party VAT No:</strong> {{ $partyVatNo }}</span>
        </div>
        <div class="party-currency"><strong>Currency</strong><span>:</span>NPR</div>
    </div>

    <table class="ledger-table">
        <colgroup>
            @if ($showActions)
                <col style="width: 6%"><col style="width: 13%"><col style="width: 11%">
                <col style="width: 19%"><col style="width: 17%"><col style="width: 20%"><col style="width: 14%">
            @else
                <col style="width: 7%"><col style="width: 15%"><col style="width: 12%">
                <col style="width: 22%"><col style="width: 20%"><col style="width: 24%">
            @endif
        </colgroup>
        <thead>
            <tr>
                <th>S.N</th>
                <th>Date (B.S.)</th>
                <th>Bill No</th>
                <th>Taxable Amount (NPR)</th>
                <th>VAT (13%) (NPR)</th>
                <th>Total Amount (NPR)</th>
                @if ($showActions)<th>Action</th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ str_replace('-', '/', $row['date_bs']) }}</td>
                    <td>{{ $row['bill_no'] }}</td>
                    <td class="amount">{{ number_format($row['taxable_amount'], 2) }}</td>
                    <td class="amount">{{ number_format($row['vat_amount'], 2) }}</td>
                    <td class="amount">{{ number_format($row['total_amount'], 2) }}</td>
                    @if ($showActions)
                        <td class="ledger-actions">
                            <a href="{{ route('vat-bills.entry.edit', $row['vat_bill_id']) }}" class="ledger-edit-btn" title="Edit VAT bill">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('vat-bills.entry.destroy', $row['vat_bill_id']) }}"
                                onsubmit="return confirm('Delete this VAT bill? The sales invoice will not be deleted.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ledger-delete-btn" title="Delete VAT bill">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="{{ $showActions ? 7 : 6 }}" class="empty-row">No VAT bills found for the selected period.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">TOTAL</th>
                <th>{{ number_format($totalTaxable, 2) }}</th>
                <th>{{ number_format($totalVat, 2) }}</th>
                <th>{{ number_format($grandTotal, 2) }}</th>
                @if ($showActions)<th></th>@endif
            </tr>
        </tfoot>
    </table>
</div>

<style>
.party-ledger{background:#fff;border:1px solid #b8cee8;border-radius:8px;color:#102b50;overflow:hidden;padding:0 18px 18px}.ledger-header{align-items:center;border-bottom:2px solid #0b4b91;display:flex;gap:24px;justify-content:center;padding:14px 30px 9px}.firm-monogram{align-items:center;border:4px solid #0b4b91;border-radius:50%;color:#0b4b91;display:flex;font-size:29px;font-weight:900;height:88px;justify-content:center;letter-spacing:-4px;width:88px}.firm-heading{flex:1;max-width:780px;text-align:center}.firm-heading h1{color:#0b376d;font-family:Georgia,serif;font-size:34px;font-weight:900;margin:0}.heading-line{border-top:3px solid #0b4b91;margin:7px auto 5px;max-width:520px;position:relative}.firm-address{font-size:18px;font-weight:800}.firm-vat-number,.firm-contact-number{color:#0b376d;font-size:15px;font-weight:900;margin-top:2px}.ledger-title{background:#0b4b91;border-radius:6px;color:#fff;display:inline-block;font-size:20px;font-weight:900;letter-spacing:1px;margin-top:8px;padding:5px 55px}.party-meta{align-items:center;display:flex;font-size:15px;justify-content:space-between;padding:12px 32px 8px}.party-meta .party-identity{display:flex;flex-wrap:wrap;gap:8px 28px}.party-meta .party-currency{display:flex;gap:15px;white-space:nowrap}
.party-ledger .ledger-table{border-collapse:separate!important;border-spacing:0!important;display:table!important;font-size:15px;margin:0!important;min-width:900px;table-layout:fixed!important;width:100%!important}.party-ledger .ledger-table thead{display:table-header-group!important;width:auto!important}.party-ledger .ledger-table tbody{display:table-row-group!important;width:auto!important}.party-ledger .ledger-table tfoot{display:table-footer-group!important;width:auto!important}.party-ledger .ledger-table tr{display:table-row!important;width:auto!important}.party-ledger .ledger-table th,.party-ledger .ledger-table td{border-bottom:1px solid #b8cee8!important;border-left:0!important;border-right:1px solid #b8cee8!important;padding:10px 12px;text-align:center;vertical-align:middle;white-space:normal}.party-ledger .ledger-table th:first-child,.party-ledger .ledger-table td:first-child{border-left:1px solid #b8cee8!important}.party-ledger .ledger-table thead th,.party-ledger .ledger-table tfoot th{background:#073e7c!important;color:#fff;font-weight:900;position:static;text-align:center}.party-ledger .ledger-table thead th:first-child{border-top-left-radius:6px}.party-ledger .ledger-table thead th:last-child{border-top-right-radius:6px}.party-ledger .ledger-table tbody tr:nth-child(even){background:#f0f5fa}.party-ledger .ledger-table .amount{text-align:right}.party-ledger .ledger-table .empty-row{padding:28px;text-align:center}.party-ledger .ledger-table tfoot th{font-size:16px}
.party-ledger .ledger-actions{white-space:nowrap}.party-ledger .ledger-actions form{display:inline}.party-ledger .ledger-edit-btn,.party-ledger .ledger-delete-btn{border:0;border-radius:5px;color:#fff!important;display:inline-block;font-size:11px;font-weight:900;margin:2px;padding:6px 7px;text-decoration:none}.party-ledger .ledger-edit-btn{background:#d97706}.party-ledger .ledger-delete-btn{background:#dc2626;cursor:pointer}
@media(max-width:800px){.ledger-header{padding-left:5px;padding-right:5px}.firm-monogram{display:none}.firm-heading h1{font-size:23px}.party-ledger{overflow-x:auto;padding:0 5px 10px}.party-meta{align-items:flex-start;flex-direction:column;gap:6px;padding-left:5px}.party-ledger .ledger-table{min-width:800px}}
@media print{.party-ledger{border:0;padding:0}.firm-heading h1{font-size:28px}.party-ledger .ledger-table{min-width:0}.party-ledger .ledger-table th,.party-ledger .ledger-table td{padding:8px}}
</style>
