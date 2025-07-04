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
                                    <td data-label="Total Due Amount"><b>{{ $item->debit_credit_difference }}
                                        @if ($item->debit_credit_difference >= 0 && $item->debit_credit_difference < 100)
                                        <button class="btn btn-outline-warning btn-sm ms-2 border-2 rounded-pill">
                                            Discount
                                        </button>                                        
                                        @endif
                                    </td>
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

<div class="card mt-5 shadow" id="discountFormCard" style="display: none;">
    <div class="card-header bg-warning text-white fw-bold">
        Apply Discount
    </div>
    <div class="card-body">
        <form action="{{ route('Creditcpayments.CreditdueDiscount') }}" method="POST">
            @csrf

            <!-- Hidden input to track customer -->
            <input type="hidden" name="customerid" id="discountCustomerId">

            <div class="mb-3">
                <label for="discountCustomerName" class="form-label">Customer Name</label>
                <input type="text" id="discountCustomerName" class="form-control" disabled>
            </div>

            <div class="mb-3">
                <label for="discount_amount" class="form-label">Discount Amount <span class="text-danger">*</span></label>
                <input type="number" name="discount_amount" id="discount_amount" class="form-control @error('discount_amount') is-invalid @enderror" required>
                @error('discount_amount')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-3">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" rows="2"></textarea>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">Submit Discount</button>
            </div>
        </form>
    </div>
</div>