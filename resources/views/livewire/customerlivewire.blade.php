<div class="container">
    <div class="card ">
         <div class="card-header">
            <a href="{{route('customerinfos.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer {{ $all->total() }}         <a href="{{ route('itemsales.create') }}" class="btn btn-primary ms-5" style="background-color: #FF0066; border-color: #0be813; color: white; transition: background-color 0.3s, border-color 0.3s;"> <i class="fas fa-file-invoice"></i> ADD NEW INVOICE</a>


              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name, phoneno, email" style="width: 250px;" wire:model="searchTerm" >
         </div>
         <div class="card-body">
              <table class="table text-center">
                   <thead>
                        <tr>
                            <th >Customer Id </th>
                            <th>Name</th>
                            <th >Address</th>
                            <th >Email</th>
                            <th >Phoneno</th>
                            <th>Remarks</th>
                            <th>Added By</th>
                            <th>Created_at</th>
                            <th>Updated_at</th>

                            <th style="width: 160px;" >Action</th>


                        </tr>
                   </thead>
                   <tbody class="text-center">
                    @if ($all->count())
                             @foreach ($all as $i)
                                  <tr>
                                     <td class="text-center">{{ $i->id }}</td>
                                      <td class="text-center">{{ $i->name }}</td>
                                      <td class="text-center">{{ $i->address }}</td>
                                      <td class="text-center">{{ $i->email }}</td>
                                      <td class="text-center">{!! $i->phoneno . ' <b>,</b> ' . $i->alternate_phoneno !!}</td>
                                      <td class="text-center">{{ $i->remarks}}</td>
                                      <td>{{ $i->added_by}}</td>
                                      <td>{{ $i->created_at}}</td>
                                      <td>{{ $i->updated_at}}</td>



                                        
                                        <td style="width: 160px;" class="text-center">

                                             @if (auth()->user()->email != 'dineshtkp14@gmail.com')
                                                 @if (Session::has('success') && $i->id == session('lastInsertedId'))
                                                     <!-- Actions for all users except dineshtkp14@gmail.com if session success and last inserted id match -->
                                                     <a href="{{ Route('customerinfos.edit', $i->id) }}" class="btn" rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                                                     <a href="#" onclick="delfunctionusers({{ $i->id }})" class="btn btn-danger" rel="noopener noreferrer">Delete</a>
                                                     <form id="eea{{ $i->id }}" action="{{ route('customerinfos.destroy', $i->id) }}" method="post">
                                                         @csrf
                                                         @method('delete')
                                                     </form>
                                                 @endif
                                             @else
                                                 <!-- Actions for dineshtkp14@gmail.com -->
                                                 <a href="{{ Route('customerinfos.edit', $i->id) }}" class="btn" rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                                                 <a href="#" onclick="delfunctionusers({{ $i->id }})" class="btn btn-danger" rel="noopener noreferrer">Delete</a>
                                                 <form id="eea{{ $i->id }}" action="{{ route('customerinfos.destroy', $i->id) }}" method="post">
                                                     @csrf
                                                     @method('delete')
                                                 </form>
                                             @endif
                                             
                                             </td>
                                             
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
