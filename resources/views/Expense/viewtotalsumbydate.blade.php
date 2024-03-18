    @extends('layouts.master')
    @include('layouts.breadcrumb')

    @section('content')
    <div class="main-content">
        @yield('breadcrumb')

        <div class="container">
            <!-- Check if there are any validation errors -->
   <!-- Check if there are any validation errors -->
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        <!-- Loop through each error message and display it -->
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


            @if(isset($expenses))
                <!-- Display total sum -->
                <div class="mb-3">
                    <h4 class="fw-bold">Total Sum: {{ $totalSum }}</h4>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('expenses.search') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <a href="{{ route('expenses.index') }}" class="btn btn-success mt-5">
                <i class="fas fa-table"></i> View Expenses Table
            </a>
            
            @if(isset($expenses))
                <!-- Display search results -->
                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Particulars</th>
                            <th>Bill No</th>
                            <th>Amount</th>
                            <th>Notes</th>
                            <!-- Add more columns if needed -->
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $expense)
                            <tr>
                                <td>{{ $expense->date }}</td>
                                <td>{{ $expense->particulars }}</td>
                                <td>{{ $expense->billno }}</td>
                                <td>{{ $expense->amount }}</td>
                                <td>{{ $expense->notes }}</td>
                                <!-- Add more columns if needed -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $expenses->links() }} <!-- Pagination links -->
            @endif
        </div>
    </div>
    @stop
