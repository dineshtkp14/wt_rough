



<div class="container">
    <span class="p-1"> Type "war" to see warning items  </span>    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="p-1"> Type "out" to see out of stock items  </span>

    <div class="card ">
         <div class="card-header">
            <a href="{{route('stocks.index')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Items {{ $all->total() }}  
              {{-- Total Warning Items: {{$warnings}} --}}

              <div class="row">
                <div class="col-3">
                    <p class="text-success">Total Number of Stock Items: <span class="h4 text-primary">{{$cou}}</span></p>
                </div>
                <div class="col-3">
                    <p class="text-success">Total out Of Stock Items <span class="text-danger"><b>(OUT)</b></span>: <span class="h4 text-primary">{{$x}}</span></p>
                </div>
    
                <div class="col-3">
                    <p class="text-success">Total Warning Items <span class="text-danger"><b>(WAR)</b></span>: <span class="h4 text-primary">{{$war}}</span></p>
                </div>
                <div class="col-3">
                    <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Here" style="width: 250px;" wire:model="searchTerm" >
                </div>
              </div>
         </div>
         <div class="card-body">
            <table class="table">
                <thead>
                     <tr>
                         <th >Id </th>
                         <th >Date</th>
                         <th>Distributor Name</th>
                         <th>Items Name</th>
            
                         <th >Quantity</th>
                         <th >MRP</th>
                         <th >Extra</th>
                         <th >Show Warning</th>
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
                                   <td>{{ $i->mrp }}  &nbsp; &nbsp; <!-- Button trigger modal -->
                                    </td>

                                   <td><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{$i->id}}">
                                    Extra
                                  </button>
                                  
                                  <!-- Modal -->
                                  <div class="modal fade" id="exampleModal{{$i->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                
                                      <div class="modal-content">
                                        <div class="modal-header">
                                          <h1 class="modal-title fs-5" id="exampleModalLabel">View Addtional Details  (Excluding VAT 13%)</h1>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                         <h2>Cost Price: {{$i->costprice}}  /-<br></h2>
                                        <h2> WholeSale Price: {{$i->wholesale_price}}  /-</h2>
                                        <h2> Competitive Retail Sale Price: {{$i->com_Retail_price}}  /-</h2>
                                        <h2> Competitive Wholesale Sale Price: <span class="text-success">{{$i->com_wholesale_price	}} </span> /-</h2>

                                        
                                        </div>
                                        <div class="modal-footer">
                                          
                                        </div>
                                      </div>
                                    </div>
                                  </div></td>

                                   <td>{{ $i->showwarning }}</td>

                              
                                 
                                   <td>  

                                   


                                    @if ($i->quantity <= $i->showwarning  and $i->quantity >= 1)
                                    <div class="span-box">
                                        <span class="btn btn-warning ">warning</span>
                                    </div>
                                @elseif($i->quantity == 0)
                                    <div class="span-box">
                                        <span class="btn btn-danger  ">outofstock</span>


                                        <form id="removeForm{{$i->id}}" action="{{ route('stocks.updateofs') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $i->id }}">
                                            <button type="submit" class="btn btn-info" onclick="confirmRemove({{$i->id}})">remove</button>
                                        </form>

                                    </div>

                                 @elseif($i->quantity < 0)
                                    <div class="span-box">
                                        <span class="btn btn-primary  ">Data in Minus</span>
                                    </div>
                                @else
                                    <div class="span-box">
                                        <span class="btn btn-success "> Available</span>
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
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener("submit", function(event) {
                var formId = this.getAttribute('id');
                var itemId = formId.replace('removeForm', '');
                if (!confirm("Please make sure to order before removing.Are you sure you want to remove item " + itemId + "?")) {
                    event.preventDefault(); // Prevent form submission
                }
            });
        });
    });
</script>