<div>
  
    <div class="container">
	

        <div class="row my-4">
            <div class="col-md-6">
                <h4>Total collected cash today : {{$totalsum}}</h4>
        
            </div>
            <div class="col-md-3 flat-end">
            </div>
            <div class="col-md-3 flat-end">
                <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Name ,Id or phoneno" style="width: 250px;" wire:model="searchTerm" >
        
            </div>
        
        </div>
        
        
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact No.</th>
                    <th>Amount</th>
                    <th>Paisa</th>
                    <th>Date</th>
                    <th>Remarks</th>
                    
                </tr>
            </thead>
            <tbody>
            @if ($custo->isNotEmpty())
                                @foreach ($custo as $i)
                <tr>
                    <td data-label="Id">{{ $i->id }}</td>
                    <td data-label="Name">{{ $i->name }}</td>
                    <td data-label="Address">{{ $i->address}}</td>
                    <td data-label="Contact No.">{{ $i->contact }}</td>
                    <td data-label="Amount">{{ $i->amount }}</td>
                    <td data-label="Paisa">{{ $i->modeofpay }}</td>
                    <td data-label="Date">{{ $i->date }}</td>
                    <td data-label="Remarks">{{ $i->remarks }}</td>
                    
                </tr>
                @endforeach
                
            @else
             <h3>Database is Empty !! Plese Add to view List</h3>
                
             @endif
            </tbody>
        </table>
        </div>
</div>
