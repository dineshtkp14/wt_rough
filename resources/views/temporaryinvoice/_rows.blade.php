@forelse ($temporaryInvoices as $invoice)
    <tr class="temporary-invoice-row">
        <td><b>{{ $invoice->invoice_number ?? $invoice->id }}</b></td>
        <td>{{ $invoice->invoice_date }}</td>
        <td>{{ $invoice->customer_name }}</td>
        <td>{{ $invoice->contact_number }}</td>
        <td>{{ $invoice->customer_address }}</td>
        <td>{{ $invoice->items_count }}</td>
        <td><b>{{ number_format($invoice->total, 2) }}</b></td>
        <td>
            <div class="temporary-row-actions">
            <a href="{{ route('temporaryinvoice.show', $invoice) }}"
                class="temporary-row-action view temporary-invoice-view-btn"
                data-url="{{ route('temporaryinvoice.show', $invoice) }}">
                <i class="fa-solid fa-eye"></i>
            </a>
            <a href="{{ route('temporaryinvoice.print', $invoice) }}"
                class="temporary-row-action print temporary-invoice-print-direct"
                data-url="{{ route('temporaryinvoice.print', $invoice) }}">
                <i class="fa-solid fa-print"></i>
            </a>
            <form action="{{ route('temporaryinvoice.destroy', $invoice) }}" method="post"
                class="d-inline"
                onsubmit="return confirm('Delete this temporary invoice?');">
                @csrf
                @method('DELETE')
                <button class="temporary-row-action delete" type="submit">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">No temporary invoices found.</td>
    </tr>
@endforelse
