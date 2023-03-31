<div>
  
    <div class="container">
	

        <div class="card ">
            <div class="card-header">
               <a href="{{route('daybooks.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
              Total collected cash today : {{$totalsum}}
   
                 <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name, phoneno, email" style="width: 250px;" wire:model="searchTerm" >
            </div>
            <div class="card-body">
        
        
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Amount</th>
                    <th>Paisa</th>
                    <th>Date</th>
                    <th>Remarks</th>
                    <th>Action</th>
                    
                </tr>
            </thead>
            <tbody>
            @if ($custo->isNotEmpty())
                                @foreach ($custo as $i)
                <tr>
                    <td data-label="Id">{{ $i->id }}</td>
                    <td data-label="Name">{{ $i->name }}</td>
                    <td data-label="Address">{{ $i->address}}</td>
                    <td data-label="Contact No.">{{ $i->contact }}</td>
                    <td data-label="Amount">{{ $i->amount }}</td>
                    <td data-label="Paisa">{{ $i->modeofpay }}</td>
                    <td data-label="Date">{{ $i->date }}</td>
                    <td data-label="Remarks">{{ $i->remarks }}</td>
                    <td>
                        <a href="{{Route('daybooks.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                         
                                  
                         <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                         <form id="eea{{$i->id}}" action="{{ route('daybooks.destroy',$i->id)}}" method="post">
                         @csrf
                         @method('delete')
                         
                         </form>
                    </td>
                    
                </tr>
                @endforeach
                
            @else
             <h5>No Record Found</h>
                
             @endif
            </tbody>
        </table>
        </div>
</div>
