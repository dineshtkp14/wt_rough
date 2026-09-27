@extends('layouts.master')

@section('content')
<div class="main-content" style="background:#f1f5fb;min-height:calc(100vh - 70px)">
    <div class="container-fluid p-3 p-md-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div><h1 class="fw-bold text-primary mb-1"><i class="fa fa-pen-to-square me-2"></i>Edit Opening Stock</h1><p class="text-muted mb-0">Update the opening stock entry for <strong>{{ $firm->name }}</strong>.</p></div>
            <a href="{{ route('vat-system.stock.opening.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i>Back to Opening Stock List</a>
        </div>
        @if($errors->any())<div class="alert alert-danger shadow-sm">{{ $errors->first() }}</div>@endif
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><h4 class="mb-0 fw-bold">Opening Stock Details</h4></div>
            <div class="card-body">
                <form method="post" action="{{ route('vat-system.stock.opening.update',$movement) }}" class="row g-3">
                    @csrf @method('PUT')
                    <div class="col-md-6"><label class="form-label fw-bold">Item Name *</label><input name="item_name" class="form-control" value="{{ old('item_name',$movement->stock?->item_name) }}" required></div>
                    <div class="col-md-3"><label class="form-label fw-bold">HSN Code</label><input name="hs_code" class="form-control" value="{{ old('hs_code',$movement->stock?->hs_code) }}"></div>
                    <div class="col-md-3"><label class="form-label fw-bold">Unit *</label><input name="unit" class="form-control" value="{{ old('unit',$movement->stock?->unit) }}" required></div>
                    <div class="col-md-3"><label class="form-label fw-bold">Opening Quantity *</label><input name="quantity" type="number" min=".001" step=".001" class="form-control" value="{{ old('quantity',$movement->quantity) }}" required></div>
                    <div class="col-md-3"><label class="form-label fw-bold">Purchase Rate</label><input name="purchase_rate" type="number" min="0" step=".01" class="form-control" value="{{ old('purchase_rate',$movement->stock?->purchase_rate) }}"></div>
                    <div class="col-md-3"><label class="form-label fw-bold">Sale Rate</label><input name="sale_rate" type="number" min="0" step=".01" class="form-control" value="{{ old('sale_rate',$movement->stock?->sale_rate) }}"></div>
                    <div class="col-md-3"><label class="form-label fw-bold">Reorder Level</label><input name="reorder_level" type="number" min="0" step=".001" class="form-control" value="{{ old('reorder_level',$movement->stock?->reorder_level) }}"></div>
                    <div class="col-12"><label class="form-label fw-bold">Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes',$movement->notes) }}</textarea></div>
                    <div class="col-12"><button class="btn btn-primary"><i class="fa fa-save me-1"></i>Update Opening Stock</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
