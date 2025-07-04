<div class="container">
    <button class="button mb-2 btn btn-primary" wire:click="generateallcustomerPDF">
        <i class="fas fa-file-pdf icon"></i> DOWNLOAD PDF
    </button>

    <span> 
        @if (!$all->isEmpty())
    <!-- Content to display when $all is not empty -->
    <!-- For example, you can display the count of items -->
    Total Credit Customer No: {{ $all->count() }}
@else
    <!-- Content to display when $all is empty -->
    No items found.
@endif
    </span>

    <div class="card">
        <div class="card-header">
            <a href="{{ route('companys.create') }}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
            <span class="me-5 fw-bold">Total Credit: <span class="h2 text-success">{{$totalDebitCreditDifferencewhole}} </span> </span>

            Total Credit of This Page: <span class="h5"><b>{{ $totalDebitCreditDifference  }}</b></span><i class="fas fa-arrow-down"></i>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Advance Payment: <span class="h3"><b>{{ $totalNegativeDebitCreditDifference }}</b></span>
            
            <!-- Filter dropdown for sorting -->
            <select class="form-select float-end border-warning border border-5" style="width: 150px;" wire:model="sortBy">
                <option value="">Sort By</option>
                <option value="asc">Low to High</option>
                <option value="desc">High to Low</option> 
                <option value="date_asc">Oldest First</option>
                <option value="date_desc">Newest First</option> 
            </select>
        </div>
        <div class="card-body">
            <table>
                <thead> 
                    <tr>
                        <th>S.N</th>
                        <th>Customer Id</th>
                        <th>Customer Name</th>
                        <th>Total Due Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sn = ($all->currentPage() - 1) * $all->perPage() + 1;
                    @endphp

                    @if (!$all->isEmpty())
                        @foreach ($all as $item)
                            @if ($item->debit_credit_difference != 0)
                                <tr>
                                    <td>{{ $sn++ }}</td>
                                    <td data-label="Customer Id"><b>{{ $item->customerid }}</b></td>
                                    <td data-label="Customer Name"><b>{{ $item->cname }}</b>  &nbsp; ({{ $item->cphoneno }})</td>
                                    <td data-label="Total Due Amount"><b>{{ $item->debit_credit_difference }}<button>Discount </button></b></td>
                                    <td data-label="Total Due Amount"><b>{{ $item->latest_date  }}</b></td>

                                </tr>
                            @endif
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4"><h3>No Record Found !!!!</h3></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted">
            {{ $all->links() }}
        </div>

        <button class="button  mb-2 btn btn-primary " wire:click="generateallcustomerPDF">
            <i class="fas fa-file-pdf icon"></i> DOWNLOAD PDF
        </button>
    </div>
</div>
