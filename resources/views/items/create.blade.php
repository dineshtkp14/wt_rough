@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
   
@yield('breadcrumb')



<div class="card customer-card mb-4" id="customerCard" style="display: none;" style="">
    
  
    
    <div class="card-body">
        <h5 class="card-title">Company Info</h5>
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


<div class="container mt-5">
            @if (Session::has('success'))
            <div class="alert bg-success text-white w-50">
                {{ Session::get('success') }}
                </div>
            @endif
</div>


<div class="container">
   

<form class="row gx-5 gy-3" action="{{ route('items.store') }}" method="post" style="margin-top:-150px;">
                @csrf

                <div class="col-md-6 float-end"> <a href="{{ route('companys.create') }}"  class=" float-end btn btn-md btn-primary border border-5 border-dark ms-auto" style="background-color: brown; border-color: orange;">
                    <i class="fas fa-plus-circle me-2"></i> <!-- Font Awesome icon -->
                    Add New Company
                </a></div>
                
                <div class="col-md-6 float-end">
                    <label for="inputPassword4" class="form-label">Date</label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                        name="date" value="{{now()->format('Y-m-d')}}" id="">
                    @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                </div> 

                <div class="col-md-12">-</div>


            <div class="col-md-6">
               
                 <div class="search-box">
                            <input id="customerIdInput" name="companyid" hidden>
                            <input type="text"  class="search-input @error('companyid') is-invalid @enderror" placeholder="Search Company Name"
                                id="searchCustomerInput"  data-api="company_search" autocomplete="off">
                                @error('companyid')
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
                        </div>
        </div>

        
           


        <div class="col-md-6">
            <label for="inputPassword4" class="form-label">My Firm Name (CHOOSE FIRM)  <span style="color: red;">*</span></label>
            <select class="form-select @error('firm_name') is-invalid @enderror" name="firm_name" id="firm_name" stye="border-color: red;">
                @foreach($all as $firm)
                <option value="{{ $firm->nick_name }}">{{ $firm->firm_name }}</option>
            @endforeach
            </select>
            @error('firm_name')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
        </div>
        

     
    
       
                 

                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Bill No </label>
                    <input type="text" placeholder=" Please Enter Bill No" class="form-control @error('billno') is-invalid @enderror" 
                        name="billno" value="{{ old('billno') }}">
                    @error('billno')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

         
           
           

           

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">ITEMS Name  <span style="color: red;">*</span></label>
                    <input placeholder=" Please Enter Items Name" type="text" class="form-control @error('itemsname') is-invalid @enderror" 
                        name="itemsname" value="{{ old('itemsname') }}">
                    @error('itemsname')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Quantity  <span style="color: red;">*</span></label>
                    <input placeholder=" Please Enter Quantity" type="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                        name="quantity" value="{{ old('quantity') }}">
                    @error('quantity')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Unit(PCS/kg/etc)  <span style="color: red;">*</span></label>
                <input type="unit" placeholder=" Please Enter Unit" class="form-control @error('unit') is-invalid @enderror" 
                    name="unit" value="{{ old('unit') }}">
                @error('unit')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Show Stock Warning  <span style="color: red;">*</span></label>
                <input placeholder=" Please Enter Show Stock Warning" type="showwarning" class="form-control @error('showwarning') is-invalid @enderror" 
                    name="showwarning" value="{{ old('showwarning') }}">
                @error('showwarning')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">$Cost Rate Per (PCS/kg)  <span style="color: red;">*</span></label>
                    <input placeholder=" Please Enter Cost Rate" type="text" class="form-control @error('costprice') is-invalid @enderror" 
                        name="costprice" value="{{ old('costprice') }}">
                    @error('costprice')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">$Sale Price Per (PCS/kg)  <span style="color: red;">*</span></label>
                <input placeholder=" Please Enter Sale Price" type="text" class="form-control @error('mrp') is-invalid @enderror" 
                    name="mrp" value="{{ old('mrp') }}">
                @error('mrp')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>


            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Iteam Store Area  <span style="color: red;">*</span></label>
                <input  placeholder=" Please Enter Iteam Store Area" type="text" class="form-control @error('itemstorearea') is-invalid @enderror" 
                    name="itemstorearea" value="{{ old('itemstorearea') }}">
                @error('itemstorearea')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
            </div>

        <div class="col-md-12">
            <label for="inputPassword4" class="form-label">Notes</label>
            <textarea type="text" class="form-control @error('notes') is-invalid @enderror" 
                name="notes" value="{{ old('notes') }}"> </textarea>
            @error('notes')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
    </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">$Wholesale Price Per (PCS/kg)</label>
                <input placeholder=" Please Enter Wholesale Price"  type="text" class="form-control @error('wp') is-invalid @enderror" 
                    name="wp" value="{{ old('wp') }}">
                @error('wp')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

        <div class="col-md-6">
            <label for="inputPassword4" class="form-label">$Competitive Retail Sale Price Per (PCS/kg)</label>
            <input type="text" placeholder=" Please Enter Competitive Retail Sale Price" class="form-control @error('competetiveretail') is-invalid @enderror" 
                name="competetiveretail" value="{{ old('competetiveretail') }}">
            @error('competetiveretail')
                <p class="invalid-feedback">{{ $message }}</p>
            @enderror
    </div>

    <div class="col-md-6">
        <label for="inputPassword4" class="form-label">$Competitive Wholesale Sale Price Per (PCS/kg)</label>
        <input placeholder=" Please Enter Competitive Wholesale Sale Price"  type="text" class="form-control @error('competetivewholesale') is-invalid @enderror" 
            name="competetivewholesale" value="{{ old('competetivewholesale') }}">
        @error('competetivewholesale')
            <p class="invalid-feedback">{{ $message }}</p>
        @enderror
</div>



           
            

           
           

            <div class="d-grid gap-2 pt-2 pb-4">
                    <button type="submit" id="submitBtn" class="btn btn-lg btn-primary">Save</button>
            </div>
</form>
</div>

</div>



    <script>
        
$(document).ready(function () {
        $('form').submit(function () {
            // Disable the submit button
            $('#submitBtn').prop('disabled', true);
            
        });
    });

    </script>

@stop

