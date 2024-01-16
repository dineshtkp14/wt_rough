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
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <table class="table h5">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Sales Per Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDay as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDay->links() }}

                    </div>

                    <div class="col">
                        <table class="table h5">
                            <thead>
                                <tr>
                                    <th>Date (CREDIT NOTES)</th>
                                    <th>Total Credit Notes Per day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDaycrnotes as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDaycrnotes->links() }}

                    </div>


                    <div class="col">
                        <table class="table h5">
                            <thead>
                                <tr>
                                    <th>Date (CREDIT )</th>
                                    <th>Total Credit Sales Per day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDayCredit as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDaycrnotes->links() }}

                    </div>


                    <div class="col">
                        <table class="table h5">
                            <thead>
                                <tr>
                                    <th>Date (Cash Ony)</th>
                                    <th>Total Cash only </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDayCash as $sale)
                                <tr @if(now()->format('Y-m-d') == $sale->date) style="color: red; font-weight: bold;" @endif>
                                    <td>{{ $sale->date }}</td>
                                    <td>{{ $sale->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $salesPerDaycrnotes->links() }}

                    </div>

                </div>
            </div>
            {{-- <div class="card-footer text-muted">
                {{ $salesPerDay->links() }}
                {{ $salesPerDaycrnotes->links() }}
            </div> --}}
        </div>
    </div>

</div>
@endsection
