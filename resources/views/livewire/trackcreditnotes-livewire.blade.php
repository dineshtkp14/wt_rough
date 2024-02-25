<div class="container">
    <div class="card ">
         <div class="card-header">
            <a href="{{route('customerinfos.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total  {{ $all->total() }} 

              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search here" style="width: 250px;" wire:model="searchTerm" >
         </div>
         <div class="card-body">
              <table class="table text-center">
                   <thead>
                        <tr>
                            <th >Id </th>
                            <th>Credit Notes Bill No</th>
                            <th class="">Title</th>
                            <th >Created At</th>
                            <th class="w-50" >Notes</th>
                           

                        </tr>
                   </thead>
                   <tbody class="text-center">
                    @if ($all->count())
                             @foreach ($all as $i)
                                  <tr>
                                     <td class="text-center">{{ $i->id }}</td>
                                      <td class="text-center">{{ $i->Cn_bill_no }}</td>
                                      <td class="text-center">{{ $i->title }}</td>
                                      <td class="text-center">{{ $i->created_at }}</td>
                                    
                                      <td class="text-center w-50">{{ $i->notes }}</td>
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
