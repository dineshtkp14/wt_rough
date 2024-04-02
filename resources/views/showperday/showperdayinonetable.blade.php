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
                    Total No Of Items  <span class="h3 btn btn-dark btn-lg">In Conunter : {{ $totalCashAndPaymentToday }}
                        -{{$totalCreditNotesTodaySUM}}={{$totalCashAndPaymentToday-$totalCreditNotesTodaySUM}}</span>
                    <a href="{{ route('showonlysalesperday.pp') }}" class="btn btn-primary border border-5 border-warning float-end me-2">
                        <i class="fas fa-check"></i> Check All Sales Amount
                    </a>
                    
                </div>
                <div class="card-body ">
                    <div class="row">

                        {{-- <div class="col">
                            <table class="table h5 " >
                                <thead>

                                    <center>  <SPAN class="h5 btn btn-warning fw-bold"> CREDIT SALES</SPAN> </center>

                                

                                    <tr>
                                        <th>Date (CREDIT )</th>
                                        <th>Total Credit Sales Per day</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesPerDayCredit as $crsale)
                                    <tr @if(now()->format('Y-m-d') == $crsale->date) style="color: white;background:red; font-weight: bold;" @endif>
                                        <td>{{ $crsale->date }}</td>
                                        <td>{{ $crsale->total }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                          
                            {{ $salesPerDaycrnotes->links() }}

                        </div> --}}


                        {{-- <div class="col">
                            <table class="table h5">
                                <thead>
                                    <center>  <SPAN class="h5 btn btn-warning fw-bold"> CASH SALES</SPAN> </center>

                                

                                    <tr>
                                        <th>Date (Cash Ony)</th>
                                        <th>Total Cash only </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesPerDayCash as $cashsale)
                                    <tr @if(now()->format('Y-m-d') == $cashsale->date) style="color: white;background:red; font-weight: bold;" @endif>
                                        <td>{{ $cashsale->date }}</td>
                                        <td>{{ $cashsale->total }}</td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $salesPerDaycrnotes->links() }}

                        </div> --}}





                        {{-- <div class="col">
                            <table class="table h5">
                                <thead>
                                    <center>  <SPAN class="h5 btn btn-warning fw-bold"> TOTAL SALES PER DAY </SPAN> </center>

                                    <tr>
                                        <th>Date</th>
                                        <th>Total Sales Per Day</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesPerDay as $totalcashcreditsales)
                                    <tr @if(now()->format('Y-m-d') == $totalcashcreditsales->date) style="color: white;background:red; font-weight: bold;" @endif>
                                        <td>{{ $totalcashcreditsales->date }}</td>
                                        <td>{{ $totalcashcreditsales->total }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $salesPerDay->links() }}

                        </div>

                        <div class="col">
                            <table class="table h5">
                                <thead>
                                    <center>  <SPAN class="h5 btn btn-warning fw-bold">CREDIT NOTES/SALES RETURN
                                    </SPAN> </center>


                                    <tr>
                                        <th>Date (CREDIT NOTES)</th>
                                        <th>Total Credit Notes Per day</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesPerDaycrnotes as $crnotes)
                                    <tr @if(now()->format('Y-m-d') == $crnotes->date) style="color: white;background:red; font-weight: bold;" @endif>
                                        <td>{{ $crnotes->date }}</td>
                                        <td>{{ $crnotes->total }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $salesPerDaycrnotes->links() }}

                        </div> --}}


                    
                        
                        {{-- <div class="col">
                            <table class="table h5">
                                <thead>
                                    <center>  <SPAN class="h5 btn btn-warning fw-bold">  LEDGER PAYMENT</SPAN> </center>

                            
                                    <tr>
                                        <th>Date (Payment)</th>
                                        <th>Total Payment only </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment as $pay)
                                    <tr @if(now()->format('Y-m-d') == $pay->date) style="color: white;background:red; font-weight: bold;" @endif>
                                        <td>{{ $pay->date }}</td>
                                        <td>{{ $pay->total }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $salesPerDaycrnotes->links() }}

                        </div> --}}


                        
                        <div class="col">
                            <table class="table h5">
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
                                                &#10004; <!-- Checkmark HTML entity -->
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <?php if ($data['bank_deposit'] == 'yes'): ?>
                                                &#10004; <!-- Checkmark HTML entity -->
                                            <?php endif; ?>
                                        </td>

                                       
                                        
                                    </tr>
                                @endforeach
                                
                                </tbody>
                            </table>
                            {{ $salesPerDaycrnotes->links() }}

                        </div>


                     
                  
                       
            
            </div>
        </div>

    </div>
    @endsection
