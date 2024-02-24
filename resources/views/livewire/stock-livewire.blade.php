<div class="container">
   



    <form wire:submit.prevent="filterByFirm">
        <div class="row">
            <div class="col-md-5">
                <div style="display: flex; align-items: center;">
                    <span>CHOOSE FIRM Name</span>
                    <select wire:model="firm_name" class="form-select @error('firm_name') is-invalid @enderror" id="firm_name" style="border-color: red;">
                        <option value="">Select Firm</option>
                        <option value="Durga">Durga And Dinesh Traders</option>
                        <option value="Malika">Malika & Nav Durga Traders</option>
                        <option value="OmHari">Om Hari Tradelink</option>
                        <option value="Bajgain_Supp">Bajgain Suppliers</option>
                    </select>
                </div>
                @error('firm_name')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </form>
    
    <br>
   

    <div class="card">
        <!-- Card Content for displaying items -->
        <div class="card-header">
            <a href="{{ route('stocks.index') }}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
            Total No Of Items {{ $all->total() }}  
            {{-- Total Warning Items: {{$warnings}} --}}
            <div class="row">
                <div class="col-3">
                    <p class="text-success">Total Number of Stock Items: <span class="h4 text-primary">{{ $cou }}</span></p>
                </div>
                <div class="col-3">
                    <p class="text-success">Total out Of Stock Items <span class="text-danger"><b>(OUT)</b></span>: <span class="h4 text-primary">{{ $x }}</span></p>
                </div>
                <div class="col-3">
                    <p class="text-success">Total Warning Items <span class="text-danger"><b>(WAR)</b></span>: <span class="h4 text-primary">{{ $war }}</span></p>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control float-end border-warning border border-5" placeholder="Search Here" style="width: 250px;" wire:model="searchTerm">
                </div>
            </div>
           <span class="float-end"> <span class="p-1">Type "war" to see warning items</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            <span class="p-1">Type "out" to see out of stock items</span> </span>
        </div>
        <div class="card-body">
            
            <table class="table">
                
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Distributor Name</th>
                        <th>Items Name</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Firm Name</th>
                        <th>MRP</th>
                        <th>Update Price</th>
                        <th>Extra</th>
                        <th>Show Warning</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($all->count())
                    @foreach ($all as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>{{ $i->date }}</td>
                        <td>{{ $i->distributorname }}</td>
                        <td>{{ $i->itemsname }}</td>
                        <td>{{ $i->quantity }}</td>
                        <td>{{ $i->unit }}</td>
                        <td>{{ $i->firm_name }}</td>
                        <td>{{ $i->mrp }} &nbsp; &nbsp; <!-- Button trigger modal --></td>
                        <td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{ $i->id }}">Extra</button>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal{{ $i->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-light">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">View Additional Details (Excluding VAT 13%)</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <h4>Cost Price: <b>{{ $i->costprice }} </b>/-</h4>
                                            <h4>Wholesale Price: <b>{{ $i->wholesale_price }}</b> /-</h4>
                                            <h4>Competitive Retail Sale Price:<b> {{ $i->com_Retail_price }}</b> /-</h4>
                                            <h4>Competitive Wholesale Sale Price: <span class="text-success"><b>{{ $i->com_wholesale_price }}</b></span> /-</h4>
                                        </div>
                                        <div class="modal-footer">
                                           
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </td>

                      

                        <td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eexampleModal{{ $i->id }}">Update Price</button>
                            <!-- Modal 222-->
                            <div class="modal fade" id="eexampleModal{{ $i->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel"><h2>PRICE UPDATE FORM</h2></h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="updateForm{{ $i->id }}" class="row gx-3 gy-3" action="{{ route('stockpriceupdate', $i->id) }}" method="post">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="col-md-12">
                                                    <label for="wp" class="form-label" style="color: #333;"><b>$Wholesale Price Per (PCS/kg)</b></label>
                                                    <input type="text" id="wp" name="wp" class="form-control border border-primary @error('wp') is-invalid @enderror" value="{{ old('wp') ?? $i->wholesale_price }}" required >
                                                    @error('wp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <label for="competetiveretail" class="form-label" style="color: #333;"><b>$Competitive Retail Sale Price Per (PCS/kg)</b></label>
                                                    <input type="text" id="competetiveretail" name="competetiveretail" class="form-control border border-primary @error('competetiveretail') is-invalid @enderror" value="{{ old('competetiveretail')?? $i->com_Retail_price }}"required >
                                                    @error('competetiveretail')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <label for="competetivewholesale" class="form-label" style="color: #333;"><b>$Competitive Wholesale Sale Price Per (PCS/kg)</b></label>
                                                    <input type="text" id="competetivewholesale" name="competetivewholesale" class="form-control border border-primary @error('competetivewholesale') is-invalid @enderror" value="{{ old('competetivewholesale')?? $i->com_wholesale_price }}"required >
                                                    @error('competetivewholesale')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-lg btn-block text-center btn-success w-100">Update Prices</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer"></div>
                                    </div>
                                </div>
                            </div>
                        </td>


                        <td>{{ $i->showwarning }}</td>
                        <td>
                            @if ($i->quantity <= $i->showwarning  and $i->quantity >= 1)
                            <div class="span-box">
                                <span class="btn btn-warning">warning</span>
                            </div>
                            @elseif($i->quantity == 0)
                            <div class="span-box">
                                <span class="btn btn-danger">outofstock</span>
                                <form id="removeForm{{ $i->id }}" action="{{ route('stocks.updateofs') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $i->id }}">
                                    <button type="submit" class="btn btn-info" onclick="confirmRemove({{ $i->id }})">remove</button>
                                </form>
                            </div>
                            @elseif($i->quantity < 0)
                            <div class="span-box">
                                <span class="btn btn-primary">Data in Minus</span>
                            </div>
                            @else
                            <div class="span-box">
                                <span class="btn btn-success">Available</span>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="5">No record found</td>
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

<script>

document.addEventListener('DOMContentLoaded', function () {
        @foreach($all as $item)
            var updateForm{{ $item->id }} = document.getElementById('updateForm{{ $item->id }}');
            updateForm{{ $item->id }}.addEventListener('submit', function (event) {
                var inputs = this.querySelectorAll('input[type="text"]');
                var isValid = true;

                inputs.forEach(function (input) {
                    if (!input.value.trim()) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        @endforeach
    });
     </script>