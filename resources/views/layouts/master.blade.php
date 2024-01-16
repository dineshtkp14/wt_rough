<!DOCTYPE html>
<html lang="en">

<head>
    <title>WT</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script>
        BASE_URL = "<?php echo url(''); ?>";
    </script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon_white.png') }}" type="image/x-icon">
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
    <link href="http://nepalidatepicker.sajanmaharjan.com.np/nepali.datepicker/css/nepali.datepicker.v4.0.1.min.css"
        rel="stylesheet" type="text/css" />

    {{-- //forhtmltabletoexport --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.6/jspdf.plugin.autotable.min.js"></script>


    {{-- forselectsearch --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery -->

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/sidebars/">


</head>

<body>

    <main class="side-nav d-flex flex-nowrap">
        <h1 class="visually-hidden">Sidebars </h1>


        <div class="flex-shrink-0 p-3" style="width: 280px;">
            <a class="nav-link text-white btn btn-danger p-2 mb-3" href="{{ route('signout') }}"><h4>LOG OUT</h4></a>
            <h6 class="text-white">Hello!!  {{ session('user_email'); }}</h6>

            <a href="/" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none "
                style="border-bottom:1px solid #e5e7eb7e;">
                {{-- <img src="{{ asset('assets/images/logo.png') }}" class="logo-img" alt="logo" style="height: ;"> --}}
            </a>
            <ul class="list-unstyled ps-0">

                <li class="mb-1">
                    <button class=" btn btn-toggle d-inline-flex align-items-center  border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </button>
                    <div class="collapse" id="dashboard-collapse">
                      <a href="/dashaboard">  <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small"> </a>
                            {{-- <li><a href="{{ route('dashboard.index') }}" --}}
                                  {{-- <a  class="link-dark d-inline-flex text-decoration-none rounded">Dashboard</a></li>
                            <li><a href="#"
                                    class="link-dark d-inline-flex text-decoration-none rounded">Weekly</a></li>
                            <li><a href="#"
                                    class="link-dark d-inline-flex text-decoration-none rounded">Monthly</a></li>
                            <li><a href="#"
                                    class="link-dark d-inline-flex text-decoration-none rounded">Annually</a></li> --}}
                       
                                    <li><a href="{{ route('deletedcustomer.deletebillno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded">Searh Deleted Bill</a></li> 
                                </ul>
                    </div>
                </li>
               

                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapse" aria-expanded="false">
                        <i class="fa-solid fa-receipt"></i> Bill 
                    </button>
                    <div class="collapse" id="Invoice-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('itemsales.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Add New 
                                    Bill</a></li>
                            <li><a href="{{ route('itemsales.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    Sales Items</a></li>
                                    <li><a href="{{ route('customer.billno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>Search Bill
                                    </a></li>

                                    <li><a href="{{ route('invoice.index') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View Invoices
                                        </a></li>
                                    
                                        <li><a href="{{ route('deletedcustomer.deletebillno') }}"
                                            class="link-dark d-inline-flex text-decoration-none rounded"><i
                                                class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Search Deleted Bill</a></li>

                        </ul>
                    </div>
                </li>

                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#cnorders-collapse" aria-expanded="false">
                        <i class="fa-solid fa-building-columns"></i> Credit Notes/Sales Return
                    </button>
                    <div class="collapse" id="cnorders-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('creditnotes.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                    Add New Credit Notes</a></li>
                            <li><a href="{{ route('creditnotes.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    View Credit Notes Sales Detail</a></li>

                                    <li><a href="{{ route('creditnotescustomer.billno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                       Search Credit Notes Bill No</a></li>
                                    
                                       <li><a href="{{ route('creditnotescustomer.billno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                      View Invoices</a></li>

                                      <li><a href="{{ route('deletedcncustomer.deletebillno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Search CN Deleted Bill</a></li>


                        </ul>
                    </div>
                </li>



                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#orders-collapse" aria-expanded="false">
                        <i class="fa-solid fa-building-columns"></i> Banks
                    </button>
                    <div class="collapse" id="orders-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('banks.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                    Deposit Amount</a></li>
                            <li><a href="{{ route('banks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    Deposit</a></li>

                        </ul>
                    </div>
                </li>

                {{-- <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#orders-collapse" aria-expanded="false">
                        <i class="fa-solid fa-building-columns"></i> Banks
                    </button>
                    <div class="collapse" id="orders-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('banks.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                    Deposit Amount</a></li>
                            <li><a href="{{ route('banks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    Deposit</a></li>

                        </ul>
                    </div>
                </li> --}}


                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#items-collapse" aria-expanded="false">
                        <i class="fa-solid fa-synagogue"></i> Item
                    </button>
                    <div class="collapse" id="items-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('items.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Add
                                    Items</a></li>
                            <li><a href="{{ route('items.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    Items</a></li>

                        </ul>
                    </div>
                </li>
                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#daybook-collapse" aria-expanded="false">
                        <i class="fa-solid fa-book"></i> Daybook
                    </button>
                    <div class="collapse" id="daybook-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('daybooks.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Add
                                    Amount</a></li>
                            <li><a href="{{ route('daybooks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    Daybook</a></li>

                        </ul>
                    </div>
                </li>
                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#company-collapse" aria-expanded="false">
                        <i class="fa-regular fa-building"></i> Suppliers/Company
                    </button>
                    <div class="collapse" id="company-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('companys.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Add
                                   Add</a></li>
                            <li><a href="{{ route('companys.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                   </a></li>

                                   

                        </ul>
                    </div>
                </li>

                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#companyledger" aria-expanded="false">
                        <i class="fa-sharp fa-solid fa-book"></i> Company Ledger
                    </button>
                    <div class="collapse" id="companyledger">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{route('companybillentry.create') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                             Bill Entry </a></li>

                             <li><a href="{{route('companybillentry.index') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-solid fa-money-check-dollar px-2  d-flex justify-content-center align-items-center"></i>
                               View Bill Entry</a></li>

                              <li><a href="{{route('companyledgerdetails.returnchoosendatehistroy') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-solid fa-money-check-dollar px-2  d-flex justify-content-center align-items-center"></i>
                               View Ledger</a></li>

                               <li><a href="{{route('companyLedgers.create') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-solid fa-money-check-dollar px-2  d-flex justify-content-center align-items-center"></i>
                               Payment</a></li>

                        </ul>
                    </div>
                </li>

                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#customer-collapse" aria-expanded="false">
                        <i class="fa-solid fa-person-military-pointing"></i> Customer
                    </button>
                    <div class="collapse" id="customer-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('customerinfos.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"> <i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Add
                                    New Customer</a></li>
                            <li><a href="{{ route('customerinfos.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-solid fa-eye px-2 d-flex justify-content-center align-items-center"></i>
                                    View Customer</a></li>
                            <li><a href="{{ route('clhs.returnchoosendatehistroy') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    Customer Ledger</a></li>

                                    <li><a href="{{ route('allsalesdetails.showallcuscreditdetails') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                       All Customer Ledger Due List</a></li>

                            <li><a href="{{ route('cpayments.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-solid fa-money-check-dollar  px-2  d-flex justify-content-center align-items-center"></i>Customer
                                    Ledger Payment </a></li>
                                    <li><a href="{{ route('openingbalances.create') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-solid fa-money-check-dollar  px-2  d-flex justify-content-center align-items-center"></i>Opening Balance
                                         </a></li>


                        </ul>
                    </div>
                </li>

                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#price-collapse" aria-expanded="false">
                        <i class="fa-regular fa-money-bill-1"></i> Price List
                    </button>
                    <div class="collapse" id="price-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('pricelists.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>Add
                                    Items Price List </a></li>
                            <li><a href="{{ route('pricelists.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    Items Price List</a></li>

                        </ul>
                    </div>
                </li>
                
                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#stocks" aria-expanded="false">
                        <i class="fa-regular fa-money-bill-1"></i> Stock
                    </button>
                    <div class="collapse" id="stocks">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('stocks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                   View Stocks </a></li>
                            <li><a href=""
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                   Report</a></li>

                        </ul>
                    </div>
                </li>
                <li style="border-bottom:1px solid #e5e7eb7e;"></li>
                <li class="mb-1">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#account-collapse" aria-expanded="false">
                        <i class="fa-solid fa-user"></i> EXTRA
                    </button>
                    <div class="collapse" id="account-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('profit') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded">CALCULATE PROFIT</a></li>
                            <li><a href="{{ route('totalsales.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded">CALCULATE TOTAL SALES</a></li>
                                   
                                    <li><a href="{{ route('allsalesdetails.showdetails') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded">Show TOTAL SALES</a></li>
                                            <li><a href="{{ route('showonlysalesperday.pp') }}"
                                            class="link-dark d-inline-flex text-decoration-none rounded">Show Per Day</a></li>
                                    
                                        
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        
        <div class="game-wrapper d-none">
            <div class="game-modal" id="gameModal">
            </div>
        </div>

    </main>

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

        // function delfunction(id) {
        //     if (confirm("Are You Sure You want to delete")) {
        //         document.getElementById('eea' + id).submit();
        //     }
        // }

        function delfunctionusers(id) {
            if (confirm("Are You Sure You want to delete ?????")) {
                document.getElementById('eea' + id).submit();
            }
        }

        // select input 
        $('#selectCustomerInput').select2();


        //
    </script>


    @php
        $items_data = null;
        if (Request::route()->getName() == 'itemsales.create') {
            $items_data = $data;
        }
    @endphp

    <script>
        var ITEMS_DATA = @json($items_data);
    </script>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/game.js') }}"></script> --}}
</body>

</html>
