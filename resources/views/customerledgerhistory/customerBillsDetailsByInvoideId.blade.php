@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content">
    @yield('breadcrumb')

    {{-- <div class="container">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif

</div> --}}


<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Search Invoice</h5>
                    <form action="{{ route('customer.billno') }}" method="get" id="chosendatepdfform">
                        <div class="mb-3">
                            <label for="invoiceid" class="form-label">Enter Bill No</label>
                            <input type="number" class="form-control" id="invoiceid" name="invoiceid" placeholder="Enter Bill No">
                        </div>
                        <button type="submit" class="btn btn-success">Search</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Delete Invoice</h5>
                    @if (Session::has('error'))
                        <div class="alert alert-info">
                            {{ Session::get('error') }}
                        </div>
                    @endif

                    <form action="{{ route('customer.deletebillno') }}" method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <div class="mb-3">
                            <label for="deleteInvoiceId" class="form-label text-danger">Enter Bill No To Delete:</label>
                            <input type="number" class="form-control @error('invoiceid') is-invalid @enderror" id="deleteInvoiceId" name="invoiceid" value="{{ old('invoiceid') }}" placeholder="Enter Bill No">
                            @error('invoiceid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Update Invoice Type</h5>
                    @if (Session::has('updateerror'))
                        <div class="alert alert-info">
                            {{ Session::get('updateerror') }}
                        </div>
                    @endif
                    @if (Session::has('updatesuccess'))
                    <div class="alert alert-success w-50">
                        {{ Session::get('updatesuccess') }}
                    </div>
                @endif

                    <form action="{{ route('customer.updatebillinvoicetype') }}" method="POST" id="updateForm">
                        @csrf
                        @method('put')
                        <div class="mb-3">
                            <label for="updateInvoiceId" class="form-label text-danger">Enter Bill No To Update:</label>
                            <input type="number" value="{{ old('updateinvoiceid') }}" class="form-control @error('updateinvoiceid') is-invalid @enderror" id="updateInvoiceId" name="updateinvoiceid" value="{{ old('updateinvoiceid') }}" placeholder="Enter Bill No">
                            @error('updateinvoiceid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="invoiceType" class="form-label text-danger">Select Invoice Type:</label>
                            <select class="form-select @error('invoicetype') is-invalid @enderror" id="invoiceType" name="invoicetype" required>
                                <option value="check" selected>Please Select Invoice Type</option>
                                <option value="credit">Credit</option>
                                <option value="cash">Cash</option>
                            </select>
                            @error('invoicetype')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


       
          


        <div class="card customer-card mb-4" id="customerCard" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Customer Info</h5>
                <p>
                    <span>ID: </span><span id="customerId">...</span>
                </p>
                <p class="card-text">
                    <span>Name: </span><span id="customerName">...</span>
                </p>
                <p>
                    <span>Address: </span><span id="customerAddress">...</span>
                </p>
                <p>
                    <span>E-mail: </span><span id="customerEmail">...</span>
                </p>
                <p>
                    <span>PhoneNo: </span><span id="customerPhone">...</span>
                </p>
            </div>

            <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
                <i class="fas fa-user"></i>
            </div>
        </div>
        @if(isset($forinvoicetype) && !empty($forinvoicetype))
        <b style="float: right; margin-right: 100px;">Invoice Type: {{ $forinvoicetype->invoicetype }}</b>
       @endif
        @if ($cinfodetails !=null)
            @foreach($cinfodetails as $i)
                Name:  {{$i->name}}<br>
                Address:  {{$i->address}}<br>
                Email:  {{$i->email}}<br>
                ContactNo:  {{$i->phoneno}}<br>
            @endforeach
        @endif

        Invoice Id: {{$invoiceid}} <br>

        @if ($allinvoices !=null)
            @foreach($allinvoices as $i)
                Customer Id: {{$i->customerid}}
            @endforeach
        @endif
        <span class="my-4">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ITEM Name</th>
                        <th scope="col">Original Price</th>
                        <th scope="col">Sold Price</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total</th>
                        <th scope="col">Discount</th>
                        <th scope="col">Sub-Total</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @if ($allcusbyid != null)
                        @foreach($allcusbyid as $i)
                            <tr>
                                <td>{{$i->itemid}}</td>
                                <td>{{$i->mrp}}</td>
                                <td>{{$i->price}}</td>
                                <td>{{$i->quantity}}</td>
                                <td>{{$i->price*$i->quantity}}</td>
                                <td>{{$i->discount}}</td>
                                <td>{{$i->subtotal}}</td>
                                {{-- <td>{{$i->total}}</td> --}}
                            </tr>
                        @endforeach
                    @endif
            
                    @if ($allinvoices != null)
                        @foreach($allinvoices as $i)
                            <tr>
                                 <td colspan="5"></td>
                                 <td class="text-center"><b>Sub-Total:</b></td>
                                <td style="border: none; background-color: #f0f0f0;"><b> {{$i->subtotal}}</b></td>                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-center"><b>Extra Discount: -</b></td>
                                <td style="border: none; background-color: #f0f0f0;"><b> {{$i->discount}}</b></td>
                            </tr>
                            <tr>
                                <td colspan="5">Twelve Lakh Thirty Four Thousand Five Hundred Thirty Two </td>
                                <td class="text-center"><b>Total Amount:</b></td>
                                <td style="border: none; background-color: #f0f0f0;"><b> {{$i->total}}</b></td>
                            </tr>

                            <tr>
                               
                                <td colspan="7">
                                   <b> Notes: {{$i->notes}}</b>
                                 
                                 </td>
            </tr>


            
                           
        </tr>

       
                        @endforeach
                    @endif
                </tbody>
               
                   
               
            </table>
            
            <br>

            <div class="col-12 d-flex justify-content-end align-items-center pt-4">
                <a href="{{route('invoicebillno.convert')}}" class="{{ count($allinvoices) <= 0 ? 'pdf-link-disabled' : '' }}" id="pdfLink">convert To PDF
                    <div class="icon-box d-flex justify-content-center align-items-center">
                        <i class="fa-solid fa-download"></i>
                    </div>
                </a>
            </div>
        </span>
    </div>

    <script>
// JavaScript for Delete Form
document.getElementById('deleteFormSubmitButton').addEventListener('click', function(e) {
    return confirm('Are you sure you want to delete this invoice?') || e.preventDefault();
});

// JavaScript for Update Form
document.getElementById('updateFormSubmitButton').addEventListener('click', function(e) {
    return confirm('Are you sure you want to update this invoice?') || e.preventDefault();
});



        document.getElementById('pdfLink').addEventListener('click', function(e) {
            e.preventDefault();
            var query = window.location.search;
            var param = new URLSearchParams(query);
            var url = "{{ route('invoicebillno.convert') }}?invoiceid=" + param.get('invoiceid');
            window.location.href = url;
        });
    </script>
</div>
@stop
