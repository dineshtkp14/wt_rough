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
        <div class="card">
            <div class="card-header">
                {{-- <a href="{{route('companys.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a> --}}
                 Today Sales list
    
                  <input type="text" class="form-control float-end  border-warning border border-5" placeholder="Search Here ...." style="width: 250px;" wire:model="searchTerm" >
             </div>
            <div class="card-body">
        <table>
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>Date</th>

                    {{-- <th>Id</th> --}}
                    <th>Customer Id</th>
                    <th>Customer Name</th>
                    <th>Invoice Id</th>
                    <th>Particulars</th>
                    <th>Voucher Type</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Invoice Type</th>
                    <th>Notes</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @if (!$all->isEmpty())
                @foreach ($all as $index => $i)    
                    <tr @if (date('Y-m-d', strtotime($i->date)) === date('Y-m-d')) style="font-weight:bold;" @endif>
                        <td data-label="S.N">{{ $index + 1 }}</td>
                        <td data-label="Date">{{ $i->date }}</td>

                            {{-- <td data-label="Id">{{ $i->id }}</td> --}}
                            <td data-label="Customer Id">{{ $i->customerid }}</td>
                            <td data-label="Customer Id">{{ $i->cname }}</td>
    
                            <td data-label="Invoice Id">{{ $i->invoiceid }}</td>
                            <td data-label="Particulars">{{ $i->particulars }}</td>
                            <td data-label="Voucher Type">{{ $i->voucher_type }}</td>
                            <td data-label="Debit">{{ $i->debit }}</td>
                            <td data-label="Credit">{{ $i->credit }}</td>
                            <td data-label="Invoice Type">{{ $i->invoicetype }}</td>
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
        {{-- {{ $all->links() }} --}}
    </div>
    </div>
    </div>
    
          

</body>
</html>
