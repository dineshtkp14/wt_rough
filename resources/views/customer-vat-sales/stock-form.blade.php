@extends('layouts.master')

@section('content')
@php $isEditing = $stockItem !== null; @endphp
<div class="main-content"><div class="p-3 p-md-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h2 class="mb-1">{{ $isEditing ? 'Edit VAT Stock Item' : 'Add VAT Stock Item' }}</h2><p class="text-muted mb-0">This stock belongs only to the VAT system and never changes normal project stock.</p></div>
        <a href="{{ route('customer-vat-sales.stock', ['myfirm_id' => old('myfirm_id', $stockItem?->myfirm_id ?? request('myfirm_id'))]) }}" class="btn btn-outline-secondary">Back to VAT Stock</a>
    </div>

    @if($errors->any())<div class="alert alert-danger"><strong>Please correct these fields:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form method="post" action="{{ $isEditing ? route('customer-vat-sales.stock.update', $stockItem) : route('customer-vat-sales.stock.store') }}">
        @csrf @if($isEditing) @method('PUT') @endif
        <div class="card"><div class="card-header">Independent VAT Stock Information</div><div class="card-body"><div class="row g-3">
            <div class="col-md-6"><label class="form-label">Firm <span class="text-danger">*</span></label><select name="myfirm_id" class="form-select" required><option value="">Choose firm</option>@foreach($firms as $firm)<option value="{{ $firm->id }}" @selected(old('myfirm_id', $stockItem?->myfirm_id ?? request('myfirm_id')) == $firm->id)>{{ $firm->firm_name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Item Name <span class="text-danger">*</span></label><input name="item_name" value="{{ old('item_name', $stockItem?->item_name) }}" class="form-control" maxlength="255" required autofocus></div>
            <div class="col-md-3"><label class="form-label">Unit <span class="text-danger">*</span></label><input name="unit" value="{{ old('unit', $stockItem?->unit ?? 'pcs') }}" class="form-control" maxlength="30" required></div>
            <div class="col-md-3"><label class="form-label">Current VAT Stock <span class="text-danger">*</span></label><input type="number" name="quantity" value="{{ old('quantity', $stockItem?->quantity ?? 0) }}" class="form-control" min="0" step="0.001" required></div>
            <div class="col-md-3"><label class="form-label">Cost Price <span class="text-danger">*</span></label><input type="number" name="cost_price" value="{{ old('cost_price', $stockItem?->cost_price ?? 0) }}" class="form-control" min="0" step="0.01" required></div>
            <div class="col-md-3"><label class="form-label">VAT Sale Rate <span class="text-danger">*</span></label><input type="number" name="sale_rate" value="{{ old('sale_rate', $stockItem?->sale_rate ?? 0) }}" class="form-control" min="0" step="0.01" required></div>
            <div class="col-md-4"><label class="form-label">Low Stock Warning At <span class="text-danger">*</span></label><input type="number" name="warning_quantity" value="{{ old('warning_quantity', $stockItem?->warning_quantity ?? 0) }}" class="form-control" min="0" step="0.001" required></div>
            <div class="col-md-8"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="3" maxlength="2000">{{ old('notes', $stockItem?->notes) }}</textarea></div>
        </div></div><div class="card-footer d-flex justify-content-end gap-2"><a href="{{ route('customer-vat-sales.stock', ['myfirm_id' => old('myfirm_id', $stockItem?->myfirm_id ?? request('myfirm_id'))]) }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary"><i class="fa fa-floppy-disk me-1"></i>{{ $isEditing ? 'Update VAT Stock' : 'Save VAT Stock' }}</button></div></div>
    </form>
</div></div>
@endsection
