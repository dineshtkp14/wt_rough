@php($showActions = $showActions ?? false)
@php($displayFirmName = $firmType === 'Malika & Nav Durga Traders' ? 'MALIKA AND NAWADURGA TRADERS' : strtoupper(str_replace('&', 'AND', $firmType)))
@php($monogram = $firmType === 'Malika & Nav Durga Traders' ? 'MN' : 'DD')
<div class="party-ledger">
    <div class="ledger-header">
        <div class="firm-monogram">{{ $monogram }}</div>
        <div class="firm-heading">
            <h1>{{ $displayFirmName }}</h1>
            <div class="heading-line"></div>
            <div class="firm-details">
                <div>Tikapur, Kailali</div>
                <div>VAT No: {{ $firmVatNo }}</div>
                <div>Contact No: {{ $firmContactNumbers }}</div>
            </div>
            <div class="ledger-title">PARTY LEDGER</div>
        </div>
    </div>

    <div class="party-meta">
        <div class="party-identity">
            <span><strong>Party Name:</strong> {{ $customer->name }}{{ $customer->address ? ' - ' . $customer->address : '' }}</span>
            <span><strong>Party VAT No:</strong> {{ $partyVatNo }}</span>
        </div>
        <div class="party-currency">
            <strong>Currency</strong><span>:</span><span>NPR</span>
        </div>
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
@page{size:A4 landscape;margin:9mm}
body{background:#fff;margin:0}
.party-ledger{background:#fff;border:1px solid #b8cee8;border-radius:7px;color:#001f4d;font-family:Georgia,"Times New Roman",serif;overflow:hidden;padding:20px 20px 20px}
.ledger-header{border-bottom:2px solid #0b4b91;min-height:176px;padding:0 0 10px;position:relative;text-align:center}
.firm-monogram{align-items:center;border:4px solid #0b4b91;border-radius:50%;color:#0b4b91;display:flex;font-size:28px;font-weight:900;height:98px;justify-content:center;left:100px;position:absolute;top:28px;width:98px}
.firm-heading{margin:0 auto;max-width:820px;text-align:center}
.firm-heading h1{color:#073e7c;font-size:38px;font-weight:900;letter-spacing:.3px;line-height:1.1;margin:0}
.heading-line{border-top:3px solid #0b4b91;margin:8px auto 8px;max-width:585px}
.firm-details{color:#001f4d;font-size:17px;font-weight:900;line-height:1.35}
.ledger-title{background:#0b4b91;border-radius:6px;color:#fff;display:inline-block;font-size:22px;font-weight:900;letter-spacing:1px;margin-top:10px;padding:6px 62px;text-align:center}
.party-meta{align-items:center;display:flex;font-size:18px;font-weight:900;justify-content:space-between;padding:13px 34px 9px}
.party-identity{display:flex;gap:28px;min-width:0}
.party-currency{display:flex;gap:14px;white-space:nowrap}
.party-meta strong,.party-meta span{font-weight:900}
.party-ledger .ledger-table{border-collapse:collapse!important;display:table!important;font-size:15px;margin:0!important;table-layout:fixed!important;width:100%!important}
.party-ledger .ledger-table thead{display:table-header-group!important}.party-ledger .ledger-table tbody{display:table-row-group!important}.party-ledger .ledger-table tfoot{display:table-footer-group!important}.party-ledger .ledger-table tr{display:table-row!important}
.party-ledger .ledger-table th,.party-ledger .ledger-table td{border:1px solid #b8cee8!important;padding:11px 12px;text-align:center;vertical-align:middle;white-space:normal}
.party-ledger .ledger-table thead th{background:#073e7c!important;color:#fff;font-size:16px;font-weight:900}
.party-ledger .ledger-table tbody tr:nth-child(even){background:#eef4fb}
.party-ledger .ledger-table tbody td{color:#001f4d}
.party-ledger .ledger-table .amount{text-align:right}
.party-ledger .ledger-table .empty-row{padding:24px;text-align:center}
.party-ledger .ledger-table tfoot th{background:#073e7c!important;color:#fff;font-size:17px;font-weight:900;padding:12px}
.party-ledger .ledger-actions{white-space:nowrap}.party-ledger .ledger-actions form{display:inline}.party-ledger .ledger-edit-btn,.party-ledger .ledger-delete-btn{border:0;border-radius:5px;color:#fff!important;display:inline-block;font-size:11px;font-weight:900;margin:2px;padding:6px 7px;text-decoration:none}.party-ledger .ledger-edit-btn{background:#d97706}.party-ledger .ledger-delete-btn{background:#dc2626;cursor:pointer}
@media(max-width:800px){.firm-monogram{display:none}.firm-heading h1{font-size:23px}.ledger-header{min-height:0}.party-ledger{overflow-x:auto;padding:10px}.party-meta{align-items:flex-start;flex-direction:column;gap:6px;padding-left:5px}.party-identity{display:block}.party-ledger .ledger-table{min-width:760px}}
@media print{.party-ledger{padding:0}.firm-heading h1{font-size:34px}.party-ledger .ledger-table th,.party-ledger .ledger-table td{padding:9px 10px}}
</style>
