<!DOCTYPE html>
<html>
<head>
    <title>Print</title>
    <script src="{{ asset('assets/js/common.js') }}"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0 !important;
            padding: 0 !important;
        }
        * {
            margin-top: 0 !important; /* Set top margin to 0 for all elements */
        }
        .container {
            margin: 0 auto;
            padding: 20px;
            background-color: white;
        }
        .letterhead {
            background-color: black;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .letterhead h1 {
            margin: 0;
            font-size: 24px;
            text-decoration: underline;
        }
        .address-info {
            text-align: center;
            margin-top: 20px;
        }

        .firstdiv{
            float: right;
        }
        .address-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .invoice-info {
            margin-top: 20px;
        }
        .invoice-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000; /* Set border color to black */
            padding: 6px;
        }
        th {
            background-color: white; /* Set background color to white */
        }
        .text-right {
            text-align: right;
        }
        .notes {
            margin-top: 20px;
            max-height: 100px;
            overflow: hidden;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="letterhead">
        <h1>OM HARI TRADELINK</h1>
    </div>

   <div class="address-info" style="font-size: 21px;">
    <p style="font-size: 21px;">Address: Tikapur, Kailali (in front of Tikapur Police Station)</p>
    <p style="font-size: 21px;">Mobile No: 9860378262, 9848448624, 9812656284</p>
</div>

    <div class="invoice-info">

        <div class="container">
   


{{--         
            <form wire:submit.prevent="filterByFirm">
                <div class="row">
                    <div class="col-md-5">
                        <div style="display: flex; align-items: center;">
                            <span>CHOOSE FIRM Name</span>
                            <select wire:model="firm_name" class="form-select @error('firm_name') is-invalid @enderror" id="firm_name" style="border-color: red;">
                                <option value="">Select Firm</option>
                                <option value="Durga">Durga And Dinesh Traders</option>
                                <option value="Malika">Malika & Nav Durga Traders</option>
                                <option value="OmHari">Om Hari Tradelink</option>
                                <option value="Bajgain_Supp">Bajgain Suppliers</option>
                            </select>
                        </div>
                        @error('firm_name')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </form>
             --}}
            <br>
           
        
            <div class="card">
                <!-- Card Content for displaying items -->
                <div class="card-header">
                    Total No Of Items {{ $all->total() }}  
                    {{-- Total Warning Items: {{$warnings}} --}}
                </div>

                
                <div class="card-body overflow-auto">
                    
                    <table class="table">
                        
                        <thead>
                            <tr>
                                <th>Item Id</th>
                                <th>Date</th>
                                <th>Distributor Name</th>
                                <th>Items Name</th>
                                <th>Opening Stock</th>
                                <th>Balance(I+O)</th>
                                <th class=" bg-danger">Quantity (IN) org</th>
                                <th>Check (in)</th>
                                <th>Out</th>
                                <th>Unit</th>
                                <th>Firm Name</th>
                                <th>MRP</th>
                              
                              
                                <th>Show Warning</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($all->count())
                            @foreach ($all as $i)
                            <tr>
                                <td>{{ $i->id }}</td>
                                <td>{{ $i->date }}</td>
                                <td>{{ $i->companyname }}</td>
                                <td>{{ $i->itemsname }}</td>
                                <td>{{ $i->opening_stock }}</td>
                                <td>{{ $i->opening_stock-$sellsquantity_out[$i->id] ?? ''+ $sellsquantity_out[$i->id] ?? '' }}</td>
        
                               
                                <td><button class="btn btn-danger">{{ $i->quantity }} </button></td>
                                
                                <td>{{ $i->opening_stock-$sellsquantity_out[$i->id] ?? '' }}</td>
                                <td>{{ $sellsquantity_out[$i->id] ?? '' }}</td>
        
                                <td>{{ $i->unit }}</td>
                                <td>{{ $i->firm_name }}</td>
                              
        
                                <td>{{ $i->mrp }} &nbsp; &nbsp; <!-- Button trigger modal --></td>
                               
                                    
                             
        
        
                                <td>{{ $i->showwarning }}</td>
                                <td>
                                    @if ($i->quantity <= $i->showwarning  and $i->quantity >= 1)
                                    <div class="span-box">
                                        <span class="btn btn-warning">warning</span>
                                    </div>
                                    @elseif($i->quantity == 0)
                                    <div class="span-box">
                                        <span class="btn btn-danger">outofstock</span>
                                        
                                    </div>
                                    @elseif($i->quantity < 0)
                                    <div class="span-box">
                                        <span class="btn btn-primary">Data in Minus</span>
                                    </div>
                                    
                                    @else
                                    <div class="span-bo">
                                        <span class="btn btn-success">Available</span>
                                    </div>
                                    @endif
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
                </div>
            </div>
        </div>
        
        <script>
        
        document.addEventListener('DOMContentLoaded', function () {
                @foreach($all as $item)
                    var updateForm{{ $item->id }} = document.getElementById('updateForm{{ $item->id }}');
                    updateForm{{ $item->id }}.addEventListener('submit', function (event) {
                        var inputs = this.querySelectorAll('input[type="text"]');
                        var isValid = true;
        
                        inputs.forEach(function (input) {
                            if (!input.value.trim()) {
                                isValid = false;
                            }
                        });
        
                        if (!isValid) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });
                @endforeach
            });
             </script>

</body>
</html>
