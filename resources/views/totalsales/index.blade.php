@extends('layouts.master')
@include('layouts.breadcrumb')

@section('content')
<div class="main-content"> 
    @yield('breadcrumb')
    Add 1 END day date more to view actual result

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <!-- Form for date input -->
                <form action="{{ route('totalsales.index') }}" method="get" class="row g-3 justify-content-center">
                    @csrf
                    <div class="col-md-5">
                        <label for="start_date" class="form-label">Start Date:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label for="end_date" class="form-label">End Date:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">Calculate</button>
                    </div>
                </form>
            </div>
        </div>
        <br><br>
        @if(isset($finalResult))
        <div class="card mt-3 bg-light">
            <div class="card-body">
                <h3 class="card-title text-center mb-3">Total Sales for <span class="h2">{{ $startDate }}</span>  to  <span class="h2">{{ $endDate }} </span>:</h3>
                <p class="card-text text-center fs-4">Total Sales: <span class="h1 text-success">$ {{ $finalResult }} /-</span></p>
                <label class="my-3"><b>Amount in words: </b><span id="totalAmountWords" style="text-transform: capitalize;"></span></label>
            </div>
        </div>
        <!-- Define the convertNumberToWords function -->
        <script>
            // Assuming $finalResult is the numeric value
            var finalResult = {{ $finalResult }};

            // Update the text content of the span with the converted value
            document.getElementById("totalAmountWords").textContent = convertNumberToWords(finalResult);
        </script>
        @endif
    </div>
</div>
@stop
