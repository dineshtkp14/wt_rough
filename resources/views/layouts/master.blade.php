<!DOCTYPE html>
<html lang="en">

<head>
    <title>WT</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/number-to-words"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>



    {{-- forselectsearch --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery -->

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top  navbar-dark bg-dark">
        <div class="container-fluid">
          <a class="navbar-brand" href="">Wholesale Tikapur</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    BANKS
                  </a>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('banks.create')}}">Add Deposit</a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{route('banks.index')}}">View Deposit</a></li>
                  </ul>
              </li>
             
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Daybook
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('daybooks.create')}}">Add Daybook</a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{route('daybooks.index')}}">View Daybook</a></li>
                  </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Suppliers
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('disinfos.create')}}">Add New Suppliers</a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{route('disinfos.index')}}">View Suppliers Details</a></li>
                  </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Products
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('daybooks.create')}}">Add Product</a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{route('daybooks.index')}}">View Product Details</a></li>
                  </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Customers
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('customerinfos.create')}}">Add Customer</a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{route('customerinfos.index')}}">View Customers Details</a></li>
                  </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  PRODUCT
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('items.create')}}">ADD Product</a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{route('items.index')}}">View  Product Details</a></li>
                  </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Sell Products
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('itemsales.create')}}">Sell Product</a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{route('itemsales.index')}}">View  sales Product Details</a></li>
                  </ul>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Price List
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="">Add Product Price </a></li>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="">View Product Price</a></li>
                  </ul>
              </li>
            </ul>
            
            <form class="d-flex" role="search">
              <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
          </div>
        </div>
      </nav>
    @yield('content')
    
    <script>
        $(document).ready(function() {
            $("#filterInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
        $(document).ready(function() {
            $(".alert").fadeTo(2000, 500).slideUp(500, function() {
                $(".alert").slideUp(500);
            });

        });

        function delfunction(id) {
            if (confirm("Are You Sure You want to delete")) {
                document.getElementById('eea' + id).submit();
            }
        }

        function delfunctionusers(id) {
            if (confirm("Are You Sure You want to delete")) {
                document.getElementById('eea' + id).submit();
            }
        }

        // select input 
        $('#selectCustomerInput').select2();


        //
        
    </script>


@php
$items_data = null;
   if(Request::route()->getName() =="itemsales.create"){
        $items_data = $data;
   }
@endphp

<script>
    var ITEMS_DATA= @json($items_data);
</script>

<script src="{{ asset('assets/js/script.js') }}"></script>
</body>

</html>
