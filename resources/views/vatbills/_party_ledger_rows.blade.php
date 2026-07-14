@forelse ($partyLedgers as $ledger)
    <tr>
        <td>{{ $partyLedgers->firstItem() + $loop->index }}</td>
        <td>
            <strong>{{ $ledger->party_name }}</strong>
            <small>{{ $ledger->address ?: '-' }}</small>
        </td>
        <td><span class="vat-number">{{ $ledger->vat_no ?: '-' }}</span></td>
        <td>{{ $ledger->phoneno ?: '-' }}</td>
        <td>{{ $ledger->firm_type }}</td>
        <td><span class="bill-count">{{ $ledger->bill_count }}</span></td>
        <td class="amount">Rs {{ number_format((float) $ledger->taxable_total, 2) }}</td>
        <td>
            {{ $ledger->latest_bill_date
                ? \App\Support\NepaliDate::adToBsString($ledger->latest_bill_date, 'en')
                : '-' }}
        </td>
        <td>
            <a href="{{ route('vat-party-ledgers.show', ['vatBill' => $ledger->vat_bill_id, 'firm_type' => $ledger->firm_type]) }}"
                class="btn btn-success btn-sm">
                <i class="fa-solid fa-book"></i> Open Ledger
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center py-5">
            <i class="fa-solid fa-folder-open fa-2x text-muted mb-2"></i>
            <div>No confirmed VAT party ledgers found.</div>
        </td>
    </tr>
@endforelse
