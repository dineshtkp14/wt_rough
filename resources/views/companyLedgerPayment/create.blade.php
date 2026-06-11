@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content company-create-page company-payment-create-page"> 
        @yield('breadcrumb')


    <div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
        <div class="card-body">
            <h5 class="card-title">Company Information</h5>
            <p>
                <span>ID: </span><span id="customerId">...</span>
            </p>
            <p class="card-text">
                <span>Name: </span><span id="customerName">...</span>
            </p>
            <p>
                <span>Addres: </span><span id="customerAddress">...</span>
            </p>
            <p>
                <span>E-mail: </span><span id="customerEmail">...</span>
            </p>
            <p>
                <span>PhoneNo: </span><span id="customerPhone">...</span>
            </p>
        </div>

        <div class="toogle-box p-3 d-flex justify-content-center align-items-center" id="toggleBox" data-toggle="close">
            <i class="fas fa-user"></i>
        </div>
    </div>

 


<div class="container-fluid company-create-shell">
            @if (Session::has('success'))
            <div class="alert bg-success text-white company-alert">
                {{ Session::get('success') }}
                </div>
            @endif
</div>

<div class="container-fluid company-create-shell">


<form class="row gx-4 gy-3 company-entry-form" action="{{route('companyLedgerspay.store')}}" method="post">
                @csrf

               
           
           
                  <div class="col-12 company-form-head">
                    <div class="company-search-control">
                      
                        <div class="input-group mb-1">
                            <div class="search-box">
                               
                                <input id="customerIdInput"  name="companyid" hidden required>

                                <input  autocomplete="off" type="text" required class="search-input @error('customerid') is-invalid @enderror" placeholder="Search Company"
                                    id="searchCustomerInput"  data-api="company_search" autocomplete="off">
                                    @error('customerid')
                                        <p class="invalid-feedback m-0" style="position: absolute; bottom: -24px; left: 0;">{{ $message }}</p>
                                    @enderror  
                                    
                                <i class="fas fa-search search-icon"> </i>
                                <div class="result-wrapper" id="customerResultWrapper" style="display: none;">
                                    <div class="result-box d-flex justify-content-start align-items-center"
                                        id="customerLoadingResultBox">
                                        <i class="fas fa-spinner" id="spinnerIcon"> </i>
                                        <h1 class="m-0 px-2"> Loading</h1>
                                    </div>
    
                                    <div class="result-box d-flex justify-content-start align-items-center d-none"
                                        id="customerNotFoundResultBox">
                                        <i class="fas fa-triangle-exclamation"> </i>
                                        <h1 class="m-0 px-2"> Record Not Found</h1>
                                    </div>
    
                                <div id="customerResultList">
                                    </div>
                                </div>
                                <div class="company-selected-info" id="selectedCompanyInfo" style="display: none;">
                                    <span>Address:</span>
                                    <strong id="selectedCompanyAddress">-</strong>
                                    <span>Contact No:</span>
                                    <strong id="selectedCompanyPhone">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="company-date-control">
                      
                        <div class="input-group mb-1">
                            <span class="input-group-text">Date:  <span style="color: red;">*</span></span>
                            <input  autocomplete="off" type="date" class="form-control  @error('date') is-invalid @enderror" placeholder="" id="salesDate" class="form-control foritemsaledatecss" value="{{now()->format('Y-m-d')}}" name="date" >
                            @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Particulars <span class="text-success fw-bold">(Bank Name / Cash) </span>  <span style="color: red;">*</span></label>
                    <input  autocomplete="off" type="text" class="form-control @error('particulars') is-invalid @enderror" 
                        name="particulars" value="{{ old('particulars') }}">
                    @error('particulars')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Voucher Type<span class="text-success fw-bold"> (Receipt / Cash) </span>  <span style="color: red;">*</span></label>
                    <input  autocomplete="off" type="text" class="form-control @error('vt') is-invalid @enderror" 
                        name="vt" value="{{ old('vt') }}">
                    @error('vt')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Amount  <span style="color: red;">*</span></label>
                    <input  id="amount" autocomplete="off" type="number" class="form-control @error('amount') is-invalid @enderror" 
                        name="amount" value="{{ old('amount') }}">
                    @error('amount')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea  autocomplete="off"  class="form-control @error('notes') is-invalid @enderror" 
                    name="notes" value="{{ old('notes') }}"> </textarea>
                @error('notes')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

        <div class="col-md-12">
            <label for="inputPassword4" class="form-label">Amount in Words: </label>
           <b> <span id="amountInWords"></span></b>
        </div>

           

            <div class="col-12 company-form-actions">
                    <button type="submit" id="companySubmitBtn" class="btn btn-lg btn-primary company-submit-btn">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Save Payment
                    </button>
            </div>
</form>
</div>



</div>



