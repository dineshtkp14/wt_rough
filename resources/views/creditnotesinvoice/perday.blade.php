{{-- itemssales/perday.blade.php --}}
@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 

    @yield('breadcrumb')

    



    <div class="container">
        <div class="card ">
             <div class="card-header">
                <a href="{{route('items.create')}}"><img src="https://img.icons8.com/glyph-neue/50/40C057/plus-2-math.png"/></a>
                 Total No Of Items 
    
             </div>
             <div class="card-body">
                @if(count($salesPerDay) > 0)
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
               
                @else
                    <p>No sales data available.</p>
                @endif
            </div>
            <div class="card-footer text-muted">
                {{ $salesPerDay->links() }}
            </div>
          </div>
    
    
    
     
    </div>
    
    

</div>
@endsection
