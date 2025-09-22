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

@php
    // Absolute filesystem paths for Dompdf to embed fonts (REGULAR ONLY)
    $nepR = str_replace('\\','/', public_path('fonts/Hind-Regular.ttf'));                 // Nepali
    $engR = str_replace('\\','/', public_path('fonts/NotoSans_Condensed-Regular.ttf'));  // English
  @endphp

  
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
   

    {{-- //forhtmltabletoexport --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.6/jspdf.plugin.autotable.min.js"></script>


    {{-- forselectsearch --}}
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> --}}
    <!-- jQuery -->

    <!-- Select2 JS -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script> --}}
    <script src="{{ asset('assets/js/common.js') }}"></script>
    <script src="{{ asset('assets/js/nepali-date-converter.umd.js') }}"></script>

    {{-- //nepalifontfor pdf pages --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@100..900&display=swap" rel="stylesheet">
    
    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/sidebars/">
<style>
    @font-face { font-family:'HindDevanagari';  src:url('file://{{ $nepR }}') format('truetype');  font-weight:normal; font-style:normal; }
    @font-face { font-family:'NotoSansEnglish'; src:url('file://{{ $engR }}') format('truetype');  font-weight:normal; font-style:normal; }

html body{
    font-family:'NotoSansEnglish','HindDevanagari',sans-serif;
      font-size:14px; line-height:1.12;
}
</style>

</head>

<body>
   

    <main class="side-nav d-flex flex-nowrap">
        <h1 class="visually-hidden">Sidebars </h1>


        <div class="flex-shrink-0 p-3" style="width: 280px;">
            <a class="nav-link text-white btn btn-danger p-2 mb-3" href="{{ route('signout') }}"><h4>LOG OUT</h4></a>
            <h6 class="text-white">Hello!!  {{ session('user_email'); }}</h6>
            @auth
    <!-- User is authenticated, show content here -->
        {{-- <div class="text-white">Welcome, {{ Auth::user()->name }}</div> --}}
@else
    <!-- User is not authenticated, redirect to login page -->
    <script>window.location = "{{ route('login') }}";</script>
@endauth

            <a href="/" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none "
                style="border-bottom:1px solid #e5e7eb7e;">
                {{-- <img src="{{ asset('assets/images/logo.png') }}" class="logo-img" alt="logo" style="height: ;"> --}}
            </a>
            @if(Auth::check() && Auth::user() && Auth::user()->email == 'dineshtkp14@gmail.com')

        {{-- @if(Auth::user()->email == 'dineshtkp14@gmail.com') --}}
     
            <ul class="list-unstyled ps-0">

                <li class="mb-1 border border-success border-5 
                ">
                  <a href="{{ route('dashboard.index') }}">  <button class=" btn btn-toggle d-inline-flex align-items-center  border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </button>
                  </a>
                    
                </li>
               

                <li class="mb-1 border border-success border-5 ">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapse" aria-expanded="false">
                        <i class="fas fa-file-invoice"></i> INVOICE 
                    </button>
                    <div class="collapse" id="Invoice-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('itemsales.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>ADD NEW 
                                        INVOICE</a></li>

                                        <li><a href="{{ route('customer.billno') }}"
                                            class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>SEARCH INVOICE
                                        </a></li>
                            <li><a href="{{ route('itemsales.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW
                                   SALES ITEMS TABLE</a></li>
                                    

                                    <li><a href="{{ route('invoice.index') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW INVOICES TABLE
                                        </a></li>
                                    
                                        <li><a href="{{ route('deletedcustomer.deletebillno') }}"
                                            class="link-dark d-inline-flex text-decoration-none rounded"><i
                                                class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>SEARCH DELETED INVOICE</a></li>
                                               
                                                <li><a href="{{ route('deleted.invoice') }}"
                                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW DELETED INVOICE TABLE</a></li>
        
                        </ul>
                    </div>
                </li>

                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#cnorders-collapse" aria-expanded="false">
                        <i class="fas fa-file-alt"></i>CREDIT NOTES / SALES RETURN
                    </button>
                    <div class="collapse" id="cnorders-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('creditnotes.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                    ADD NEW CREDIT NOTES</a></li>

                                    <li><a href="{{ route('creditnotescustomer.billno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                      SEARCH CREDIT NOTES INVOICE</a></li>

                            <li><a href="{{ route('creditnotes.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                    VIEW CREDIT NOTES SALES DETAIL TABLE</a></li>

                              
                                    
                                       <li><a href="{{ route('creditnotescustomer.billno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                    VIEW CREDIT NOTES INVOICES TABLE</a></li>

                                      <li><a href="{{ route('deletedcncustomer.deletebillno') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>SEARCH CN DELETED BILL</a></li>

                                            <li><a href="{{ route('deletedcn.invoice') }}"
                                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                                    class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW DELETED CN INVOICE</a></li>
    


                        </ul>
                    </div>
                </li>



                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#trackorders-collapse" aria-expanded="false">
                        <i class="fas fa-university"></i> Track
                    </button>
                    <div class="collapse" id="trackorders-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('trackinvoice.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                   Track Invoice</a></li>
                            <li><a href="{{ route('trackcreditnotes.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                   Track Credit Notes</a></li>

                                   <li><a href="{{ route('trackitemstable.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                   Track Items</a></li>

                                   <li><a href="{{ route('trackcustomerledger.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                   Track CustomerLedger Payment</a></li>

                                   <li><a href="{{ route('Trackcompanyledger.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                   Track Company Ledger</a></li>

                                 

                        </ul>
                    </div>
                </li>


                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#orders-collapse" aria-expanded="false">
                        <i class="fas fa-university"></i> Banks
                    </button>
                    <div class="collapse" id="orders-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('banks.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                    DEPOSIT AMOUNT</a></li>
                            <li><a href="{{ route('banks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW
                                    DEPOSIT TABLE</a></li>

                        </ul>
                    </div>
                </li>

               


                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#items-collapse" aria-expanded="false">
                        <i class="fas fa-box"></i> ITEM
                    </button>
                    <div class="collapse" id="items-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('items.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>ADD
                                    ITEM</a></li>
                            <li><a href="{{ route('items.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW
                                    ITEMS TABLE</a></li>

                        </ul>
                    </div>
                </li>
                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#daybook-collapse" aria-expanded="false">
                        <i class="fas fa-calendar-day"></i> DAYBOOK
                    </button>
                    <div class="collapse" id="daybook-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('daybooks.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>ADD
                                    AMOUNT</a></li>
                            <li><a href="{{ route('daybooks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW
                                    DAYBOOK TABLE</a></li>

                        </ul>
                    </div>
                </li>

                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#company-collapse" aria-expanded="false">
                        <i class="fas fa-building"></i> SUPPLIER/COMPANY
                    </button>
                    <div class="collapse" id="company-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('companys.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>ADD NEW COMPANY
                                   </a></li>
                            <li><a href="{{ route('companys.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW COMPANY DETAILS TABLE
                                   </a></li>

                                   

                        </ul>
                    </div>
                </li>



                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#PUROR-collapse" aria-expanded="false">
                        <i class="fas fa-shopping-cart"></i> PURCHASE ORDER
                    </button>
                    <div class="collapse" id="PUROR-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('purorder.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>ADD NEW PURCHASE ORDER
                                   </a></li>
                            <li><a href="{{ route('purorder.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW PURCHASE ORDER TABLE
                                   </a></li>

                                   

                        </ul>
                    </div>
                </li>




                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#companyledger" aria-expanded="false">
                        <i class="fas fa-book"></i> COMPANY LEDGER
                    </button>
                    <div class="collapse" id="companyledger">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{route('companybillentry.create') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                             BILL ENTRY </a></li>

                             <li><a href="{{route('companybillentry.index') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-solid fa-money-check-dollar px-2  d-flex justify-content-center align-items-center"></i>
                               VIEW BILL ENTRY TABLE</a></li>

                              <li><a href="{{route('companyledgerdetails.returnchoosendatehistroy') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-solid fa-money-check-dollar px-2  d-flex justify-content-center align-items-center"></i>
                               VIEW COMPANY LEDGER</a></li>

                               <li><a href="{{route('companyLedgerspay.create') }}"
                                class="link-dark d-inline-flex text-decoration-none rounded"><i
                                    class="fa-solid fa-money-check-dollar px-2  d-flex justify-content-center align-items-center"></i>
                               COMPANY PAYMENENT</a></li>

                        </ul>
                    </div>
                </li>

                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#customer-collapse" aria-expanded="false">
                        <i class="fas fa-users"></i> CUSTOMER
                    </button>
                    <div class="collapse" id="customer-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('customerinfos.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"> <i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>ADD NEW CUSTOMER
                                   </a></li>
                            <li><a href="{{ route('customerinfos.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-solid fa-eye px-2 d-flex justify-content-center align-items-center"></i>
                                    VIEW CUSTOMER TABLE</a></li>
                            <li><a href="{{ route('clhs.returnchoosendatehistroy') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                    CUSTOMER LEDGER</a></li>


                                    <li><a href="{{ route('cashreceipt.search') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                       SEARCH CUSTOMER PAYMENT CASH RECEIPT </a></li>

                                    <li><a href="{{ route('allsalesdetails.showallcuscreditdetails') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>View
                                       ALL CUSTOMER LEDGER DUE LIST</a></li>

                            <li><a href="{{ route('cpayments.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-solid fa-money-check-dollar  px-2  d-flex justify-content-center align-items-center"></i>CUSTOMER LEDGER PAYMENT
                                </a></li>
                                    <li><a href="{{ route('openingbalances.create') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i
                                            class="fa-solid fa-money-check-dollar  px-2  d-flex justify-content-center align-items-center"></i>OPENING BALANCE
                                         </a></li>


                                         <li><a href="{{ route('returnchoosendatehistroycashandcredit') }}"
                                            class="link-dark d-inline-flex text-decoration-none rounded"><i
                                                class="fa-solid fa-money-check-dollar  px-2  d-flex justify-content-center align-items-center"></i>CASH/CREDIT LEDGER
                                             </a></li>


                                         


                        </ul>
                    </div>
                </li>

               

                <li class="mb-1 border border-success border-5">
                  <a href="{{ route('employees.index') }}">  <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#emp" aria-expanded="false">
                        <i class="fas fa-list-alt"></i> VIEW EMPLOYEE
                    </button>
                  </a>
                </li>

                 <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#price-collapse" aria-expanded="false">
                        <i class="fas fa-list-alt"></i> PRICE LIST
                    </button>
                    <div class="collapse" id="price-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('pricelists.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                    ADD ITEMS PRICE </a></li>
                            <li><a href="{{ route('pricelists.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>VIEW ITEM PRICE LIST TABLE
                                    </a></li>

                        </ul>
                    </div>
                </li>
                
                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#stocks" aria-expanded="false">
                        <i class="fas fa-cubes"></i> STOCK
                    </button>
                    <div class="collapse" id="stocks">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('stocks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                   VIEW STOCK </a></li>

                                   <li><a href="{{ route('adminstocks.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                   VIEW ADMIN STOCK </a></li>
                            

                        </ul>
                    </div>
                </li>

                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#expenses" aria-expanded="false">
                        <i class="fas fa-cubes"></i> Expenses
                    </button>
                    <div class="collapse" id="expenses">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('expenses.create') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-plus px-2  d-flex justify-content-center align-items-center"></i>
                                   ADD EXPENSES </a></li>



                                   <li><a href="{{ route('expenses.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i
                                        class="fa-sharp fa-solid fa-eye px-2  d-flex justify-content-center align-items-center"></i>
                                   VIEW EXPENSES </a></li>


                                 
                            

                        </ul>
                    </div>
                </li>


                <li style="border-bottom:1px solid #e5e7eb7e;"></li>
                <li class="mb-1 border border-success border-5">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#account-collapse" aria-expanded="false">
                        <i class="fa-solid fa-user"></i> EXTRA
                    </button>
                    <div class="collapse" id="account-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li><a href="{{ route('profit') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded "><i class="fas fa-chart-line"></i> CALCULATE PROFIT</a></li>
                            <li><a href="{{ route('totalsales.index') }}"
                                    class="link-dark d-inline-flex text-decoration-none rounded"><i class="fas fa-chart-bar"></i>  CALCULATE TOTAL SALES</a></li>
                                   
                                    <li><a href="{{ route('allsalesdetails.showdetails') }}"
                                        class="link-dark d-inline-flex text-decoration-none rounded"><i class="fas fa-money-bill-wave"></i>  SHOW TOTAL SALES</a></li>
                                            <li><a href="{{ route('showonlysalesperday.pp') }}"
                                            class="link-dark d-inline-flex text-decoration-none rounded"><i class="fas fa-calendar"></i> SHOW PER DAY</a></li>
                                    
                                            <li><a href="{{ route('CheckBankDeposit.index') }}"
                                                class="link-dark d-inline-flex text-decoration-none rounded"><i class="fas fa-calendar"></i> Check Bank Deposit</a></li>
                                        

                                                <li><a href="{{ route('CheckCounterDeposit.index') }}"
                                                    class="link-dark d-inline-flex text-decoration-none rounded"><i class="fas fa-calendar"></i> Check Counter Deposit</a></li>
                                            
                        </ul>
                    </div>
                </li>
            </ul>
        
        
        {{-- foruseronlynav --}}

        @else

        <ul class="list-unstyled ps-0">
            <li class="mb-1 border border-success border-5 bg-dark">
                <a href="{{ route('userdash') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </button>
                </a>
            </li>
        
            <li class="mb-1 border border-warning border-5 bg-dark ">
                <a href="{{ route('itemsales.create') }}" style="text-decoration:none;" class="text-warning">
                    <button class="text-warning btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-file-invoice"></i><b> Invoice</b>
                    </button>
                </a>
            </li>
        
           
        
            <!-- Add icons to the remaining menu items -->
            <li class="mb-1 border border-success border-5">
                <a href="{{ route('returnchoosendatehistroycashandcredit') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-book"></i>  Customer Ledger
                    </button>
                </a>
            </li>
        

            <li class="mb-1 border border-success border-5">
                <a href="{{ route('oldpricecheck') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-rupee-sign"></i>  Check Old Price
                    </button>
                </a>
            </li>

            <li class="mb-1 border border-success border-5">
                <a href="{{ route('stocks.index') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-cube"></i> Check Stock
                    </button>
                </a>
            </li>
        
            <li class="mb-1 border border-success border-5">
                <a href="{{ route('companyledgerdetails.returnchoosendatehistroy') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-bookmark"></i>  Company Ledger
                    </button>
                </a>
            </li>
        
            <li class="mb-1 border border-success border-5">
                <a href="{{ route('showonlysalesperdayinone_table.pp') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-calendar-day"></i> Show Per Day
                    </button>
                </a>
            </li>

            <li class="mb-1 border border-success border-5">
                <a href="{{ route('creditnotes.create') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-file-invoice"></i> Sales Return / Credit Notes
                    </button>
                </a>
            </li>
        
            {{-- <li class="mb-1 border border-success border-5">
                <a href="{{ route('banks.create') }}" style="text-decoration:none;" class="text-white">
                    <button class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed"
                        data-bs-toggle="collapse" data-bs-target="#Invoice-collapsee" aria-expanded="false">
                        <i class="fas fa-piggy-bank"></i> Bank Deposit
                    </button>
                </a>
            </li> --}}

            
        </ul>
        

        @endif

        
        
        
        
        
        
        
        
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

        // select input ok
        // $('#selectCustomerInput').select2();


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


        //forsidenavbarcollpse
        $(document).ready(function() {
            // Toggle side nav bar when button is clicked
            $('#toggleSidebarBtn').click(function() {
                $('.side-nav').toggleClass('collapsed');
            });
        });
    </script>

    <script src="{{ asset('assets/js/script.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/game.js') }}"></script> --}}



    {{-- //for date converter --}}
    
    
</body>

</html>
