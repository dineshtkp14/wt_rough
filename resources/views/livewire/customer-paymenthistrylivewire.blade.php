<div wire:poll.30000ms> 

    <div class="container">

    @if (Session::has('success'))
        <div class="bg-success text-white alert alert-success w-50">
        {{ Session::get('success') }}
        </div>
    @endif  
        
    @if (Session::has('error'))
        <div class=" bg-danger text-white alert alert-danger w-50">
        {{ Session::get('error') }}
        </div>
    @endif


        
        <div class="card ">
            <div class="card-header">
                <div class="row float-end">
                    <div class="col-12 float-end">
                        <input type="text" class="form-control float-end border-warning border border-5" 
                            placeholder="Search Here" style="width: 250px;" 
                            wire:model.debounce.30ms="searchTerm" />
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table>
        
                    <thead>
                        <tr>
                            <th>Id</th>
                            
                            <th>Date</th>
                            <th>Customer Id</th>

                            <th>Customer Name</th>
                            <th>Particulars</th>
                            <th>Voucher Type</th>
                            <th>Invoice Type</th>
                            <th>Amount</th>
                            <th>created At</th>


                            @if(auth()->user()->email == 'dineshtkp14@gmail.com')

                            <th>Action</th>
                           
                            @endif
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                   @if ($all && !$all->isEmpty())
                        @foreach ($all as $i)
                        <tr>
                            <td data-label="Id">{{ $i->id }}</td>
                            <td data-label="Name">{{ $i->date }}</td>
                            <td data-label="Name">{{ $i->customerid }}</td>
                            <td data-label="Name">{{ $i->customername }}</td>

                            <td data-label="Address">{{ $i->particulars}}</td>
                            <td data-label="Contact No.">{{ $i->voucher_type }}</td>
                            <td data-label="Contact No.">{{ $i->invoicetype }}</td>

                           
                            <td data-label="Remarks">{{ $i->credit }}</td>
                            <td data-label="Remarks">{{ $i->created_at }}</td>

                            @if(auth()->user()->email == 'dineshtkp14@gmail.com')

                            <td data-label="action">
                                {{-- <a href="{{Route('cpayments.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                                           --}}
                                                   
                                          <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                                          <form id="eea{{$i->id}}" action="{{ route('cpayments.destroy',$i->id)}}" method="post">
                                          @csrf
                                          @method('delete')
                                          
                                          </form>
                            </td>
                            @endif
                            

                            
                            
                        </tr>
                        @endforeach
                        
                    @else
                     <h5> No Record Found </h5>
                        
                     @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                {{ $all->links() }}
            </div>
        </div>
    </div>
</div>
