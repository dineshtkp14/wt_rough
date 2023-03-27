@extends('layouts.master')

@section('content')
<div class="main-content"> 
<Center><h1 class="text-danger mt-5 bold"><U>VIEW Bill Details By</U></h1></Center>

<div class="container">
	<div class="row">
        <form action="{{route('customer.billno') }}" method="get" id="chosendatepdfform">
 <input type="text" placeholder="Enter Invoice Id" name="invoiceid">
 <input type="submit" class="btn btn-success" value="Search">
        </form>

	</div>

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
           
            <th scope="col">Quantity</th>
            <th scope="col">Price</th>
            <th scope="col">Discount</th>
            <th scope="col">Total</th>
           

          </tr>
        </thead>
        <tbody>
            @if ($allcusbyid !=null)
            @foreach($allcusbyid as $i)
          <tr>
            <td scope="">{{$i->itemid}} </td>
            <td>{{$i->quantity}}</td>
            <td>{{$i->price}}</td>
            <td>{{$i->discount}} </td>
            <td>{{$i->subtotal}}</td>

          </tr>

          @endforeach
          @endif


          @if ($allinvoices !=null)
            @foreach($allinvoices as $i)
      
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
           

           <td>Total Amount :{{$i->subtotal}}</td>
          </tr>
             @endforeach
          @endif
         
       
          
        </tbody>
      </table>
<br>

      @if ($cinfodetails !=null)
      <a href="{{route('invoicebillno.convert')}}" class="btn btn-danger" id="pdfLink">Print To PDF</a>
      
      @endif

</div>


<script>
	document.getElementById('pdfLink').addEventListener('click', function(e) {
        e.preventDefault(); 
		var query=window.location.search;
		var param=new URLSearchParams(query);

        var url = "{{ route('invoicebillno.convert') }}?invoiceid=" + param.get('invoiceid');
		window.location.href = url;

    });
</script>
</div>
@stop