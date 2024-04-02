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
                    <div class="col-md-4">
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


                    
                    <div class="col-md-8">
                        <table class="table h5">
                            <thead>
                                <center>  <SPAN class="h5 btn btn-warning fw-bold">Total Cash
                                </SPAN> </center>

                                <tr>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Counter Check</th>
                                    <th>Bank Deposit Check</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($totalSalesAndPayments as $data)
                                <tr @if(now()->format('Y-m-d') == $data['date']) style="color: white;background:red; font-weight: bold;" @endif>
                                    <td>{{ $data['date'] }}</td>
                                    <td>{{ $data['total'] }}</td>
                                    <td>
                                        @if ($data['counter_deposit'] == 'yes')
                                            &#10004; <!-- Checkmark HTML entity -->
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data['bank_deposit'] == 'yes')
                                            &#10004; <!-- Checkmark HTML entity -->
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $totalSalesAndPayments->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
