@extends('layouts.master')
@section('content')


<style>
    .btn-super-duper-bigger {
        width: 380px; /* Increased width for bigger buttons */
        padding: 30px; /* Adjusted padding for desired button height */
        font-size: 30px; /* Increased font size for bigger text */
        border-radius: 10px; /* Rounded corners for buttons */
        transition: all 0.3s ease; /* Smooth transition for hover effect */
        display: flex; /* Use flexbox for alignment */
        justify-content: center; /* Center content horizontally */
        align-items: center; /* Center content vertically */
    }

    .btn-super-duper-bigger .fa {
        margin-right: 10px; /* Add margin between icon and text */
        font-size: 60px; /* Increased font size for bigger icon */
    }

    .btn-super-duper-bigger:hover {
        transform: scale(1.1); /* Scale button slightly on hover */
    }

    .btn-super-duper-bigger:focus {
        outline: none; /* Remove outline on focus for better visual */
    }

    .bg-primary-custom {
        background-color: #064b19; /* Custom primary background color */
    }

    .text-white-custom {
        color: #ffffff; /* Custom text color */
    }
</style>
<div class="main-content"> 

    <div class="row mt-3">
        <div class="col-md-4 mb-3">
            <a href="{{ route('CheckBankDeposit.index') }}" class="btn btn-primary btn-block btn-super-duper-bigger"><i class="fa fa-university"></i> BANK Check</a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="{{ route('CheckCounterDeposit.index') }}" class="btn btn-primary btn-block btn-super-duper-bigger"><i class="fa fa-file-invoice"></i> Counter Check</a>
        </div>

{{-- 
        <div class="col-md-4 mb-3">
            <a href="{{ route('change-password') }}" class="btn btn-primary btn-block btn-super-duper-bigger"><i class="fa fa-key"></i> CHANGE PASSWORD</a>
        </div> --}}
       
    </div>


</div>

    @stop