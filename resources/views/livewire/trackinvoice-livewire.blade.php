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
                            <th  style="width:7%;">Id </th>
                            <th style="width:7%;">Bill No</th>
                            <th style="width:18%;" >Title</th>
                            <th style="width:25%;">Created At</th>
                           
                            <th >Notes</th>
                           

                        </tr>
                   </thead>
                   <tbody class="text-center">
                    @if ($all->count())
                             @foreach ($all as $i)
                             <tr @if (date('Y-m-d', strtotime($i->created_at)) === date('Y-m-d')) style="font-weight:bold;" @endif>
                                     <td class="text-center" style="width:7%">{{ $i->id }}</td>
                                      <td class="text-center" style="width:7%;">{{ $i->bill_no }}</td>
                                      <td class="text-center" style="width:18%;">{{ $i->title }}</td>
                                      <td class="text-center" style="width:25%;">{{ $i->created_at }}</td>
                                    
                                      <td >{{ $i->notes }}</td>
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
