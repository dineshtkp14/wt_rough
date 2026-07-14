@forelse ($invoices as $invoice)
    <tr>
        <td><strong>#{{ $invoice->id }}</strong></td>
        <td>{{ $invoice->inv_date }}</td>
        <td><span class="badge {{ $invoice->inv_type === 'cash' ? 'bg-success' : 'bg-warning text-dark' }}">{{ strtoupper($invoice->inv_type) }}</span></td>
        <td><strong>{{ $invoice->customer->name }}</strong><small>{{ $invoice->customer->address }}</small></td>
        <td>{{ $invoice->customer->vat_no ?: '-' }}</td>
        <td>{{ $invoice->customer->phoneno ?: '-' }}</td>
        <td><strong>Rs {{ number_format((float) $invoice->total, 2) }}</strong></td>
        <td>
            <a href="{{ route('vat-bills.create', $invoice) }}" class="btn btn-success btn-sm">
                <i class="fa-solid fa-plus"></i> Add VAT Bill
            </a>
            <a href="{{ route('onlyviewbillafterbill', ['invoiceid' => $invoice->id]) }}" class="btn btn-secondary btn-sm">View Invoice</a>
        </td>
    </tr>
@empty
    <tr><td colspan="8" class="text-center py-5">No cash/shop invoices are missing VAT bills.</td></tr>
@endforelse
