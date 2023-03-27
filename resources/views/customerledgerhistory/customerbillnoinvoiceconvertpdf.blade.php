
<!DOCTYPE html>
<html>
<head>
	<title></title>
    <style>
        table, th, td {
            border: 1px solid green;
            border-collapse: collapse;
            padding: 15px;
            
        }
        .floatleft{
            float: right;
        }
        .forunderline{
            text-decoration: underline;
            color: Red;
        }

    </style>
</head>
<body>

<Center><h1 class="text-danger mt-5 bold"><U>OM HARI TRADELINK</U></h1></Center>

<div class="container">
	<div class="row">
       

	</div>

  Invoice Id: {{$invoiceid}}<br>

  @if ($cinfodetails !=null)
  @foreach($cinfodetails as $i)
Name:  {{$i->name}}<br>
Address:  {{$i->address}}<br>
Email:  {{$i->email}}<br>
ContactNo:  {{$i->phoneno}}<br>



    @endforeach
   @endif


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




</div>
</body>
</html>


