<div class="container">

    <button class="button mb-2 btn btn-primary" wire:click="generateTodaysalesdetailsPDF">
        <i class="fas fa-file-pdf icon"></i> DOWNLOAD PDF
    </button>
	<div class="card">
		<div class="card-header">
            <a href="{{route('companys.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
             Total No Of Customer 

              <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Here ...." style="width: 250px;" wire:model="searchTerm" >
         </div>
		<div class="card-body">
    <table>
        <thead>
            <tr>
                <th>Id</th>
                <th>Date</th>
                <th>Customer Id</th>
                <th>Customer Name</th>
                <th>Invoice Id</th>
                <th>Particulars</th>
                <th>Invoice Type</th>
                <th>Voucher Type</th>
             
                <th>Debit</th>
                <th>Credit</th>
              
                <th>Notes</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @if (!$all->isEmpty())
                @foreach ($all as $i)
{{-- forcolor --}}
                <tr @if (date('Y-m-d', strtotime($i->date)) === date('Y-m-d')) style="font-weight:bold;color:white;background:red;" @endif>

                        <td data-label="Id">{{ $i->id }}</td>
                        <td data-label="Date">{{ $i->date }}</td>

                        <td data-label="Customer Id">{{ $i->customerid }}</td>
                        <td data-label="Customer Id">{{ $i->cname }}</td>

                        <td data-label="Invoice Id">{{ $i->invoiceid }}</td>
                        <td data-label="Particulars">{{ $i->particulars }}</td>
                        <td data-label="Invoice Type">{{ $i->invoicetype }}</td>

                        <td data-label="Voucher Type">{{ $i->voucher_type }}</td>
                        <td data-label="Debit">{{ $i->debit }}</td>
                        <td data-label="Credit">{{ $i->credit }}</td>
                        <td data-label="Notes">{{ $i->notes }}</td>
                        <td data-label="Created At">{{ $i->created_at }}</td>
                        <td data-label="Created At">{{ $i->updated_at }}</td>

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