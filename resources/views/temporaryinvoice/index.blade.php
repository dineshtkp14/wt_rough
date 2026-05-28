@extends('layouts.master')
@include('layouts.breadcrumb')
@section('content')
    <div class="main-content">
        @yield('breadcrumb')

        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Temporary Invoices</h4>
                <a href="{{ route('temporaryinvoice.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> New Temporary Invoice
                </a>
            </div>

            <form class="card mb-3" method="get" action="{{ route('temporaryinvoice.index') }}">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" id="temporaryInvoiceLiveSearch"
                                placeholder="Customer name, contact, address, or invoice no"
                                value="{{ request('search') }}">
                            <small class="text-muted">Searches all temporary invoices.</small>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button class="btn btn-warning w-100" type="submit">
                                <i class="fa-solid fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('temporaryinvoice.index') }}" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-rotate-left"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th style="width: 180px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="temporaryInvoiceTableBody">
                                @include('temporaryinvoice._rows', ['temporaryInvoices' => $temporaryInvoices])
                            </tbody>
                        </table>
                    </div>

                    <div id="temporaryInvoicePagination">
                        {{ $temporaryInvoices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var input = document.getElementById('temporaryInvoiceLiveSearch');
            var form = input ? input.closest('form') : null;
            var tbody = document.getElementById('temporaryInvoiceTableBody');
            var pagination = document.getElementById('temporaryInvoicePagination');
            var timer = null;

            if (!input || !form || !tbody || !pagination) return;

            function search(page) {
                var params = new URLSearchParams(new FormData(form));
                if (page) {
                    params.set('page', page);
                }

                fetch("{{ route('temporaryinvoice.live-search') }}?" + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        tbody.innerHTML = data.html;
                        pagination.innerHTML = data.pagination;
                    });
            }

            input.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    search();
                }, 450);
            });

            form.querySelectorAll('input[type="date"]').forEach(function (dateInput) {
                dateInput.addEventListener('change', search);
            });

            pagination.addEventListener('click', function (event) {
                var link = event.target.closest('a');
                if (!link) return;

                event.preventDefault();
                var url = new URL(link.href);
                search(url.searchParams.get('page') || 1);
            });
        })();
    </script>
@stop
