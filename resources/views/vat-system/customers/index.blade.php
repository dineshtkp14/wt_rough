@extends('layouts.master')

@section('content')
<div class="main-content"><div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="fw-bold mb-1">VAT Customers</h1><p class="text-muted mb-0">Customers used only for VAT bills.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('vat-system.index') }}" class="btn btn-outline-secondary">Back</a><a href="{{ route('vat-system.customers.create') }}" class="btn btn-primary"><i class="fa fa-plus me-1"></i>Add Customer</a></div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <form class="mb-3" method="get"><div class="input-group"><input name="search" value="{{ $search }}" class="form-control" placeholder="Search by name, PAN or phone"><button class="btn btn-outline-primary">Search</button></div></form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-dark"><tr><th>#</th><th>Name</th><th>Address</th><th>PAN/VAT No.</th><th>Phone</th><th class="text-end">Actions</th></tr></thead><tbody>
        @forelse($customers as $customer)
            <tr><td>{{ $customers->firstItem() + $loop->index }}</td><td class="fw-bold">{{ $customer->name }}</td><td>{{ $customer->address ?: '-' }}</td><td>{{ $customer->pan_no ?: '-' }}</td><td>{{ $customer->phone ?: '-' }}</td><td class="text-end"><a href="{{ route('vat-system.customers.edit', $customer) }}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a> <form class="d-inline" method="post" action="{{ route('vat-system.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this VAT customer?');">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></form></td></tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-5">No VAT customers found.</td></tr>
        @endforelse
    </tbody></table></div><div class="p-3">{{ $customers->links() }}</div></div>
</div></div>
@endsection
