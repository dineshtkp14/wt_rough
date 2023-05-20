@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')

<div class="main-content"> 

    @yield('breadcrumb')

    <div class="container">
        @if (Session::has('success'))
            <div class="alert alert-success w-50">
                {{ Session::get('success') }}
            </div>
        @endif
    </div>

    <div class="container">
        <div class="row float-end">
            <div class="col-12 float-end">
                <input class="form-control border-warning border-2" id="filterInput" type="text" placeholder="Search Here">
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Bill No</th>
                    <th>Items Name</th>
                    <th>Unstocked Name</th>
                    <th>Quantity</th>
                    <th>Original Price</th>
                    <th>Sold Price</th>
                    <th>Discount</th>
                    <th>Sub-Total</th>
                </tr>
            </thead>
            <tbody>
                @if ($cus->isNotEmpty())
                    @foreach ($cus as $item)
                        <tr>
                            <td data-label="Bill No">{{ $item->invoiceid }}</td>
                            <td data-label="Items Name">{{ $item->itemname ? $item->itemname : '-' }}</td>
                            <td data-label="Unstocked Name">{{ $item->unstockedname ? $item->unstockedname : '-' }}</td>
                            <td data-label="Quantity">{{ $item->quantity }}</td>
                            <td data-label="Original Price">{{ $item->itemprice ? $item->itemprice : '-' }}</td>
                            <td data-label="Price">{{ $item->price }}</td>
                            <td data-label="Discount">{{ $item->discount }}</td>
                            <td data-label="Sub-Total">{{ $item->subtotal }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8"><h3>Database is Empty !! Please add items to view the list.</h3></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@stop
