<div class="container">
    <div class="card">
        <div class="card-header">
            <a href="{{ route('openingbalances.create') }}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
            Total Number of Customers: {{ $all->total() }}
            <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-5" style="background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> Add New Invoice</a>
            <input type="text" class="form-control float-end border-warning border border-5" placeholder="Search Name, phoneno, email" style="width: 250px;" wire:model="searchTerm">
        </div>
        <div class="card-body">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Customer Id</th>
                        <th>Customer Name</th>
                        <th>Particulars</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Added By</th>
                        <th>Created_at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($all->count() > 0)
                    @foreach ($all as $ledgerDetail)
                    <tr>
                        <td>{{ $ledgerDetail->id }}</td>
                        <td>{{ $ledgerDetail->date }}</td>
                        <td>{{ $ledgerDetail->customerid }}</td>
                        <td>{{ $ledgerDetail->customername }}</td>
                        <td>{{ $ledgerDetail->particulars }}</td>
                        <td>{{ $ledgerDetail->debit }}</td>
                        <td>{{ $ledgerDetail->notes }}</td>
                        <td>{{ $ledgerDetail->added_by }}</td>
                        <td>{{ $ledgerDetail->created_at }}</td>
                        <td>
                            @if (auth()->user()->email != 'dineshtkp14@gmail.com')
                            @if (Session::has('success') && $ledgerDetail->id == session('lastInsertedId'))
                           
                            <button class="btn btn-danger" onclick="delfunctionusers({{ $ledgerDetail->id }})">Delete</button>
                            <form id="eea{{ $ledgerDetail->id }}" action="{{ route('openingbalances.destroy', $ledgerDetail->id) }}" method="post">
                                @csrf
                                @method('delete')
                            </form>
                            @endif
                            @else
                            <!-- Actions for dineshtkp14@gmail.com -->
                            <a href="{{ route('openingbalances.edit', $ledgerDetail->id) }}" class="btn btn-primary">Edit</a>
                            <button class="btn btn-danger" onclick="delfunctionusers({{ $ledgerDetail->id }})">Delete</button>
                            <form id="eea{{ $ledgerDetail->id }}" action="{{ route('openingbalances.destroy', $ledgerDetail->id) }}" method="post">
                                @csrf
                                @method('delete')
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="7">No record found</td>
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
