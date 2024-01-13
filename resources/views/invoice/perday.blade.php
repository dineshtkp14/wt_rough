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
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Sales Per Day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDay as $sale)
                                    <tr>
                                        <td>{{ $sale->date }}</td>
                                        <td>{{ $sale->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="col">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date (CREDIT NOTES)</th>
                                    <th>Total Credit Notes Per day</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesPerDaycrnotes as $sale)
                                    <tr>
                                        <td>{{ $sale->date }}</td>
                                        <td>{{ $sale->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                {{ $salesPerDay->links() }}
                {{ $salesPerDaycrnotes->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
