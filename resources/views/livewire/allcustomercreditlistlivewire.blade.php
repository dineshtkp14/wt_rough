<div class="container">
	<div class="card">
		<div class="card-header">
            <a href="{{route('companys.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer 

              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name, phoneno, email" style="width: 250px;" wire:model="searchTerm" >
         </div>
		<div class="card-body">
    <table>
        <thead>
            <tr>
              
                <th>Customer Id</th>
                <th>Customer Name</th>
                <th>Total Due Amount</th>
              
                
            </tr>
        </thead>
        <tbody>
            @if (!$all->isEmpty())
                @foreach ($all as $i)
                    <tr>
                      
                        <td data-label="Customer Id"><b>{{ $i->customerid }}</b></td>
                        <td data-label="Customer Id"><b>{{ $i->cname }}</b></td>

                        <td data-label="Invoice Id"><b>{{ $i->debit_credit_difference  }}</b></td>
                       
                      

                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="11"><h3> No Record Found !!!!</h3></td>
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