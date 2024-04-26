    @extends('layouts.master')
    @include('layouts.breadcrumb')

    @section('content')
    <div class="main-content">
        @yield('breadcrumb')

        <div class="container">
        

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card" style="border-width: 5px; border-color: #343a40;">
                        
                        @if (Session::has('counter_success'))
                        <div class="bg-success alert alert-success alert-dismissible fade show text-white" role="alert">
                            {{ Session::get('counter_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if (Session::has('counter_error'))
                        <div class="bg-danger alert alert-success alert-dismissible fade show text-white" role="alert">
                            {{ Session::get('counter_error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                
                        @if (Session::has('error'))
                        <div class="bg-danger alert alert-danger alert-dismissible fade show text-white" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="card-header h3 p-3">
                            <i class="fas fa-money-check-alt"></i> CHECK COUNTER DEPOSIT
                        </div>

                        <div class="card-body">
                            <form action="{{ route('CheckCounterDeposit.update') }}" method="post">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}">
                                    @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                

                                <button type="submit" class="btn btn-primary w-100" style="height: 50px;"><i class="fas fa-save"></i> Save</button>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card" style="border-width: 5px; border-color: #343a40;">

                        @if (Session::has('bank_success'))
                        <div class="bg-success alert alert-success alert-dismissible fade show text-white" role="alert">
                            {{ Session::get('bank_success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif
                
                        @if (Session::has('bank_error'))
                        <div class="bg-danger alert alert-danger alert-dismissible fade show text-white" role="alert">
                            {{ Session::get('bank_error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="card-header h3 p-3">
                            <i class="fas fa-university"></i> Check Bank Deposit
                        </div>

                        <div class="card-body">
                            <form action="{{ route('CheckBankDeposit.update') }}" method="post">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}">
                                    @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                

                                <button type="submit" class="btn btn-primary w-100" style="height: 50px;"><i class="fas fa-save"></i> Save</button>
                            </form>
                        </div>
                    </div>

                    
                </div>
            
            </div>
        </div>
    </div>
    @endsection
