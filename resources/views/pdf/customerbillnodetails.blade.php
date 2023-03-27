@extends('layouts.master')

@section('content')

<Center><h1 class="text-danger mt-5 bold"><U>VIEW Bill Details By</U></h1></Center>

<div class="container">
	<div class="row">
        <form action="{{route('customer.billno') }}" method="get" id="chosendatepdfform">
 <input type="text" placeholder="Enter Invoice Id" name="invoiceid">
 <input type="submit" class="btn btn-success" value="Search">
        </form>

	</div>

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
            <td></td>

           <td>Total Amount :{{$i->subtotal}}</td>
          </tr>
             @endforeach
          @endif
         
       
          
        </tbody>
      </table>




</div>



@stop