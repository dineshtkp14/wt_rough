<div wire:poll.30000ms> {{-- Update every 30 seconds (adjust the duration as needed) --}}
    {{-- The whole world belongs to you. --}}
    <!-- resources/views/livewire/item-sales.blade.php -->

    <div class="container">
        <div class="card ">
            <div class="card-header">
                <div class="row "> 
                    <div class="col-md-6 ">     
                        <a href="" style="width: 200px; text-decoration:none" class=" text-center  h3 text-dark"> ITEMSALES  TABLE</a>
                        <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-5" style="background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>
                    </div>

                    <div class="col-md-6 float-end">
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

                            <th>Created at </th>
                            <th>Bill No</th>
                            <th>Items Name</th>
                            <th>Unstocked Name</th>
                            <th>Quantity</th>
                            <th>Cost Price</th>
                            <th>Original Sell Price</th>
                            <th>Sold Price</th>
                            <th>Sub-Total</th>
                            <th>Profit</th>
                            {{-- <th>Action</th> --}}

                        </tr>
                    </thead>
                    <tbody>
                        @if ($cus->isNotEmpty())
                            @foreach ($cus as $item)
                                <tr>
                                    <td data-label="Bill No">{{ $item->date }}</td>

                                    <td data-label="Bill No">{{ $item->created_at }}</td>

                                    <td data-label="Bill No">{{ $item->invoiceid }}</td>
                                    <td data-label="Items Name">{{ $item->itemname ? $item->itemname : '-' }}</td>
                                    <td data-label="Unstocked Name">{{ $item->unstockedname ? $item->unstockedname : '-' }}</td>
                                    <td data-label="Quantity">{{ $item->quantity }}</td>
                                    <td data-label="cost Price">{{ $item->itemdlp}}</td>

                                    <td data-label="Originl Price">{{ $item->itemprice ? $item->itemprice : '-' }}</td>
                                    <td data-label="sold Price">{{ $item->price }}</td>
                                    <td data-label="Sub-Total">{{ $item->subtotal }}</td>
                                    {{-- <td data-label="profit">{{ ($item->price-$item->itemdlp)*$item->quantity }}</td> --}}
                                    <td data-label="Sub-Total">{{ !empty($item->itemdlp) ? ($item->price - $item->itemdlp) * $item->quantity : '-' }}  </td>
                                  

                                    {{-- <td><a href="{{ route('itemsales.edit', $item->id) }}" class="btn" style="background:#389AF5;color:white;">EDIT</a> </td> --}}
                                   

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8"><h3>No Record Found.</h3></td>
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
