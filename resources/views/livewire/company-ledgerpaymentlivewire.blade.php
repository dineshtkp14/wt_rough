<div>
    {{-- If your happiness depends on money, you will never be happy with yourself. --}}

<div class="container">
    <div class="card">
        <div class="card-header">
            <a href="{{route('companyLedgerspay.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer 

             <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Here" style="width: 250px;" wire:model="searchTerm" >
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Company ID</th>
                            <th>Company Name</th>

                            <th>Particulars</th>
							<th>Voucher Type</th>
                            <th>Date</th>
                            <th>Debit</th>
                            <th>Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($all->isNotEmpty())
                            @foreach ($all as $i)
                                @php
                                    $todayDate = now()->format('Y-m-d');
                                @endphp
                                <tr>
                                    <td data-label="Id">{{ $i->id }}</td>
                                    <td data-label="Name">{{ $i->companyid }}</td>
                                    <td data-label="Name">{{ $i->companyname }}</td>

                                    <td data-label="Address">{{ $i->particulars}}</td>
                                    <td data-label="Contact No.">{{ $i->voucher_type }}</td>
                                    <td data-label="Contact No.">{{ $i->date }}</td>
                                    <td data-label="Contact No.">{{ $i->debit }}</td>
                                    <td data-label="Remarks">{{ $i->notes }}</td>
                                    <td data-label="action">
                                        @if (auth()->user()->email != 'dineshtkp14@gmail.com')

                                        @if (Session::has('success') && $i->id == session('lastInsertedId'))
                                                @if ($todayDate == $i->date)
                                                    <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                                                    <form id="eea{{$i->id}}" action="{{ route('companyLedgerspay.destroy',$i->id)}}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                    </form>
                                                @endif

                                            @endif                                               

                                                @else
                                                <a href="{{Route('companyLedgerspay.edit',$i->id)}}" class="btn "  rel="noopener noreferrer" style="background:#389AF5;color:white;">EDIT</a>
                                                <a href="#" onclick="delfunctionusers({{$i->id}})" class="btn btn-danger"  rel="noopener noreferrer">Delete</a>
                                                <form id="eea{{$i->id}}" action="{{ route('companyLedgerspay.destroy',$i->id)}}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                </form>
       
                                               @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9"><h3>No Record Found !!</h3></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted">
            {{ $all->links() }}
        </div>
    </div>
</div>
</div>