<div class="container">
    <div class="card ">
         <div class="card-header">
            <a href="{{route('employees.index')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer {{ $all->total() }} <a href="{{ route('itemsales.create') }}" class="btn btn-primary text-center ms-5">Add New Bill</a>
             <form action="{{ route('employees.toggle-all-locks') }}" method="post" class="d-inline ms-3">
                 @csrf
                 <input type="hidden" name="action" value="lock">
                 <button type="submit" class="btn btn-danger" onclick="return confirm('Lock login for all non-admin users?');">
                     <i class="fa fa-lock"></i> Lock All Users
                 </button>
             </form>
             <form action="{{ route('employees.toggle-all-locks') }}" method="post" class="d-inline ms-1">
                 @csrf
                 <input type="hidden" name="action" value="unlock">
                 <button type="submit" class="btn btn-success" onclick="return confirm('Unlock login for all non-admin users?');">
                     <i class="fa fa-unlock"></i> Unlock All Users
                 </button>
             </form>

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
                            <th>Status</th>
                            
                            <th>Action</th>
                            <th>Login Access</th>

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
                                        @if($i->isAdmin())
                                          <span class="badge bg-primary">Admin</span>
                                        @elseif($i->is_locked)
                                          <span class="badge bg-danger">Locked</span>
                                        @else
                                          <span class="badge bg-success">Active</span>
                                        @endif
                                      </td>

                                     
                                      <td>
                                        <a href="{{Route('employees.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                         
                                  
                         <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                         <form id="eea{{$i->id}}" action="{{ route('employees.destroy',$i->id)}}" method="post">
                         @csrf
                         @method('delete')
                         </form>
                                        </td>
                                        <td>
                         @if(!$i->isAdmin())
                         <form action="{{ route('employees.toggle-lock', $i->id) }}" method="post" class="d-inline">
                         @csrf
                         <button type="submit" class="btn {{ $i->is_locked ? 'btn-success' : 'btn-warning' }}" onclick="return confirm('{{ $i->is_locked ? 'Unlock this user login?' : 'Lock this user login?' }}');">
                           {{ $i->is_locked ? 'Unlock Login' : 'Lock Login' }}
                         </button>
                         </form>
                         @endif
                                        </td>
                                  </tr>
                             @endforeach
                        @else
                             <tr>
                                  <td colspan="9">No record found</td>
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
