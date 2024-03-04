<div class="container">

    <div class="d-inline-flex mb-2">
        <input type="text" class="form-control float-none border-warning border border-5" placeholder="SEARCH BY ITEM ID" style="width: 250px; " wire:model="searchItemId">
    </div>
    
    <div class="card">
        <div class="card-header">
            <a href="{{ route('transfergoods.create') }}">
                <img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/>
            </a>
            Total No Row {{ $all->total() }}
            {{-- <a href="{{ route('itemsales.create') }}" class="btn btn-primary text-center ms-5">Add New Bill</a> --}}

            @if (isset($sumQuantity) && $sumQuantity > 0)
               <b> Total Quantity: {{ $sumQuantity }}  <span class="bg-danger p-2 text-white circle"> Total At Shop {{$sumQuantity-$sellout}} </span> </b>
            @endif

            <input type="text" class="form-control float-end border-warning border border-5" placeholder="Search by Table Details" style="width: 250px;" wire:model="searchTerm">
        </div>
        <div class="card-body">
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th>Shift Area</th>
                        <th>Shift By</th>
                        <th>Notes</th>
                        <th>Added By</th>
                        <th style="width: 160px;">Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @forelse ($all as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>{{ $i->itemid }}</td>

                        <td>{{ $i->item->itemsname }}</td>
                        <td>{{ $i->quantity }}</td>
                        <td>{{ $i->date }}</td>
                        <td>{{ $i->shiftArea }}</td>
                        <td>{{ $i->shiftBy }}</td>
                        <td>{{ $i->notes }}</td>
                        <td>{{ $i->added_by }}</td>
                        <td style="width: 160px;">
                            <a href="{{ route('transfergoods.edit', $i->id) }}" class="btn" style="background:#389AF5;color:white;">EDIT</a>
                            <a href="#" onclick="delfunctionusers({{ $i->id }})" class="btn btn-danger">Delete</a>
                            <form id="eea{{ $i->id }}" action="{{ route('transfergoods.destroy', $i->id) }}" method="post">
                                @csrf
                                @method('delete')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">No record found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted">
            {{ $all->links() }}
        </div>
    </div>
</div>
