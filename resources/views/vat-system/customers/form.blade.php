@extends('layouts.master')

@section('content')
<div class="main-content"><div class="container py-4" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="fw-bold mb-1">{{ $customer ? 'Edit VAT Customer' : 'Add VAT Customer' }}</h1><p class="text-muted mb-0">This customer list is separate from the regular customer module.</p></div><a href="{{ route('vat-system.customers.index') }}" class="btn btn-outline-secondary">Back</a></div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="post" action="{{ $customer ? route('vat-system.customers.update', $customer) : route('vat-system.customers.store') }}" class="card border-0 shadow-sm"><div class="card-body p-4">@csrf @if($customer) @method('PUT') @endif
        <div class="row g-3"><div class="col-md-6"><label class="form-label">Customer Name *</label><input name="name" value="{{ old('name', $customer?->name) }}" class="form-control" required></div><div class="col-md-6"><label class="form-label">PAN/VAT No.</label><input name="pan_no" value="{{ old('pan_no', $customer?->pan_no) }}" class="form-control"></div><div class="col-md-6"><label class="form-label">Address</label><input name="address" value="{{ old('address', $customer?->address) }}" class="form-control"></div><div class="col-md-6"><label class="form-label">Phone</label><input name="phone" value="{{ old('phone', $customer?->phone) }}" class="form-control"></div><div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email', $customer?->email) }}" class="form-control"></div><div class="col-12"><label class="form-label">Notes</label><textarea name="notes" rows="3" class="form-control">{{ old('notes', $customer?->notes) }}</textarea></div></div>
    </div><div class="card-footer bg-white border-0 p-4 text-end"><button class="btn btn-primary px-4"><i class="fa fa-floppy-disk me-1"></i>{{ $customer ? 'Update Customer' : 'Save Customer' }}</button></div></form>
</div></div>
@endsection
