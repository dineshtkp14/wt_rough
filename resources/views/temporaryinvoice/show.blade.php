@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    <div class="main-content">
        @yield('breadcrumb')

        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Temporary Invoice #{{ $temporaryinvoice->invoice_number ?? $temporaryinvoice->id }}</h4>
                <div>
                    <a href="{{ route('temporaryinvoice.index') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-list"></i> Back to List
                    </a>
                    <a href="{{ route('temporaryinvoice.create') }}" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> New
                    </a>
                    <a href="{{ route('temporaryinvoice.print', $temporaryinvoice) }}" class="btn btn-success" target="_blank">
                        <i class="fa-solid fa-print"></i> Print
                    </a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Date</small>
                            <div class="fw-bold">{{ $temporaryinvoice->invoice_date }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Customer Name</small>
                            <div class="fw-bold">{{ $temporaryinvoice->customer_name }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Address</small>
                            <div class="fw-bold">{{ $temporaryinvoice->customer_address }}</div>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Contact No</small>
                            <div class="fw-bold">{{ $temporaryinvoice->contact_number }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($temporaryinvoice->items as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-end">Subtotal</th>
                                    <th>{{ number_format($temporaryinvoice->subtotal, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">Discount</th>
                                    <th>{{ number_format($temporaryinvoice->discount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="5" class="text-end">Total</th>
                                    <th>{{ number_format($temporaryinvoice->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if ($temporaryinvoice->notes)
                        <div class="mt-3">
                            <small class="text-muted">Notes</small>
                            <div>{{ $temporaryinvoice->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
