@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')

    <div class="container">
        @if (Session::has('error'))
        <div class="alert bg-danger text-white w-50">
            {{ Session::get('error') }}
        </div>
        @endif
    </div>

    <!-- Fetch data of the item being edited and populate form fields -->
    <div class="container">
        @if ($payment)
        <h4>Edit Cash Receipt No: {{$payment->id}}</h4>
        <form class="row gx-5 gy-3" action="{{ route('cpayments.update', $payment->id) }}" method="post">
        @method('PUT')
        @else
        <h4>Cash Receipt No: {{$nextUserId}}</h4>
        <form class="row gx-5 gy-3" action="{{ route('cpayments.store') }}" method="post">
        @endif
            @csrf

            <!-- Existing Form Fields -->
            <div class="py-4 d-flex justify-content-between align-items-center">
                <div style="width: 300px">
                    <div class="input-group mb-1">
                        <!-- Adjust fields with pre-filled data if editing -->
                        <span class="input-group-text">Date: <span style="color: red;">*</span></span>
                        <input  type="date" class="form-control @error('date') is-invalid @enderror" placeholder="" id="salesDate" class="form-control foritemsaledatecss" value="{{ $payment ? $payment->date : now()->format('Y-m-d') }}" name="date">
                        @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Checkbox -->
            <div class="col-md-12">
                <div class="form-check d-flex align-items-center">
                    <input  class="form-check-input me-2" type="checkbox" id="disableFields" name="disableFields" style="width: 30px; height: 30px;">
                    <label class="form-check-label" for="disableFields">If Sales Return</label>
                </div>
            </div>

            <!-- Other Form Fields -->
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label"> Particulars (Bank Name/Fone Pay/Payment) <span style="color: red;">*</span></label>
                <input  autocomplete="off" id="particulars" type="text" class="form-control @error('particulars') is-invalid @enderror" name="particulars" value="{{ old('particulars', $payment ? $payment->particulars : '') }}" >
                <input id="hiddenParticulars" type="hidden" name="hiddenParticulars" value="{{ old('hiddenParticulars', $payment ? $payment->hiddenParticulars : '') }}">
                @error('particulars')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Voucher Type (Receipt/Cash) <span style="color: red;">*</span></label>
                <input autocomplete="off" id="vt" type="text" class="form-control @error('vt') is-invalid @enderror" name="vt" value="{{ old('vt', $payment ? $payment->vt : '') }}" >
                <input id="hiddenVt" type="hidden" name="hiddenVt" value="{{ old('hiddenVt', $payment ? $payment->hiddenVt : '') }}">
                @error('vt')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <!-- Additional Field Container -->
            <div class="col-md-6" id="additionalFieldContainer" style="display: none;">
                <label for="cninvoiceid" class="form-label">Credit Notes Invoice ID</label>
                <input autocomplete="off" type="number" class="form-control" id="cninvoiceid" name="cninvoiceid" value="{{ old('cninvoiceid', $payment ? $payment->cninvoiceid : '') }}">
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Amount <span style="color: red;">*</span></label>
                <input  autocomplete="off" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', $payment ? $payment->amount : '') }}" >
                @error('amount')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea autocomplete="off" class="form-control @error('notes') is-invalid @enderror" name="notes">{{ old('notes', $payment ? $payment->notes : '') }}</textarea>
                @error('notes')
                <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2 pt-2 pb-4">
                <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('disableFields');
    const particularsInput = document.getElementById('particulars');
    const hiddenParticularsInput = document.getElementById('hiddenParticulars');
    const voucherTypeInput = document.getElementById('vt');
    const hiddenVoucherTypeInput = document.getElementById('hiddenVt');
    const additionalFieldContainer = document.getElementById('additionalFieldContainer');

    checkbox.addEventListener('change', function() {
        if (this.checked) {
            particularsInput.value = 'salesreturn';
            voucherTypeInput.value = 'return';
            hiddenParticularsInput.value = 'salesreturn';
            hiddenVoucherTypeInput.value = 'return';
            particularsInput.disabled = true;
            voucherTypeInput.disabled = true;
            additionalFieldContainer.style.display = 'block'; // Show the additional field
        } else {
            particularsInput.value = '';
            voucherTypeInput.value = '';
            hiddenParticularsInput.value = '';
            hiddenVoucherTypeInput.value = '';
            particularsInput.disabled = false;
            voucherTypeInput.disabled = false;
            additionalFieldContainer.style.display = 'none'; // Hide the additional field
        }
    });

    particularsInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }
    });

    voucherTypeInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }
    });
});


$(document).ready(function () {
            $('form').submit(function () {
                // Disable the submit button
                $('#submitBtn').prop('disabled', true);
                
            });
        });
</script>
@stop
