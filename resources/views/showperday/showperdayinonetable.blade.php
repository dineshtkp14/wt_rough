@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

    @yield('breadcrumb')

    <div class="container">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('items.create') }}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
                Total No Of Items  <span class="h3 btn btn-dark btn-lg">In Conunter : {{ $totalCashAndPaymentToday }} - {{ $totalCreditNotesTodaySUM }} = {{ $totalCashAndPaymentToday - $totalCreditNotesTodaySUM }}</span>
                <a href="{{ route('showonlysalesperday.pp') }}" class="btn btn-primary border border-5 border-warning float-end me-2">
                    <i class="fas fa-check"></i> Check All Sales Amount
                </a>
            </div>
            <div class="card-body ">

            

                <div class="row">
                    <div class="col-md-3 m-0 p-0">
                        <table class="table h5 border border-5 border-dark" style="" >
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold">CREDIT NOTES-LEDGER
                                </SPAN> </center>


                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forsalesreturn as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $forsalesreturn->links() }}

                    </div>


                    
                    <div class="col-md-9">
                        <table class="table h5 border border-1 border-warning">
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold">Total Cash in Counter
                                </SPAN> </center>

                                <tr>
                                    <th>Date</th>
                                    <th>(C-N)</th>
                                    <th class="bg-dark">Total</th>

                                    <th>Counter Check</th>
                                    <th>Bank Deposit Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                               
                                @foreach($totalSalesAndPayments as $data)
                                <tr @if(now()->format('Y-m-d') == $data['date']) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $data['date'] }}</td>
                                    <td> {{ $data['total']}} -{{$data['credit_notes_total']  }}</td>
                                    <td class="bg-dark text-white h3"><span style="text-decoration: underline;">{{$data['total'] - $data['credit_notes_total']}}</span></td>

                                    
                                   
                                  
                                  </td>
                                    <td class="text-center">
                                        @if ($data['counter_deposit'] == 'yes')
                                        
                                        <i class="fas fa-check-circle fa-2x"></i> <!-- Larger size -->
                                        @endif
                                    </td>
                                    <td class="text-center">

                                        @if ($data['bank_deposit'] == 'yes')
                                        <i class="fas fa-check-circle fa-2x"></i> <!-- Larger size -->
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $totalSalesAndPayments->appends(['page' => $totalSalesAndPayments->currentPage()])->withPath(route('showonlysalesperdayinone_table.pp'))->links() }}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection