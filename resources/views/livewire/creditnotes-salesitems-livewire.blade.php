<div wire:poll.30000ms> {{-- Update every 30 seconds (adjust the duration as needed) --}}
    {{-- The whole world belongs to you. --}}
    <!-- resources/views/livewire/item-sales.blade.php -->
okkkkkkkkkk
    <div class="container">
        <div class="card ">
            <div class="card-header">
                <div class="row float-end">
                    <div class="col-12 float-end">
                        <input type="text" class="form-control float-end border-warning border border-5" 
                            placeholder="Search Here" style="width: 250px;" 
                            wire:model.debounce.500ms="searchTerm" />
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>created_at</th>
                            <th>Bill No</th>
                            <th>Items Name</th>
                            <th>Unstocked Name</th>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Original Sell Price</th>
                            <th>Sold Price</th>
                            <th>Sub-Total</th>
                            <th>Profit</th>
                            <th>Action</th>

                            <th>Particulas</th>

                        </tr>
                    </thead>
                    <tbody>
                        @if ($cus->isNotEmpty())
                            @foreach ($cus as $item)
                                <tr>
                                    <td data-label="Bill N">{{ $item->date }}</td>
                                    <td data-label="Bill N">{{ $item->created_at }}</td>


                                    <td data-label="Bill No">{{ $item->invoiceid }}</td>
                                    <td data-label="Items Name">{{ $item->itemname ? $item->itemname : '-' }}</td>
                                    <td data-label="Unstocked Name">{{ $item->unstockedname ? $item->unstockedname : '-' }}</td>
                                    <td data-label="Quantity">{{ $item->quantity }}</td>
                                    <td data-label="cost Price">{{ $item->itemdlp}}</td>

                                    <td data-label="Originl Price">{{ $item->itemprice ? $item->itemprice : '-' }}</td>
                                    <td data-label="sold Price">{{ $item->price }}</td>
                                    {{-- <td data-label="Discount">{{ $item->discount }}</td> --}}
                                    <td data-label="Sub-Total">{{ $item->subtotal }}</td>
                                    <td data-label="profit">{{ ($item->price-$item->itemdlp)*$item->quantity }}</td>
                                    <td><a href="{{ route('creditnotes.edit', $item->id) }}" class="btn" style="background:#389AF5;color:white;">EDIT</a> </td>
                                   

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8"><h3>Database is Empty !! Please add items to view the list.</h3></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                {{ $cus->links() }}
            </div>
        </div>
    </div>
</div>
