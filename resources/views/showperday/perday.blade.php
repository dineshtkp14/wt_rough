{{-- itemssales/perday.blade.php --}}
@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

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

                <a href="{{ route('allsalesdetails.showdetails') }}" class="btn btn-dark border border-4 border-danger ms-3">
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
@endsection
