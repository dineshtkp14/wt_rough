<div>
    {{-- If you look to others for fulfillment, you will never truly be fulfilled. --}}


    <div class="container">
        <div class="card">
            <div class="card-header">
                <a href="" class="btn btn-primary text-center ms-5">Add New Sale</a>
    
                <input type="text" class="form-control float-end border-warning border border-5" placeholder="Search..." style="width: 250px;" wire:model="searchTerm">
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Id</th>
                            <th>Customer Name</th>
                            <th>Subtotal</th>
                            <th>Discount</th>
                            <th>Total</th>
                            <th>Notes</th>
                            <th>Date</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @if ($all->count())
                            @foreach ($all as $sale)
                                <tr>
                                    <td>{{ $sale->id }}</td>
                                    <td>{{ $sale->customerid }}</td>
                                    <td>{{ $sale->name }}</td>
                                    <td>{{ $sale->subtotal }}</td>
                                    <td>{{ $sale->discount }}</td>
                                    <td>{{ $sale->total }}</td>
                                    <td>{{ $sale->notes }}</td>
                                    <td>{{ $sale->created_at }}</td>
                                    {{-- <td>
                                        <a href="{{ route('itemssales.edit', $sale->id) }}" class="btn" style="background:#389AF5;color:white;">EDIT</a>
    
                                        <a href="#" onclick="delfunctionsales({{ $sale->id }})" class="btn btn-danger">Delete</a>
                                        <form id="eea{{ $sale->id }}" action="{{ route('itemssales.destroy', $sale->id) }}" method="post">
                                            @csrf
                                            @method('delete')
                                        </form>
                                    </td> --}}
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
