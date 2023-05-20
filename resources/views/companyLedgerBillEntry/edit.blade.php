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

 


<div class="container">
            @if (Session::has('success'))
                <div class="alert alert-success w-50">
                    {{ Session::get('success') }}
                </div>
            @endif
</div>

<div class="container">


<form class="row gx-5 gy-3" action="{{route('companybillentry.update',$com->id)}}" method="post">
    @csrf  
    @method('put')

               
           
           
                  <div class="py-4 d-flex justify-content-between align-items-center">
                   
                    <div style="width: 300px">
                      
                        <div class="input-group mb-1">
                            <span class="input-group-text">Date:</span>
                            <input type="date" class="form-control  @error('date') is-invalid @enderror" placeholder="" id="salesDate" class="form-control foritemsaledatecss" value="{{now()->format('Y-m-d')}}" name="date" >
                            @error('date')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="inputPassword4" class="form-label"> Particulars</label>
                    <input type="text" value="{{ old('particulars',$com->particulars) }}" class="form-control @error('particulars') is-invalid @enderror" 
                        name="particulars" value="{{ old('particulars') }}">
                    @error('particulars')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>
            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Bill No</label>
                    <input type="text" value="{{ old('voucher_type',$com->voucher_type)}}" class="form-control @error('vt') is-invalid @enderror" 
                        name="vt" >
                    @error('vt')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            

            <div class="col-md-6">
                    <label for="inputPassword4" class="form-label">Amount</label>
                    <input type="number" value="{{ old('debit',$com->debit) }}" class="form-control @error('amount') is-invalid @enderror" 
                        name="amount" >
                    @error('amount')
                        <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
            </div>

            <div class="col-md-6">
                <label for="inputPassword4" class="form-label">Notes</label>
                <textarea  class="form-control @error('notes') is-invalid @enderror" 
                    name="notes" value=""> {{ old('notes',$com->notes) }}</textarea>
                @error('notes')
                    <p class="invalid-feedback">{{ $message }}</p>
                @enderror
        </div>

           

           

            <div class="d-grid gap-2 pt-2 pb-4">
                    <button type="submit" class="btn btn-lg btn-primary">Save</button>
            </div>
</form>
</div>



</div>
@stop

