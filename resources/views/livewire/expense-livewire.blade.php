<div class="container">
    <div class="cardd mt-5 ">
         <div class="card-header">
            <a href="{{route('customerinfos.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total {{ $expense->total() }}  
             <a href="{{ route('expenses.create') }}" class="btn  ms-5" style="background-color: #2a1604; border-color: #323933; color: white; transition: background-color 0.3s, border-color 0.3s;">
                <i class="fas fa-calendar-plus"></i> <!-- Font Awesome icon for calendar plus -->
                ADD NEW EXPENSE
            </a> 
                  
             <a href="{{ route('expenses.search') }}" class="btn btn-primary ms-5" style="background-color: #1100ff; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;">
                <i class="fas fa-calendar"></i> <!-- Font Awesome icon for calendar -->
                SEARCH EXPENSES BY DATE
            </a>

           
            <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-5" style="background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>

            
              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Here" style="width: 250px;" wire:model="searchTerm" >
         </div>
         <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th>Bill No</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($expense->count())
                    @foreach ($expense as $exp)
                        <tr>
                            <td>{{ $exp->date }}</td>
                            <td>{{ $exp->particulars }}</td>
                            <td>{{ $exp->billno }}</td>
                            <td>{{ $exp->amount }}</td>
                            <td>{{ $exp->notes }}</td>
                            <td>
                                <a href="{{ route('expenses.edit', $exp->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this expense?')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                                
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6"><h4>No Record Found</h4></td>
                    </tr>
                @endif
                
                </tbody>
            </table>

         </div>
         <div class="card-footer text-muted">
            {{ $expense->links() }}
         </div>
       </div>



  
</div>
