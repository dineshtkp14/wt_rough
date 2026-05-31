{{-- itemssales/perday.blade.php --}}
@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content legacy-sales-page"> 

    @yield('breadcrumb')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('items.create') }}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
                Total No Of Items 

                <a href="{{ route('invoice.index') }}" class="btn btn-dark border border-4 border-danger ms-5">
                    <i class="fas fa-file-invoice"></i> VIEW ALL INVOICES
                </a>
                
                <a href="{{ route('allsalesdetails.showdetails') }}" class="btn btn-dark border border-4 border-danger ms-3">
                    <i class="fas fa-list-alt mx-2"></i>VIEW ALL SALES DETAILS
                </a>

                <a href="{{ route('itemsales.index') }}" class="btn btn-dark border border-4 border-danger ms-3">
                    <i class="fas fa-list-alt mx-2"></i>VIEW ALL SALES ITEMS
                </a>
                
                
                <a href="{{ route('showonlysalesperdayinone_table.pp') }}" class="btn btn-primary border border-5 border-warning float-end me-2">
                    <i class="fas fa-check"></i> Check Counter
                </a>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col">
                        <table class="table h5" style="width: 200px">
                            <thead>

                                <center>  <SPAN class="h5 btn btn-warning fw-bold"> CREDIT SALES</SPAN> </center>

                               

                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDayCredit as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- links --}}
                        {{ $salesPerDaycrnotes->links() }}

                    </div>


                    <div class="col">
                        <table class="table h5" style="width: 200px">
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold"> CASH SALES</SPAN> </center>

                               

                                <tr>
                                    <th>Date</th>
                                    <th>Amount </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDayCash as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDaycrnotes->links() }}

                    </div>





                    <div class="col">
                        <table class="table h5" style="width: 200px">
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold"> TOTAL SALES PER DAY </SPAN> </center>

                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDay as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDay->links() }}

                    </div>

                    <div class="col">
                        <table class="table h5" style="width: 200px">
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold">CREDIT NOTES/SALES RETURN
                                </SPAN> </center>


                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDaycrnotes as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDaycrnotes->links() }}

                    </div>


                  
                    
                    <div class="col">
                        <table class="table h5" style="width: 200px">
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold">  LEDGER PAYMENT</SPAN> </center>

                          
                                <tr>
                                    <th>Date</th>
                                    <th>Amount </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment as $pay)
                                <tr @if(now()->format('Y-m-d') == $pay->date) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $pay->date }}</td>
                                    <td> <span class="">{{ $pay->total }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDaycrnotes->links() }}

                    </div>



                        
                    {{-- <div class="col">
                        <table class="table h5 " style="width: 400px">
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold">Counter Cash</SPAN> </center>

                        
                                <tr>
                                    <th>Date</th>
                                    <th>Total </th>
                                    <th>Counter Check </th>
                                    <th>Bank Deposit  Check</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                            usort($totalSalesAndPayments, function($a, $b) {
                                return strtotime($a['date']) - strtotime($b['date']);
                            });
                            @endphp

                          @foreach($totalSalesAndPayments as $data)
                                <tr @if(now()->format('Y-m-d') == $data['date']) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $data['date'] }}</td>
                                    <td>{{ $data['total'] }}</td>

                                    <td>
                                        <?php if ($data['counter_deposit'] == 'yes'): ?>
                                            &#10004; 
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($data['bank_deposit'] == 'yes'): ?>
                                            &#10004;
                                        <?php endif; ?>
                                    </td>

                                   
                                    
                                </tr>
                            @endforeach
                            
                            </tbody>
                        </table>
                        {{ $salesPerDaycrnotes->links() }}

                    </div> --}}


                    
                </div>
            </div>
           
        </div>
    </div>

</div>
<style>
    .legacy-sales-page .card {
        border: 1px solid rgba(0, 0, 0, .125) !important;
        border-radius: .375rem !important;
        box-shadow: none !important;
        overflow: visible !important;
    }

    .legacy-sales-page .card-header {
        display: block !important;
        padding: .5rem 1rem !important;
        background: rgba(0, 0, 0, .03) !important;
        border-bottom: 1px solid rgba(0, 0, 0, .125) !important;
        color: inherit !important;
        font-weight: inherit !important;
    }

    .legacy-sales-page .card-body {
        overflow: visible !important;
        padding: 1rem !important;
        background: #ffffff !important;
    }

    .legacy-sales-page table,
    .legacy-sales-page table.table {
        width: 200px !important;
        min-width: 0 !important;
        margin-bottom: 1rem !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
        table-layout: auto !important;
        color: inherit !important;
        --bs-table-bg: transparent !important;
        --bs-table-color: inherit !important;
        --bs-table-striped-bg: transparent !important;
        --bs-table-striped-color: inherit !important;
        --bs-table-hover-bg: transparent !important;
        --bs-table-hover-color: inherit !important;
    }

    .legacy-sales-page table thead,
    .legacy-sales-page table tbody,
    .legacy-sales-page table tr {
        display: revert !important;
        width: auto !important;
        height: auto !important;
        overflow: visible !important;
    }

    .legacy-sales-page table th,
    .legacy-sales-page table td,
    .legacy-sales-page table.table th,
    .legacy-sales-page table.table td {
        display: table-cell !important;
        padding: .5rem .5rem !important;
        border-color: #dee2e6 !important;
        background: transparent !important;
        color: inherit !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        text-align: inherit !important;
        text-transform: none !important;
        white-space: normal !important;
        vertical-align: top !important;
        letter-spacing: normal !important;
    }

    .legacy-sales-page table th,
    .legacy-sales-page table.table th {
        position: static !important;
        top: auto !important;
        z-index: auto !important;
        background: transparent !important;
        color: inherit !important;
        font-weight: 700 !important;
    }

    .legacy-sales-page table tbody tr:nth-child(even) td,
    .legacy-sales-page table.table tbody tr:nth-child(even) td,
    .legacy-sales-page table tbody tr:hover td,
    .legacy-sales-page table.table tbody tr:hover td {
        background: transparent !important;
    }

    .legacy-sales-page table tbody tr[style*="background:red"] td {
        background: red !important;
        color: #ffffff !important;
        font-weight: bold;
    }
</style>
@endsection
