<div class="container">
    <div class="card">
        <div class="card-header">
            <a href="{{ route('chequedeposit.create') }}">
                <img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/>
            </a>
            Total {{ $all->total() }}
            <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-5" style="background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;">
                <i class="fas fa-file-invoice"></i> ADD NEW INVOICE
            </a>
            <input type="text" class="form-control float-end border-warning border border-5" placeholder="Search Here" style="width: 250px;" wire:model="searchTerm">
            
            <!-- Select Date Filter -->
            <select wire:model="selectedDateFilter" class="me-4 form-select float-end border-warning border border-5" style="width: 200px;">
                <option value="">Select Date Filter</option>
                <option value="today">Today</option>
            </select>
        </div>
        <div class="card-body">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Customer Id</th>

                        <th>Cheque Date</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Bank Name</th>
                        <th>Added By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @if ($all->count())
                        @foreach ($all as $i)
                            <tr>
                                <td>{{ $i->id }}</td>
                                <td>{{ $i->date }}</td>
                                <td>{{ $i->customerid }}</td>

                                <td>{{ $i->cheque_date }}</td>
                                <td>{{ $i->amount }}</td>
                                <td>{{ $i->notes }}</td>
                                <td>{{ $i->bank_name }}</td>
                                <td>{{ $i->added_by }}</td>
                                <td>
                                    <a href="{{ route('chequedeposit.edit', $i->id) }}" class="btn" style="background:#389AF5;color:white;">EDIT</a>
                                    <a href="#" onclick="delfunctionusers({{ $i->id }})" class="btn btn-danger" rel="noopener noreferrer">Delete</a>
                                    <form id="eea{{ $i->id }}" action="{{ route('chequedeposit.destroy', $i->id) }}" method="post">
                                        @csrf
                                        @method('delete')
                                    </form>
                                </td>
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
