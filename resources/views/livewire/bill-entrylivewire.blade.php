<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}
</div>
<div class="container">
	<div class="card">
		<div class="card-header">
            <a href="{{route('companybillentry.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer 

			 <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name, phoneno, email" style="width: 250px;" wire:model="searchTerm" >
			</div>
		<div class="card-body">
			<table>
				<thead>
					<tr>
					<th>Id</th>
					<th>Company ID</th>
					<th>Particulars</th>
					
					<th>Voucher No</th>
					<th>Date</th>
					<th>Debit</th>
					<th>credit</th>
                    <th>Notes</th>
					<th>Action</th>


			
					</tr>
				</thead>
	<tbody>
    @if ($all->isNotEmpty())
                        @foreach ($all as $i)
		<tr>
			<td data-label="Id">{{ $i->id }}</td>
			<td data-label="Name">{{ $i->companyid }}</td>
			<td data-label="Address">{{ $i->particulars}}</td>
			<td data-label="Contact No.">{{ $i->voucher_type }}</td>
			<td data-label="Contact No.">{{ $i->date }}</td>
            <td data-label="Contact No.">{{ $i->debit }}</td>
           

			<td data-label="Amount">{{ $i->credit }}</td>
            <td data-label="Remarks">{{ $i->notes }}</td>
			<td data-label="action">

				<a href="{{Route('companybillentry.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                          
                                   
                          <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                          <form id="eea{{$i->id}}" action="{{ route('companybillentry.destroy',$i->id)}}" method="post">
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