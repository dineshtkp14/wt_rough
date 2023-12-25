@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')

<div class="main-content">
    @yield('breadcrumb')

    <div class="container">
        <div class="row">
            <form action="{{route('customer.billno') }}" method="get" id="chosendatepdfform">
                <Span>Enter Bill No</Span><input type="number" placeholder="Enter Bill No" name="invoiceid" class="form-control w-25 d-inline">
                <input type="submit" class="btn btn-success ox-2" value="Search">
            </form>
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
