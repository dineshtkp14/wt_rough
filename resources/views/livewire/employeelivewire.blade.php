<div class="container">
    <div class="card ">
         <div class="card-header">
            <a href="{{route('employees.index')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer {{ $all->total() }} <a href="{{ route('itemsales.create') }}" class="btn btn-primary text-center ms-5">Add New Bill</a>

              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name, phoneno, email" style="width: 250px;" wire:model="searchTerm" >
         </div>
         <div class="card-body">
              <table class="table">
                   <thead>
                        <tr>
                            <th >Id </th>
                            <th>Name</th>
                            <th >Address</th>
                            <th >Email</th>
                            <th >Phoneno</th>
                            <th >Added By</th>
                            
                            <th>Action</th>

                        </tr>
                   </thead>
                   <tbody>
                        @if ($all->count())
                             @foreach ($all as $i)
                                  <tr>
                                     <td>{{ $i->id }}</td>
                                      <td>{{ $i->name }}</td>
                                      <td>{{ $i->address }}</td>
                                      <td>{{ $i->email }}</td>
                                      <td>{{ $i->phoneno }}</td>
                                      <td>{{ $i->added_by }}</td>

                                     
                                      <td>
                                        <a href="{{Route('employees.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                         
                                  
                         <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                         <form id="eea{{$i->id}}" action="{{ route('employees.destroy',$i->id)}}" method="post">
                         @csrf
                         @method('delete')
                         
                         </form>
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
