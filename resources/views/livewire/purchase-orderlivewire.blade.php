<div class="container">
	<div class="card">
		<div class="card-header">
            <a href="{{route('purorder.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer 

              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name, phoneno, email" style="width: 250px;" wire:model="searchTerm" >
         </div>
		<div class="card-body">
			<table>
				<thead>
					<tr>
					<th>Id</th>
					<th>Date</th>
					<th>Order List</th>
					<th>Notes</th>
					

			
					</tr>
				</thead>
	<tbody>
    @if ($all->isNotEmpty())
                        @foreach ($all as $i)
		<tr>
			<td data-label="Id">{{ $i->id }}</td>
			<td data-label="Name">{{ $i->date }}</td>
			<td data-label="Address">{{ $i->orderlist}}</td>
			<td data-label="Contact No.">{{ $i->notes }}</td>
			
			<td data-label="action">
				<a href="{{Route('purorder.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                          
                                   
                          <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                          <form id="eea{{$i->id}}" action="{{ route('purorder.destroy',$i->id)}}" method="post">
                          @csrf
                          @method('delete')
                          
                          </form>
			</td>
			
		</tr>
        @endforeach
		
    @else
     <h3>No Record Found !!</h3>
		
     @endif
	</tbody>
</table>
</div>
<div class="card-footer text-muted">
	{{ $all->links() }}
</div>
</div>
</div>