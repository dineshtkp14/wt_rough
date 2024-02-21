<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. blade --}}


    <div class="container">
        <div class="card">
            <div class="card-header">
    
                <input type="text" class="form-control float-end border-warning border border-5" placeholder="Search..." style="width: 250px;" wire:model="searchTerm">
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>IDE</th>
                            <th>Invoice Id</th>
                            <th>Customer Id</th>
                            <th>Customer Name</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Notes</th>
                            <th>Date</th>
                            <th>Full Date</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @if ($all->count())
                            @foreach ($all as $sale)
                                <tr>
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ $sale->invoice_id }}</td>

                                    <td>{{ $sale->customerid }}</td>
                                    <td>{{ $sale->name }}</td>
                                    <td>{{ $sale->subtotal }}</td>
                                    <td>{{ $sale->discount }}</td>
                                    <td>{{ $sale->total }}</td>
                                    <td>{{ $sale->notes }}</td>
                                    <td>{{ $sale->inv_date }}</td>
                                    <td>{{ $sale->created_at }}</td>

                                    
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8">No record found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                {{ $all->links() }}
            </div>
        </div>
    </div>
    
</div>