<script>
    function convertNumberToWords(num) {
    var ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen", "Nineteen"];
    var tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];
    var decimals = ["", "Tenth", "Hundredth"];

    // Split the number into integer and fractional parts
    var parts = String(num).split('.');
    var integerPart = parseInt(parts[0], 10);
    var fractionalPart = parts[1] ? parseInt(parts[1], 10) : 0; // Convert fractional part to an integer

    var words = "";

    // Convert the integer part to words
    if (integerPart === 0) {
        words = "Zero";
    } else {
        // Convert each part of the number separately
        if (integerPart >= 10000000) {
            words += convertNumberToWords(Math.floor(integerPart / 10000000)) + " Crore ";
            integerPart %= 10000000;
        }
        if (integerPart >= 100000) {
            words += convertNumberToWords(Math.floor(integerPart / 100000)) + " Lakh ";
            integerPart %= 100000;
        }
        if (integerPart >= 1000) {
            words += convertNumberToWords(Math.floor(integerPart / 1000)) + " Thousand ";
            integerPart %= 1000;
        }
        if (integerPart >= 100) {
            words += convertNumberToWords(Math.floor(integerPart / 100)) + " Hundred ";
            integerPart %= 100;
        }
        if (integerPart >= 20) {
            words += tens[Math.floor(integerPart / 10)] + " ";
            integerPart %= 10;
        }
        if (integerPart > 0) {
            words += ones[integerPart] + " ";
        }
    }

    // Convert the fractional part to words
    if (fractionalPart > 0) {
        words += " and " + ones[fractionalPart] + " " + decimals[parts[1].length] + " "; // Append the fractional part
    }

    return words.trim();
}

        function updateAmountInWords() {
            var amount = parseInt(document.getElementById('amount').value, 10); // Parse the input as an integer
            var amountInWords = convertNumberToWords(amount);
            document.getElementById('amountInWords').innerText = amountInWords + '  Only/-';
        }


        document.getElementById('amount').addEventListener('input', updateAmountInWords);
</script>
<script>
        
    $(document).ready(function () {
            $('form').submit(function () {
                // Disable the submit button
                $('#companySubmitBtn').prop('disabled', true);
                
            });
        });

        $(document).on('customer:selected', function (event, company) {
            const phoneParts = [];

            if (company.phoneno) {
                phoneParts.push(company.phoneno);
            }

            if (company.alternate_phoneno) {
                phoneParts.push(company.alternate_phoneno);
            }

            $('#selectedCompanyAddress').text(company.address || '-');
            $('#selectedCompanyPhone').text(phoneParts.length ? phoneParts.join(', ') : '-');
            $('#selectedCompanyInfo').slideDown(150);
        });
    
        </script>
<style>
    .company-create-page {
        overflow-x: hidden;
        padding-bottom: 96px;
    }

    .company-create-shell {
        box-sizing: border-box;
        max-width: 100%;
        padding-left: 26px;
        padding-right: 26px;
        width: 100%;
    }

    .company-alert {
        border: 0;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 800;
        max-width: 760px;
    }

    .company-entry-form {
        background: #ffffff;
        border: 1px solid #dbe3ef;
        border-radius: 8px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .07);
        box-sizing: border-box;
        margin-top: -42px;
        max-width: 100%;
        padding: 22px;
    }

    .company-form-head {
        align-items: flex-start;
        border-bottom: 1px solid #e2e8f0;
        display: grid;
        gap: 18px;
        grid-template-columns: minmax(280px, 420px) minmax(240px, 340px);
        justify-content: space-between;
        margin-bottom: 8px;
        padding-bottom: 18px;
    }

    .company-search-control,
    .company-date-control {
        width: 100%;
    }

    .company-create-page .search-box,
    .company-create-page .input-group {
        width: 100%;
    }

    .company-create-page .search-input,
    .company-create-page .form-control,
    .company-create-page .input-group-text {
        border-color: #cbd5e1;
        min-height: 44px;
    }

    .company-create-page .search-input {
        border-radius: 6px;
        font-size: 18px;
        padding-left: 46px;
        width: 100%;
    }

    .company-create-page .form-label {
        color: #0f172a;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .company-create-page textarea.form-control {
        min-height: 70px;
    }

    .company-create-page #amount {
        font-size: 20px;
        font-weight: 800;
    }

    .company-create-page #amountInWords {
        color: #0f5132;
        font-size: 18px;
        font-weight: 900;
        text-transform: capitalize;
    }

    .company-selected-info {
        align-items: center;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px 10px;
        margin-top: 10px;
        padding: 9px 12px;
    }

    .company-selected-info span {
        color: #64748b;
        font-size: 13px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .company-selected-info strong {
        color: #0f172a;
        font-size: 15px;
        font-weight: 900;
        margin-right: 10px;
    }

    .company-form-actions {
        align-items: center;
        background: rgba(255, 255, 255, .96);
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        bottom: 16px;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .18);
        display: flex;
        justify-content: center;
        margin: 18px auto 0;
        padding: 14px;
        position: sticky;
        width: fit-content;
        z-index: 1050;
    }

    .company-submit-btn {
        align-items: center;
        border-radius: 6px;
        display: inline-flex;
        font-size: 30px;
        font-weight: 900;
        gap: 14px;
        justify-content: center;
        min-height: 86px;
        min-width: 720px;
        max-width: calc(100vw - 360px);
        padding-left: 42px;
        padding-right: 42px;
        text-transform: uppercase;
    }

    .company-submit-btn i {
        font-size: 34px;
    }

    @media (max-width: 900px) {
        .company-create-shell {
            padding-left: 14px;
            padding-right: 14px;
        }

        .company-form-head {
            grid-template-columns: 1fr;
        }

        .company-form-actions {
            border-radius: 0;
            bottom: 0;
            margin-left: -14px;
            margin-right: -14px;
            width: auto;
        }

        .company-submit-btn {
            max-width: none;
            min-width: 0;
            width: 100%;
        }
    }
</style>
@stop
