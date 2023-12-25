@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content">
    @yield('breadcrumb')

    <div class="container">
        <div class="card-container">
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="card-title">
                    Add New Bill
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-title">
                    View Customer Ledger
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-title">
                    View Customer Ledger
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-title">
                    View Company Ledger
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="card-title">
                    Add New Customer
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="card-title">
                    Search Bill
                </div>
            </div>
        </div>
    </div>
</div>
@stop
