<div class="container">
  
    <div class="card text-center">
        <div class="card-header">
           <span class="float-start fw-bold fs-3"> View Price List</span>

             <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name ,Id or phoneno" style="width: 250px;" wire:model="searchTerm" >
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Items Name</th>
                        <th>Sale Price</th>
                        <th>Note</th>
                        <th>Date</th>
                        <th>More</th>
                        <th>Action</th>
            
                        
                    </tr>
                </thead>
                <tbody>
                   
                @if ($pricelist->count())
                                    @foreach ($pricelist as $i)
                    <tr>
                        <td data-label="Id">{{$i->id}}</td>
                        <td data-label="Name">{{ $i->itemname }}</td>
                        <td data-label="Address">{{ $i->saleprice}}</td>
                        <td data-label="Contact No.">{{ $i->note }}</td>
                        <td data-label="Amount">{{ $i->created_at }}</td>
                        <td data-label="Amount">
                            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{$i->id}}">
                View  More
              </button>
              
              <!-- Modal -->
              <div class="modal fade" id="exampleModal{{$i->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
            
                  <div class="modal-content">
                    <div class="modal-header">
                      <h1 class="modal-title fs-5" id="exampleModalLabel">View Addtional Details</h1>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                     Cost Price: {{$i->costprice}} <br>
                     WholeSale Price: {{$i->wholesaleprice}}
                    
                    </div>
                    <div class="modal-footer">
                      
                    </div>
                  </div>
                </div>
              </div>
                        </td>
            
                        
                       
                       
                        <td>
                        <a href="{{Route('pricelists.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
            
                     
            <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
            <form id="eea{{$i->id}}" action="{{ route('pricelists.destroy',$i->id)}}" method="post">
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
            {{ $pricelist->links() }}
        
        </div>
      </div>

    
    
    </div>
    