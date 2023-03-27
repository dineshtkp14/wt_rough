<div class="container">
     <div class="card text-center">
          <div class="card-header">
              Total No Of Customer {{ $all->total() }}

               <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name ,Id or phoneno" style="width: 250px;" wire:model="searchTerm" >
          </div>
          <div class="card-body">
               <table class="table">
                    <thead>
                         <tr>
                             <th class="sort" wire:click="sortOrder('id')">Id {!! $sortLink !!}</th>
                             <th class="sort" wire:click="sortOrder('name')">Name {!! $sortLink !!}</th>
                             <th >Address</th>
                             <th >Email</th>
                             <th >Phoneno</th>
                             <th>Remarks</th>
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
                                       <td>{{ $i->remarks}}</td>
                                       <td>
                                         <a href="{{Route('customerinfos.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                          
                                   
                          <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                          <form id="eea{{$i->id}}" action="{{ route('customerinfos.destroy',$i->id)}}" method="post">
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
