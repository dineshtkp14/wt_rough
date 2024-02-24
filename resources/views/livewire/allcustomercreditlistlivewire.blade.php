@php
                $totalNegativeDebitCreditDifference = $all->filter(function($item) {
                    return $item->debit_credit_difference < 0;
                })->sum('debit_credit_difference');
 @endphp
            
<div class="container">

    <div class="card">
        <div class="card-header">
            <a href="{{ route('companys.create') }}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
            Total Credit of all Customer: <span class="h3"><b>{{ $totalDebitCreditDifference }}</b></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Advance Payment: <span class="h3"><b>{{ $totalNegativeDebitCreditDifference }}</b></span>



            <input type="text" class="form-control float-end border-warning border border-5" placeholder="Search By Name" style="width: 250px;" wire:model="searchTerm">
            
            <!-- Filter dropdown for sorting -->
            <select class="form-select float-end border-warning border border-5" style="width: 150px;" wire:model="sortBy">
                <option value="">Sort By</option>
                <option value="asc">Low to High</option>
                <option value="desc">High to Low</option>  
            </select>
        </div>
        <div class="card-body">
            <table>
                <thead> name + huduhhuds nd
                    <tr>
                        <th>Customer Id</th>
                        <th>Customer Name</th>
                        <th>Total Due Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tbody>
                        @if (!$all->isEmpty())
                            @foreach ($all as $i)
                                @if ($i->debit_credit_difference != 0)
                                    <tr>
                                        <td data-label="Customer Id"><b>{{ $i->customerid }}</b></td>
                                        <td data-label="Customer Id"><b>{{ $i->cname }}</b>  &nbsp; ({{ $i->cphoneno }})</td>
                                        <td data-label="Invoice Id"><b>{{ $i->debit_credit_difference }}</b></td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3"><h3>No Record Found !!!!</h3></td>
                            </tr>
                        @endif
                    </tbody>
                    
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted">
            {{ $all->links() }}
        </div>
    </div>
</div>
