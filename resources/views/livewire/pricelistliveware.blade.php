<div class="container price-list-container">
  
    <div class="card text-center price-list-card">
        <div class="price-list-toolbar">
            <div class="price-list-heading">
                <span class="price-list-heading-icon"><i class="fa-solid fa-tags"></i></span>
                <div><small>Inventory pricing</small><strong>Items Price List</strong></div>
            </div>
            <div class="price-list-toolbar-right">
                <div class="price-list-search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="price-list-search" placeholder="Search item name or ID" wire:model="searchTerm">
                </div>
                <a href="{{ route('pricelists.create') }}" class="price-list-add-btn"><i class="fa-solid fa-plus"></i> Add Price</a>
            </div>
        </div>
        <div class="price-list-table-wrap">
            <table class="price-list-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Items Name</th>
                        <th>Unit</th>
                        <th>MRP</th>
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
                        <td data-label="Unit">{{ $i->unit ?: '-' }}</td>
                        <td data-label="Address">{{ $i->saleprice}}</td>
                        <td data-label="Contact No.">{{ $i->note }}</td>
                        <td data-label="Amount">{{ $i->created_at }}</td>
                        <td data-label="More">
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
                     <h2>Cost Price: {{$i->costprice}}  /-<br></h2>
                    <h2> Unit: {{ $i->unit ?: '-' }}</h2>
                    <h2> WholeSale Price: {{$i->wholesaleprice}}  /-</h2>
                    
                    </div>
                    <div class="modal-footer">
                      
                    </div>
                  </div>
                </div>
              </div>
                        </td>
            
                        
                       
                       
                        <td><div class="price-list-action-cell">
                        <a href="{{Route('pricelists.edit',$i->id)}}" class="btn btn-info"  rel="noopener noreferrer">EDIT</a>
            
                     
            <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
            <form id="eea{{$i->id}}" action="{{ route('pricelists.destroy',$i->id)}}" method="post">
            @csrf
            @method('delete')
            
            </form>
                        </div></td>
                        
                    </tr>
                    @endforeach
                    
                @else
                 <tr><td colspan="8" class="price-list-empty">No price-list records found.</td></tr>
                    
                 @endif
                </tbody>
            </table>
        </div>
        <div class="price-list-footer text-muted">
            {{ $pricelist->links() }}
        
        </div>
      </div>

    
    
    </div>
    
