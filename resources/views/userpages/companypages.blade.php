@extends('layouts.master')
@section('content')

<style>
     .btn-container {
        display: flex;
        justify-content: center;
    }

    .btn-super-duper-bigger {
        width: 300px; /* Fixed width for consistent size */
        padding: 30px; /* Adjust padding for desired button height */
        font-size: 24px; /* Adjust font size for desired text size */
        position: relative; /* Positioning for icon */
        overflow: hidden; /* Hide overflow to prevent icon overflow */
        border-radius: 10px; /* Rounded corners for buttons */
        transition: all 0.3s ease; /* Smooth transition for hover effect */
        display: flex; /* Use flexbox for alignment */
        justify-content: center; /* Center content horizontally */
        align-items: center; /* Center content vertically */
        text-decoration: none; /* Remove default underline */
        color: #ffffff; /* Text color */
        margin-bottom: 10px; /* Reduce margin at the bottom */
    }

    .btn-super-duper-bigger .fa {
        margin-right: 10px; /* Add margin between icon and text */
        font-size: 36px; /* Increased font size for bigger icon */
        transition: all 0.3s ease; /* Smooth transition for icon movement */
    }

    .btn-super-duper-bigger .fas.fa-dollar-sign {
        font-size: 36px; /* Adjust the font size as needed */
    }
    .btn-super-duper-bigger:hover {
        transform: translateY(-5px); /* Move button up slightly on hover */
    }

    .btn-super-duper-bigger:hover .fa {
        transform: scale(1.2); /* Scale icon slightly on hover */
    }

    .bg-primary {
        background-color: #064b19 !important; /* Custom primary background color */
    }

    .text-white {
        color: #ffffff; /* Custom text color */
    }

    .back-btn {
        margin-bottom: 20px; /* Add margin at the bottom */
        background-color: #e411a5; /* Unique background color */
        border-color: #ff7f50; /* Border color same as background color */
    }

    .back-btn:hover {
        background-color: #ff6347; /* Unique hover background color */
        border-color: #ff6347; /* Border color same as hover background color */
    }

    .back-btn .fa {
        color: #ffffff; /* Icon color */
    }
</style>

<div class="main-content">

    <h2 class="text-center mt-3 bg-primary p-3 text-white">COMPANY PAGE</h2>
    <a href="{{ url()->previous() }}" class="btn btn-primary back-btn"><i class="fa fa-chevron-left"></i> BACK </a>
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <a href="{{ route('companys.create') }}" class="btn btn-primary btn-block btn-super-duper-bigger">
                <i class="fa fa-plus-circle"></i> ADD COMPANY
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('companys.index') }}" class="btn btn-primary btn-block btn-super-duper-bigger">
                <i class="fa fa-list"></i> VIEW COMPANY
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('companybillentry.create') }}" class="btn btn-primary btn-block btn-super-duper-bigger">
                <i class="fa fa-file"></i> BILL ENTRY
            </a>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <a href="{{ route('companyledgerdetails.returnchoosendatehistroy') }}" class="btn btn-primary btn-block btn-super-duper-bigger">
                <i class="fa fa-book"></i> VIEW COMPANY LEDGER
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('companyLedgerspay.create') }}" class="btn btn-primary btn-block btn-super-duper-bigger">
                <i class="fas fa-dollar-sign "></i> COMPANY PAYMENT
            </a>
        </div>
        
    
        <div class="col-md-4 mb-3">
            <a href="{{ route('companybillentry.index') }}" class="btn btn-primary btn-block btn-super-duper-bigger">
                <i class="fa fa-table"></i> VIEW BILL ENTRY TABLE
            </a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="{{ route('ViewWholeitemsBill.index') }}" class="btn btn-primary btn-block btn-super-duper-bigger">
                <i class="fa fa-eye"></i> SEARCH WHOLE BILL
            </a>
        </div>
    </div>

</div>
@stop
