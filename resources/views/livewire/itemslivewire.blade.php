

<div class="container" style=" overflow-x: auto;">


    <div class="card" style="overflow-x: auto;">
        <div class="card-header">


            <a href="{{route('items.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Items {{ $all->total() }}
             
             <a href="{{ route('itemsales.create') }}" class="btn btn-primary float-start me-5" style=" background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>

              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Here" style="width: 250px;" wire:model="searchTerm" >
         </div>
         <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Items Id</th>
                        <th>Bill No</th>
                        <th> Company Name</th>
                        <th>Date</th>
                        <th>Items Name</th>
                        <th>Quantity</th>
                        
                        <th>Cost Rate</th>
                        <th>MRP</th>
                        <th>Total</th>
                        <th>notes</th>
                        <th>My Firm</th>

                        <th>WholeSale Price</th>
                        <th>Competitive Retail Sale Price</th>
                        <th>Competitive Wholesale Sale Price</th>
                         <th>Action</th>
            
                        
                    </tr>
                </thead>
                <tbody>
                @if ($all->isNotEmpty())
                                    @foreach ($all as $i)
                    <tr>
                        <td data-label="Id">{{ $i->id }}</td>
                        <td data-label="Name">{{ $i->billno }}</td>
                        <td data-label="Company Name">{{ $i->companyname }}</td>

                        <td data-label="Contact No.">{{ $i->date }}</td>
                        <td data-label="Amount">{{ $i->itemsname }}</td>
                        <td data-label="Paisa">{{ $i->quantity}}</td>
                       
                        <td data-label="Remarks">{{ $i->costprice }}</td>
                        <td data-label="Remarks">{{ $i->mrp }}</td>
                        <td data-label="Remarks">{{ $i->total }}</td>
                        <td data-label="Remarks">{{ $i->notes }}</td>
                        <td data-label="Remarks">{{ $i->firm_name }}</td>

                        <td data-label="Remarks">{{ $i->wholesale_price }}</td>
                        <td data-label="Remarks">{{ $i->com_Retail_price }}</td>
                        <td data-label="Remarks">{{ $i->com_wholesale_price }}</td>

                        
                       
            
                        <td data-label="action">


                            @if (auth()->user()->email != 'dineshtkp14@gmail.com')

                                        @if (Session::has('success') && $i->id == session('lastInsertedId'))
                                                <a href="{{Route('items.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                                                        
                                                                
                                                <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                                            <form id="eea{{$i->id}}" action="{{ route('items.destroy',$i->id)}}" method="post">
                                                @csrf
                                                @method('delete')
                                                
                                                </form>
                                        @endif                                               

                            @else
                            <a href="{{Route('items.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>                         
                            <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                            <form id="eea{{$i->id}}" action="{{ route('items.destroy',$i->id)}}" method="post">
                                @csrf
                                @method('delete')
                                
                            </form>
                            @endif




                            <a href="{{Route('items.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                                      
                                               
                                      <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                                    <form id="eea{{$i->id}}" action="{{ route('items.destroy',$i->id)}}" method="post">
                                      @csrf
                                      @method('delete')
                                      
                                      </form>
                        </td>
                        
                    </tr>
                    @endforeach
                    
                @else
                 <h3>Database is Empty !! Plese Add to view List</h3>
                    
                 @endif
                </tbody>
            </table>

         </div>
         <div class="card-footer text-muted">
              {{ $all->links() }}
         </div>
       </div>



  
</div>

