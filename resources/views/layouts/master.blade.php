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
        $nepR = str_replace('\\', '/', public_path('fonts/Hind-Regular.ttf')); // Nepali
        $engR = str_replace('\\', '/', public_path('fonts/NotoSans_Condensed-Regular.ttf')); // English
    @endphp


    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon_white.png') }}" type="image/x-icon">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/modern-sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/modern-stock.css') }}" rel="stylesheet">
    @yield('page-css')

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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@100..900&display=swap"
        rel="stylesheet">

    <link rel="canonical" href="https://getbootstrap.com/docs/5.2/examples/sidebars/">
    <style>
        @font-face {
            font-family: 'HindDevanagari';
            src: url('file://{{ $nepR }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'NotoSansEnglish';
            src: url('file://{{ $engR }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        html body {
            font-family: 'NotoSansEnglish', 'HindDevanagari', sans-serif;
            font-size: 18px;
            line-height: 1.12;
        }

        .nep,
        .label-nep {
            font-family: 'HindDevanagari', sans-serif;
            line-height: 1.14;
        }

        .label-nep {
            padding-left: 3px;
        }

        /* avoids matra clipping */
    </style>

</head>

<body>
    <!-- Modern Sidebar -->
    <aside class="side-nav">
        <h1 class="visually-hidden">Sidebar Navigation</h1>

        <!-- User Header Section -->
        <div class="sidebar-header">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-email">{{ session('user_email') }}</div>
                    <div class="user-role">
                        {{ Auth::check() && Auth::user()->email == 'dineshtkp14@gmail.com' ? 'Administrator' : 'User' }}
                    </div>
                </div>
            </div>
            <a href="{{ route('signout') }}" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Log Out</span>
            </a>
        </div>

        @auth
            <!-- Admin Navigation -->
            @if (Auth::check() && Auth::user()->email == 'dineshtkp14@gmail.com')
                <ul class="nav-menu">
                    <!-- Dashboard -->
                    <li class="nav-item dashboard">
                        <a href="{{ route('dashboard.index') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#dashboard-collapse"
                                aria-expanded="false">
                                <i class="fa-solid fa-gauge"></i>
                                <span>Dashboard</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <!-- Modern Dashboard -->
                    <li class="nav-item dashboard">
                        <a href="{{ route('modern.dashboard') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#modern-dash-collapse"
                                aria-expanded="false">
                                <i class="fa-solid fa-chart-line"></i>
                                <span>Modern Dashboard</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <!-- Check Today -->
                    <li class="nav-item dashboard">
                        <a href="{{ route('checktoday.index') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#checktoday-collapse"
                                aria-expanded="false">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span>Check Today</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <!-- Invoice -->
                    <li class="nav-item invoice">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#Invoice-collapse"
                            aria-expanded="false">
                            <i class="fas fa-file-invoice"></i>
                            <span>Invoice</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="Invoice-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('itemsales.create') }}"><i class="fa-solid fa-plus"></i>Add New
                                        Invoice</a></li>
                                <li><a href="{{ route('customer.billno') }}"><i
                                            class="fa-solid fa-magnifying-glass"></i>Search Invoice</a></li>
                                <li><a href="{{ route('itemsales.index') }}"><i class="fa-solid fa-eye"></i>View Sales
                                        Items</a></li>
                                <li><a href="{{ route('invoice.index') }}"><i class="fa-solid fa-list"></i>View
                                        Invoices</a></li>
                                <li><a href="{{ route('deletedcustomer.deletebillno') }}"><i
                                            class="fa-solid fa-trash-can"></i>Search Deleted Invoice</a></li>
                                <li><a href="{{ route('deleted.invoice') }}"><i class="fa-solid fa-eye"></i>View Deleted
                                        Invoices</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Credit Notes -->
                    <li class="nav-item credit">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#cnorders-collapse"
                            aria-expanded="false">
                            <i class="fas fa-rotate-left"></i>
                            <span>Credit Notes / Sales Return</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="cnorders-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('creditnotes.create') }}"><i class="fa-solid fa-plus"></i>Add New
                                        Credit Note</a></li>
                                <li><a href="{{ route('creditnotescustomer.billno') }}"><i
                                            class="fa-solid fa-magnifying-glass"></i>Search Credit Note</a></li>
                                <li><a href="{{ route('creditnotes.index') }}"><i class="fa-solid fa-eye"></i>View Credit
                                        Note Sales</a></li>
                                <li><a href="{{ route('deletedcncustomer.deletebillno') }}"><i
                                            class="fa-solid fa-trash-can"></i>Search Deleted CN</a></li>
                                <li><a href="{{ route('deletedcn.invoice') }}"><i class="fa-solid fa-eye"></i>View
                                        Deleted CN</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Track -->
                    <li class="nav-item track">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#trackorders-collapse"
                            aria-expanded="false">
                            <i class="fas fa-route"></i>
                            <span>Track</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="trackorders-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('trackinvoice.index') }}"><i
                                            class="fa-solid fa-file-invoice"></i>Track Invoice</a></li>
                                <li><a href="{{ route('trackcreditnotes.index') }}"><i
                                            class="fa-solid fa-rotate-left"></i>Track Credit Notes</a></li>
                                <li><a href="{{ route('trackitemstable.index') }}"><i class="fa-solid fa-box"></i>Track
                                        Items</a></li>
                                <li><a href="{{ route('trackcustomerledger.index') }}"><i
                                            class="fa-solid fa-users"></i>Track Customer Ledger</a></li>
                                <li><a href="{{ route('Trackcompanyledger.index') }}"><i
                                            class="fa-solid fa-building"></i>Track Company Ledger</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Banks -->
                    <li class="nav-item bank">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#orders-collapse"
                            aria-expanded="false">
                            <i class="fas fa-building-columns"></i>
                            <span>Banks</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="orders-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('banks.create') }}"><i class="fa-solid fa-plus"></i>Deposit
                                        Amount</a></li>
                                <li><a href="{{ route('banks.index') }}"><i class="fa-solid fa-eye"></i>View Deposits</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Items -->
                    <li class="nav-item item">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#items-collapse"
                            aria-expanded="false">
                            <i class="fas fa-box"></i>
                            <span>Items</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="items-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('items.create') }}"><i class="fa-solid fa-plus"></i>Add Item</a>
                                </li>
                                <li><a href="{{ route('items.index') }}"><i class="fa-solid fa-eye"></i>View Items</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Daybook -->
                    <li class="nav-item">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#daybook-collapse"
                            aria-expanded="false">
                            <i class="fas fa-book-open"></i>
                            <span>Daybook</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="daybook-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('daybooks.create') }}"><i class="fa-solid fa-plus"></i>Add
                                        Amount</a></li>
                                <li><a href="{{ route('daybooks.index') }}"><i class="fa-solid fa-eye"></i>View
                                        Daybook</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Supplier/Company -->
                    <li class="nav-item company">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#company-collapse"
                            aria-expanded="false">
                            <i class="fas fa-building"></i>
                            <span>Supplier / Company</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="company-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('companys.create') }}"><i class="fa-solid fa-plus"></i>Add
                                        Company</a></li>
                                <li><a href="{{ route('companys.index') }}"><i class="fa-solid fa-eye"></i>View
                                        Companies</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Purchase Order -->
                    <li class="nav-item">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#PUROR-collapse"
                            aria-expanded="false">
                            <i class="fas fa-cart-shopping"></i>
                            <span>Purchase Order</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="PUROR-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('purorder.create') }}"><i class="fa-solid fa-plus"></i>Add Purchase
                                        Order</a></li>
                                <li><a href="{{ route('purorder.index') }}"><i class="fa-solid fa-eye"></i>View
                                        Orders</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Company Ledger -->
                    <li class="nav-item company">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#companyledger"
                            aria-expanded="false">
                            <i class="fas fa-book"></i>
                            <span>Company Ledger</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="companyledger">
                            <ul class="submenu">
                                <li><a href="{{ route('companybillentry.create') }}"><i class="fa-solid fa-plus"></i>Bill
                                        Entry</a></li>
                                <li><a href="{{ route('companybillentry.index') }}"><i class="fa-solid fa-eye"></i>View
                                        Bill Entries</a></li>
                                <li><a href="{{ route('companyledgerdetails.returnchoosendatehistroy') }}"><i
                                            class="fa-solid fa-eye"></i>View Company Ledger</a></li>
                                <li><a href="{{ route('companyLedgerspay.create') }}"><i
                                            class="fa-solid fa-money-bill-wave"></i>Company Payment</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Customer -->
                    <li class="nav-item customer">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#customer-collapse"
                            aria-expanded="false">
                            <i class="fas fa-users"></i>
                            <span>Customer</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="customer-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('customerinfos.create') }}"><i class="fa-solid fa-plus"></i>Add
                                        Customer</a></li>
                                <li><a href="{{ route('customerinfos.index') }}"><i class="fa-solid fa-eye"></i>View
                                        Customers</a></li>
                                <li><a href="{{ route('clhs.returnchoosendatehistroy') }}"><i
                                            class="fa-solid fa-book"></i>Customer Ledger</a></li>
                                <li><a href="{{ route('cashreceipt.search') }}"><i class="fa-solid fa-receipt"></i>Cash
                                        Receipt</a></li>
                                <li><a href="{{ route('allsalesdetails.showallcuscreditdetails') }}"><i
                                            class="fa-solid fa-list-check"></i>Due List</a></li>
                                <li><a href="{{ route('cpayments.create') }}"><i
                                            class="fa-solid fa-money-bill-wave"></i>Payment Entry</a></li>
                                <li><a href="{{ route('openingbalances.create') }}"><i
                                            class="fa-solid fa-scale-balanced"></i>Opening Balance</a></li>
                                <li><a href="{{ route('returnchoosendatehistroycashandcredit') }}"><i
                                            class="fa-solid fa-file-invoice-dollar"></i>Cash/Credit Ledger</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Employee -->
                    <li class="nav-item">
                        <a href="{{ route('employees.index') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#emp"
                                aria-expanded="false">
                                <i class="fas fa-user-tie"></i>
                                <span>View Employee</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <!-- Price List -->
                    <li class="nav-item">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#price-collapse"
                            aria-expanded="false">
                            <i class="fas fa-tags"></i>
                            <span>Price List</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="price-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('pricelists.create') }}"><i class="fa-solid fa-plus"></i>Add
                                        Price</a></li>
                                <li><a href="{{ route('pricelists.index') }}"><i class="fa-solid fa-eye"></i>View
                                        Prices</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Stock -->
                    <li class="nav-item stock">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#stocks"
                            aria-expanded="false">
                            <i class="fas fa-cubes"></i>
                            <span>Stock</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="stocks">
                            <ul class="submenu">
                                <li><a href="{{ route('stocks.index') }}"><i class="fa-solid fa-eye"></i>View Stock</a>
                                </li>
                                <li><a href="{{ route('adminstocks.index') }}"><i class="fa-solid fa-eye"></i>View Admin
                                        Stock</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Expenses -->
                    <li class="nav-item expense">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#expenses"
                            aria-expanded="false">
                            <i class="fas fa-wallet"></i>
                            <span>Expenses</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="expenses">
                            <ul class="submenu">
                                <li><a href="{{ route('expenses.create') }}"><i class="fa-solid fa-plus"></i>Add
                                        Expense</a></li>
                                <li><a href="{{ route('expenses.index') }}"><i class="fa-solid fa-eye"></i>View
                                        Expenses</a></li>
                            </ul>
                        </div>
                    </li>

                    <!-- Divider -->
                    <li class="nav-divider"></li>

                    <!-- Extra -->
                    <li class="nav-item">
                        <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#account-collapse"
                            aria-expanded="false">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                            <span>Extra</span>
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="collapse" id="account-collapse">
                            <ul class="submenu">
                                <li><a href="{{ route('profit') }}"><i class="fa-solid fa-chart-line"></i>Calculate
                                        Profit</a></li>
                                <li><a href="{{ route('totalsales.index') }}"><i class="fa-solid fa-chart-bar"></i>Total
                                        Sales</a></li>
                                <li><a href="{{ route('allsalesdetails.showdetails') }}"><i
                                            class="fa-solid fa-money-bill-wave"></i>Show Sales</a></li>
                                <li><a href="{{ route('showonlysalesperday.pp') }}"><i
                                            class="fa-solid fa-calendar-day"></i>Show Per Day</a></li>
                                <li><a href="{{ route('CheckBankDeposit.index') }}"><i
                                            class="fa-solid fa-building-columns"></i>Check Bank Deposit</a></li>
                                <li><a href="{{ route('CheckCounterDeposit.index') }}"><i
                                            class="fa-solid fa-cash-register"></i>Check Counter Deposit</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>

                <!-- User Navigation (Non-Admin) -->
            @else
                <ul class="nav-menu">
                    <li class="nav-item dashboard">
                        <a href="{{ route('userdash') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-dashboard"
                                aria-expanded="false">
                                <i class="fas fa-gauge-high"></i>
                                <span>Dashboard</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item dashboard">
                        <a href="{{ route('modern.dashboard') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-modern-dash"
                                aria-expanded="false">
                                <i class="fa-solid fa-chart-line"></i>
                                <span>Modern Dashboard</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <!-- Check Today -->
                    <li class="nav-item dashboard">
                        <a href="{{ route('checktoday.index') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-checktoday"
                                aria-expanded="false">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span>Check Today</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item invoice">
                        <a href="{{ route('itemsales.create') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-invoice"
                                aria-expanded="false">
                                <i class="fas fa-file-invoice"></i>
                                <span>Invoice</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item customer">
                        <a href="{{ route('clhs.returnchoosendatehistroy') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-ledger"
                                aria-expanded="false">
                                <i class="fas fa-book"></i>
                                <span>Customer Ledger</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('oldpricecheck') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-price"
                                aria-expanded="false">
                                <i class="fas fa-tags"></i>
                                <span>Check Old Price</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item stock">
                        <a href="{{ route('stocks.index') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-stock"
                                aria-expanded="false">
                                <i class="fas fa-cubes"></i>
                                <span>Check Stock</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item company">
                        <a href="{{ route('companyledgerdetails.returnchoosendatehistroy') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-company"
                                aria-expanded="false">
                                <i class="fas fa-building"></i>
                                <span>Company Ledger</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('showonlysalesperdayinone_table.pp') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-perday"
                                aria-expanded="false">
                                <i class="fas fa-calendar-day"></i>
                                <span>Show Per Day</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>

                    <li class="nav-item credit">
                        <a href="{{ route('creditnotes.create') }}" class="nav-link-single">
                            <button class="nav-toggle" data-bs-toggle="collapse" data-bs-target="#user-cn"
                                aria-expanded="false">
                                <i class="fas fa-rotate-left"></i>
                                <span>Sales Return / Credit Notes</span>
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </a>
                    </li>
                </ul>
            @endif
        @else
            <script>
                window.location = "{{ route('login') }}";
            </script>
        @endauth
    </aside>

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

        function delfunctionusers(id) {
            if (confirm("Are You Sure You want to delete ?????")) {
                document.getElementById('eea' + id).submit();
            }
        }
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

    <script src="{{ asset('assets/js/script.js') }}?v={{ filemtime(public_path('assets/js/script.js')) }}"></script>
    {{-- <script src="{{ asset('assets/js/game.js') }}"></script> --}}

</body>

</html>
