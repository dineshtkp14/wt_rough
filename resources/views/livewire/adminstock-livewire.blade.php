<div class="container">
   


    <button wire:click="generatePDF">Generate PDF</button>

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
            <span class="p-1">Type "out" to see out of stock items</span>  <span class="p-1">Type "ava" to see Available items</span> </span> <span class="p-1">Type "minus" to see minus items</span>
        </div>
        <div class="card-body overflow-auto">
            
            <table class="table">
                
                <thead>
                    <tr>
                        <th>Item Id</th>
                        <th>Bill No</th>

                        <th>Date</th>
                        <th>Distributor Name</th>
                        <th>Items Name</th>
                        <th>Opening Stock</th>
                        <th>Balance(I+O)</th>
                        <th class=" bg-danger">Quantity (IN) org</th>
                        <th>Check (in)</th>
                        <th>Out</th>
                        <th>Unit</th>
                        <th>Firm Name</th>
                        <th>Items Store Area</th>
                        <th>MRP</th>
                      
                      
                        <th>Show Warning</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($all->count())
                    @foreach ($all as $i)
                    <tr>
                        <td>{{ $i->id }}</td>
                        <td>{{ $i->billno }}</td>

                        <td>{{ $i->date }}</td>
                        <td>{{ $i->companyname }}</td>
                        <td>{{ $i->itemsname }}</td>
                        <td>{{ $i->opening_stock }}</td>
                        {{-- <td>{{ $i->opening_stock-$sellsquantity_out[$i->id] ?? ''+ $sellsquantity_out[$i->id] ?? '' }}</td> --}}

                        <td>{{ ($i->opening_stock - ($sellsquantity_out[$i->id] ?? 0)) + ($sellsquantity_out[$i->id] ?? 0) }}</td>

                       
                        <td><button class="btn btn-danger">{{ $i->quantity }} </button></td>
                        
                        <td>{{ $i->opening_stock-$sellsquantity_out[$i->id] ?? '' }}</td>
                        <td>{{ $sellsquantity_out[$i->id] ?? '' }}</td>

                        <td>{{ $i->unit }}</td>
                        <td>{{ $i->firm_name }}</td>
                        <td>{{ $i->item_store_area }}</td>

                      

                        <td>{{ $i->mrp }} &nbsp; &nbsp; <!-- Button trigger modal --></td>
                       
                            
                     


                        <td>{{ $i->showwarning }}</td>
                        <td>
                            @if ($i->quantity <= $i->showwarning  and $i->quantity >= 1)
                            <div class="span-box">
                                <span class="btn btn-warning">warning</span>
                            </div>
                            @elseif($i->quantity == 0)
                            <div class="span-box">
                                <span class="btn btn-danger">outofstock</span>
                                
                            </div>
                            @elseif($i->quantity < 0)
                            <div class="span-box">
                                <span class="btn btn-primary">Data in Minus</span>
                            </div>
                            
                            @else
                            <div class="span-bo">
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